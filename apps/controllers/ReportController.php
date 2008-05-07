<?php
/**
* OpenFISMA
*
* MIT LICENSE
*
* @version $Id$
*/

require_once CONTROLLERS . DS . 'SecurityController.php';
require_once MODELS . DS . 'remediation.php';

class ReportController extends SecurityController
{
     private $systems = array();
     private $ids;
     public function preDispatch()
     {
        parent::preDispatch();
        require_once MODELS . DS . 'system.php';
        $sys = new System();
        $user = new User();
        $uid = $this->me->user_id;
        $qry = $sys->select();
        $ids = implode(',', $user->getMySystems($uid));
        $this->ids = $ids;
        $this->systems = $sys->getAdapter()
                             ->fetchPairs($qry->from($sys->info(Zend_Db_Table::NAME),
                                    array('id'=>'system_id','name'=>'system_name'))
                                    ->where("system_id IN ( $ids )")
                                    ->order('id ASC'));
     }

    public function searchboxAction(){
        require_once MODELS . DS . 'source.php';
        $req = $this->getRequest();
        $flag = $req->getParam('flag');
        $db = Zend_Registry::get('db');
        $user = new User();
        $src = new Source();
        $sys = new System();
        $uid = $this->me->user_id;
        //parse the params of search
        $criteria['system'] = $req->getParam('system','any');
        $criteria['source'] = $req->getParam('source','any');
        $criteria['type']   = $req->getParam('type','');
        $criteria['fy']     = $req->getParam('fy','');
        $criteria['status'] = $req->getParam('status','');
        $qry = $db->select();
        $source_list = $db->fetchPairs($qry->from($src->info(Zend_Db_Table::NAME),
                                       array('key'=>'source_nickname','value'=>'Source_name'))
                                       ->order(array('key ASC')) );
        $qry->reset();
        $ids = implode(',',$user->getMySystems($uid));
        $system_list = $db->fetchPairs($qry->from($sys->info(Zend_Db_Table::NAME),
                                       array('key'=>'system_nickname','value'=>'system_nickname'))
                                       ->where("system_id IN ($ids)")
                                       ->order(array('key ASC')) );
        $source_list['any'] = 'All source';
        $system_list['any'] = 'All system';
        $year_list = array(''     =>'All Fiscal Year',
                           '2005' =>'2005',
                           '2006' =>'2006',
                           '2007' =>'2007',
                           '2008' =>'2008',
                           '2009' =>'2009');
        $this->view->assign('source_list',$source_list);
        $this->view->assign('system_list',$system_list);
        $this->view->assign('year_list',$year_list);

        if('poam' == $flag){
            $status_list = array(''   =>'All Status',
                             'closed' =>'Closed',
                             'open'   =>'Open');
            $type_list = array(''     =>'All Type',
                               'cap'  =>'Cap',
                               'fp'   =>'FP',
                               'ar'   =>'AR');
            $this->view->assign('status_list',$status_list);
            $this->view->assign('type_list',$type_list);
            $this->view->assign('flag','poam');
        }
        if('overdue' == $flag){
            $criteria['status'] = $req->getParam('status','');
            $criteria['overdue'] = $req->getParam('overdue','');
            $status_list = array(''=>'All Status',
                                 'openOverdue'=>'SSO Approved Overdue',
                                 'enOverdue'=>'Cource Of Action Overdue');
            $overdue_list = array(''=>'Select Date Picker',
                                  '30'=>'0-29 days',
                                  '60'=>'30-59 days',
                                  '90'=>'60-89 days',
                                  '120'=>'90-119 days',
                                  'greater'=>'120 and greater days');
            $this->view->assign('status_list',$status_list);
            $this->view->assign('overdue_list',$overdue_list);
            $this->view->assign('flag','overdue');
            //$this->render('poam');
        }
        if('search' == $req->getParam('s')){
                $this->_helper->actionStack('search','Report',null,
                                            array('flag' =>$flag,
                                                  'criteria' =>$criteria));
        }
        $this->view->assign('criteria',$criteria);
        $this->render('poam');
    }

    public function searchAction(){
        require_once 'Zend/Session.php';
        require_once 'RiskAssessment.class.php';
        Zend_Session::start();
        $poam = new remediation();
        $req = $this->getRequest();
        $flag = $req->getParam('flag');
        $criteria = $req->getParam('criteria');
        $db = $poam->getAdapter();
        if('poam' == $flag || 'overdue' == $flag){
            $system = $req->get('system');
            $source = $req->get('source');
            $fy = $req->get('fy');
            $type = $req->get('type');
            $status = $req->get('status');
            $overdue = $req->get('overdue');
            $query = $poam->select()->setIntegrityCheck(false);
            $query->from(array('p'=>'POAMS'),array('findingnum'=>'p.poam_id',
                                                   'ptype'=>'p.poam_type',
                                                   'pstatus'=>'p.poam_status',
                                                   'recommendation'=>'p.poam_action_suggested',
                                                   'effectiveness'=>'p.poam_cmeasure_effectiveness',
                                                   'correctiveaction'=>'p.poam_action_planned',
                                                   'threatlevel'=>'p.poam_threat_level',
                                                   'EstimatedCompletionDate'=>'p.poam_action_date_est'));
            $query->join(array('sys_owner'=>'SYSTEMS'),'p.poam_action_owner = sys_owner.system_id',
                                             array('system'=>'sys_owner.system_nickname'));
            $query->join(array('fin'=>'FINDINGS'),'p.finding_id = fin.finding_id',
                                             array('finding'=>'fin.finding_data'));
            $query->join(array('fins'=>'FINDING_SOURCES'),'fin.source_id = fins.source_id',
                                             array('source'=>'fins.source_nickname'));
            $query->join(array('a'=>'ASSETS'),'a.asset_id = fin.asset_id',array());
            $query->join(array('sa'=>'SYSTEM_ASSETS'),'sa.asset_id = a.asset_id',array());
            $query->join(array('sys'=>'SYSTEMS'),'sys.system_id = sa.system_id',
                                             array('tier'=>'sys.system_tier',
                                                   'availability'=>'sys.system_availability',
                                                   'integrity'=>'sys.system_integrity',
                                                   'confidentiality'=>'sys.system_confidentiality'));
            $query->join(array('aadd'=>'ASSET_ADDRESSES'),'aadd.asset_id = a.asset_id',
                                             array('SD'=>'aadd.address_ip'));
            $query->join(array('net'=>'NETWORKS'),'net.network_id = aadd.network_id',
                                             array('location'=>'net.network_nickname'));
            $query->where("sa.system_is_owner = 1");
            if(!empty($system) && $system != 'any'){
                $query->where("sys_owner.system_nickname = '$system'");
            }
            else {
                $query->where("sys_owner.system_id in ($this->ids)");
            }
            if(!empty($source) && $source != 'any' ){
                $query->where("fins.source_nickname = '$source'");
            }
            if(!empty($fy)){
                $begin_date = $fy. "-01-01";
                $end_date = $fy. "-12-31";
                $query->where("p.poam_date_created >= '$begin_date' and p.poam_date_created <= '$end_date'");
            }
            if(!empty($type)){
                $query->where("p.poam_type = '$type'");
            }
            if(!empty($status)){
                switch($status){
                    case '':
                        break;
                    case 'closed':
                        $query->where("p.poam_status ='closed'");
                        break;
                    case 'open':
                        $query->where("p.poam_status != 'closed'");
                        break;
                    case 'openOverdue':
                        $query->where("p.poam_status = 'open'");
                        break;
                    case 'enOverdue':
                        $query->where("p.poam_status = 'en'");
                        break;
                }
                if(!empty($overdue)){
                    switch($overdue){
                        case '':
                            break;
                        case '30':
                            $query->where("p.poam_action_date_est >SUBDATE(NOW(),30) AND 
                                           p.poam_date_created <NOW()");
                            break;
                        case '60':
                            $query->where("p.poam_action_date_est <SUBDATE(NOW(),30) AND 
                                           p.poam_action_date_est >SUBDATE(NOW(),60)");
                            break;
                        case '90':
                            $query->where("p.poam_action_date_est <SUBDATE(NOW(),60) AND 
                                           p.poam_action_date_est >SUBDATE(NOW(),90)");
                            break;
                        case '120':
                            $query->where("p.poam_action_date_est <SUBDATE(NOW(),90) AND
                                           p.poam_action_date_est >SUBDATE(NOW(),120)");
                            break;
                        case 'greater':
                            $query->where("p.poam_action_date_est < SUBDATE(NOW(),120)");
                            break;
                    }
                                          
                }
            }
            $poams = $poam->fetchAll($query)->toArray();
            if($poams){
                foreach($poams as &$poam_record) {
                    if(($poam_record['pstatus'] =='EN') && ($poam_record['EstimatedCompletionDate'] < date('Y-m-d'))){
                        $poam_record['pstatus'] = 'EO';
                    }
                    $conf = $poam_record['confidentiality'];
                    $avail = $poam_record['availability'];
                    $integ = $poam_record['integrity'];
                    $crit  = $avail;
                    $threat = $poam_record['threatlevel'];
                    $effect = $poam_record['effectiveness'];
                    if((strtolower($threat) == 'none') || (strtolower($effect) == 'none')){
                        $poam_record['risklevel'] = 'n/a';
                    }
                    else {
                        $assess_obj = new RiskAssessment($conf, $avail, $integ, $crit, $threat, $effect);
                        $poam_record['risklevel'] = $assess_obj->get_overall_risk();
                    }
                    //Replace each blank field with placeholder text
                    foreach (array_keys($poam_record) as $column_name){
                        if(strlen($poam_record[$column_name]) < 1){
                            $poam_record[$column_name] = 'n/a';
                        }
                    }
                }
            }
            $_SESSION['rpdata'] = $poams;
            $_SESSION['POAMT']  = array($fy,$system,$source);
            $this->view->assign('rpdata',$poams);
            $this->render('poamsearch');
        }
    }

    public function generalAction(){
        $req = $this->getRequest();
        $type_list = array('' =>'Please Select Report',
                           '1'=>'NIST Baseline Security Controls Report',
                           '2'=>'FIPS 199 Categorization Breakdown',
                           '3'=>'Products with Open Vulnerabilities',
                           '4'=>'Software Discovered Through Vulnerability Assessments',
                           '5'=>'Total # of Systems /w Open Vulnerabilities');
        $criteria['type'] = $req->getParam('type','');
        if('search' == $req->getParam('s')){
            $type = $req->getParam('type');
            $this->view->assign('type',$type);
            $this->_helper->actionStack('generalsearch','Report',null,array('criteria' =>$criteria));
        }
        $this->view->assign('criteria',$criteria); 
        $this->view->assign('type_list',$type_list);
        $this->render();
    }

    public function generalsearchAction(){
        require_once 'RiskAssessment.class.php';
        $req = $this->getRequest();
        $type = $req->getParam('type');
        $db = Zend_Registry::get('db');
        switch($type){
            case 1:
                $rpdata = array();
                $query = $db->select()->from(array('p'=>'POAMS'),array('n'=>'count(p.poam_id)'))
                                      ->join(array('b'=>'BLSCR'),'b.blscr_number = p.poam_blscr',
                                             array('t'=>'b.blscr_number'))
                                      ->where("b.blscr_class = 'MANAGEMENT'")
                                      ->group("b.blscr_number");
                $result = $db->fetchAll($query);
                array_push($rpdata,$result);
                $query->reset();
                $query = $db->select()->from(array('p'=>'POAMS'),array('n'=>'count(p.poam_id)'))
                                      ->join(array('b'=>'BLSCR'),'b.blscr_number = p.poam_blscr',
                                             array('t'=>'b.blscr_number'))
                                      ->where("b.blscr_class = 'OPERATIONAL'")
                                      ->group("b.blscr_number");
                $result = $db->fetchAll($query);
                array_push($rpdata,$result);
                $query->reset();
                $query = $db->select()->from(array('p'=>'POAMS'),array('n'=>'count(p.poam_id)'))
                                      ->join(array('b'=>'BLSCR'),'b.blscr_number = p.poam_blscr',
                                             array('t'=>'b.blscr_number'))
                                      ->where("b.blscr_class = 'TECHNICAL'")
                                      ->group("b.blscr_number");
                $result = $db->fetchAll($query);
                array_push($rpdata,$result);
                break;
            case 2:
                //$query->reset();
                $query = $db->select()->from(array('s'=>'SYSTEMS'),array('name'=>'s.system_name',
                                                                         'type'=>'s.system_type',
                                                                         'conf'=>'s.system_confidentiality',
                                                                         'integ'=>'s.system_availability',
                                                                         'avail'=>'s.system_availability'));
                                                                         //'last_upd'=>'n/a'
                $systems = $db->fetchAll($query);
                $fips_totals = array();
                $fips_totals['LOW'] = 0;
                $fips_totals['MODERATE'] = 0;
                $fips_totals['HIGH']     = 0;
                $fips_totals['n/a'] = 0;
                foreach($systems as &$system){
                    if(strtolower($system['conf']) != 'none'){
                        $risk_obj = new RiskAssessment($system['conf'],$system['avail'],$system['integ'],null,null,null);
                        $fips199 = $risk_obj->get_data_sensitivity();
                    }
                    else {
                        $fips199 = 'n/a';
                    }
                    $system['fips'] = $fips199;
                    $fips_totals[$fips199] += 1;
                    $system['crit'] = $system['avail'];
                }
                $rpdata = array();
                $rpdata[] = $systems;
                $rpdata[] = $fips_totals;
                break;
            case 3:
                $query = $db->select()->from(array('prod'=>'PRODUCTS'),array('Vendor'=>'prod.prod_vendor',
                                                                       'Product'=>'prod.prod_name',
                                                                       'Version'=>'prod.prod_version',
                                                                       'NumoOV'=>'count(prod.prod_id)'))
                  ->join(array('p'=>'POAMS'),'p.poam_status IN ("OPEN","EN","UP","ES")',array())
                  ->join(array('f'=>'FINDINGS'),'p.finding_id = f.finding_id',array())
                  ->join(array('a'=>'ASSETS'),'a.asset_id = f.asset_id AND a.prod_id = prod.prod_id',array())
                  ->group("prod.prod_vendor")
                  ->group("prod.prod_name")
                  ->group("prod.prod_version");
                $rpdata = $db->fetchAll($query);
                break;
            case 4:
                 $query = $db->select()->from(array('p'=>'PRODUCTS'),array('Vendor'=>'p.prod_vendor',
                                                                           'Product'=>'p.prod_name',
                                                                           'Version'=>'p.prod_version'))
                     ->join(array('a'=>'ASSETS'),'a.asset_source = "SCAN" AND a.prod_id = p.prod_id',array());                  $rpdata = $db->fetchAll($query);
                 break;
            case 5:
                 $rpdata = array();
                 $query = $db->select()->from(array('sys'=>'SYSTEMS'),array('sysnick'=>'sys.system_nickname',
                                                                       'vulncount'=>'count(sys.system_id)'))
                                       ->join(array('p'=>'POAMS'),'p.poam_type IN ("CAP","AR","ES") AND 
                                                    p.poam_status IN ("OPEN","EN","EP","ES")',array())
                                       ->join(array('f'=>'FINDINGS'),'f.finding_id = p.finding_id',array())
                                       ->join(array('a'=>'ASSETS'),'a.asset_id = f.asset_id',array())
                                       ->join(array('sa'=>'SYSTEM_ASSETS'),'sa.asset_id = a.asset_id AND 
                                                    sa.system_id = sys.system_id',array())
                                       ->group("sa.system_id");
                  $sys_vulncounts = $db->fetchAll($query);

                  $query->reset();
                  $query = $db->select()->from(array('s'=>'SYSTEMS'),
                                               array('system_nickname'=>'DISTINCT(s.system_nickname)'));
                  $systems = $db->fetchAll($query);
                  $system_totals = array();
                  foreach($systems as $system_row){
                      $system_nick = $system_row['system_nickname'];
                      $system_totals[$system_nick] = 0;
                  }

                  $total_open = 0;
                  foreach((array)$sys_vulncounts as $sv_row){
                      $system_nick = $sv_row['sysnick'];
                      $system_totals[$system_nick] = $sv_row['vulncount'];
                      $total_open++;
                  }

                  $system_total_array = array();
                  foreach(array_keys($system_totals) as $key){
                      $val = $system_totals[$key];
                      $this_row = array();
                      $this_row['nick'] = $key;
                      $this_row['num'] = $val;
                      array_push($system_total_array,$this_row);
                  }
                  array_push($rpdata,$total_open);
                  array_push($rpdata,$system_total_array);
                  break;
        }
        $colnum = 10;
        $this->view->assign('colnum',$colnum);
        $this->view->assign('colwidth',floor(100/($colnum+1)));
        $this->view->assign('rpdata',$rpdata);
        $_SESSION['rpdata'] = $rpdata;
        $this->render('generalsearch_.'.$type.'');
    }

    public function systemrafsAction(){
        $req = $this->getRequest();
        $db = Zend_Registry::get('db');
        $query = $db->select()->from(array('s'=>'SYSTEMS'),array('sid'=>'system_id','sname'=>'system_name'));
        $data = $db->fetchAll($query);
        foreach($data as $result){
            $system_list[$result['sid']] = $result['sname'];
        }
        $this->view->assign('system_list',$system_list);
        $criteria['system_id'] = $req->getParam('system_id','');
        $system_id = $req->getParam('system_id');
        $this->view->assign('criteria',$criteria);
        if(!empty($system_id)){
            $query->reset();
            $query = $db->select()->from(array('p'=>'POAMS'),array('poam_id'=>'p.poam_id'))
                                  ->join(array('f'=>'FINDINGS'),'p.finding_id = f.finding_id',array())
                                  ->join(array('sa'=>'SYSTEM_ASSETS'),'f.asset_id = sa.asset_id',array())
                                  ->where("p.poam_threat_level != 'NONE'")
                                  ->where("p.poam_cmeasure_effectiveness != 'NONE'")
                                  ->where("sa.system_id = ".$system_id."");
            $poam_ids = $db->fetchAll($query);
            $num_poam_ids = count($poam_ids);
            $this->view->assign('poam_ids',$poam_ids);
            $this->view->assign('num_poam_ids',$num_poam_ids);
            $this->view->assign('system_id',$system_id);
        }
        $this->render();
    }

}


