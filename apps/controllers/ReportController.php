<?php
/**
* OpenFISMA
*
* MIT LICENSE
*
* @version $Id$
*/

require_once CONTROLLERS . DS . 'SecurityController.php';
require_once MODELS . DS . 'poam.php';

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
        $uid = $this->me->id;
        $qry = $sys->select();
        $ids = implode(',', $user->getMySystems($uid));
        $this->ids = $ids;
        $this->systems = $sys->getAdapter()
                             ->fetchPairs($qry->from($sys->info(Zend_Db_Table::NAME),
                                    array('id'=>'id','name'=>'name'))
                                    ->where("id IN ( $ids )")
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
        $uid = $this->me->id;
        //parse the params of search
        $criteria['system'] = $req->getParam('system','any');
        $criteria['source'] = $req->getParam('source','any');
        $criteria['type']   = $req->getParam('type','');
        $criteria['fy']     = $req->getParam('fy','');
        $criteria['status'] = $req->getParam('status','');
        $qry = $db->select();
        $source_list = $db->fetchPairs($qry->from($src->info(Zend_Db_Table::NAME),
                                       array('key'=>'nickname','value'=>'name'))
                                       ->order(array('key ASC')) );
        $qry->reset();
        $ids = implode(',',$user->getMySystems($uid));
        $system_list = $db->fetchPairs($qry->from($sys->info(Zend_Db_Table::NAME),
                                       array('key'=>'nickname','value'=>'nickname'))
                                       ->where("id IN ($ids)")
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
        $poam = new poam();
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
            $query->from(array('p'=>'poams'),array('findingnum'=>'p.id',
                                                   'ptype'=>'p.type',
                                                   'pstatus'=>'p.status',
                                                   'recommendation'=>'p.action_suggested',
                                                   'effectiveness'=>'p.cmeasure_effectiveness',
                                                   'correctiveaction'=>'p.action_planned',
                                                   'threatlevel'=>'p.threat_level',
                                                   'EstimatedCompletionDate'=>'p.action_date_est'));
            $query->join(array('sys'=>'systems'),'p.system_id = sys.id',
                                             array('system'=>'sys.nickname',
                                                   'tier'  =>'sys.tier',
                                                   'availability'=>'sys.availability',
                                                   'integrity'=>'sys.integrity',
                                                   'confidentiality'=>'sys.confidentiality'));
            $query->join(array('s'=>'sources'),'p.source_id = s.id',array('source'=>'s.nickname'));
            $query->join(array('a'=>'assets'),'a.id = p.asset_id',array('SD'=>'a.address_ip'));
            $query->join(array('net'=>'networks'),'net.id = a.network_id',
                                             array('location'=>'net.nickname'));
            if(!empty($system) && $system != 'any'){
                $query->where("sys.nickname = '$system'");
            }
            else {
                $query->where("sys.id in ($this->ids)");
            }
            if(!empty($source) && $source != 'any' ){
                $query->where("s.nickname = '$source'");
            }
            if(!empty($fy)){
                $begin_date = $fy. "-01-01";
                $end_date = $fy. "-12-31";
                $query->where("p.create_ts >= '$begin_date' and p.createe_ts <= '$end_date'");
            }
            if(!empty($type)){
                $query->where("p.type = '$type'");
            }
            if(!empty($status)){
                switch($status){
                    case '':
                        break;
                    case 'closed':
                        $query->where("p.status ='closed'");
                        break;
                    case 'open':
                        $query->where("p.status != 'closed'");
                        break;
                    case 'openOverdue':
                        $query->where("p.status = 'open'");
                        break;
                    case 'enOverdue':
                        $query->where("p.status = 'en'");
                        break;
                }
                if(!empty($overdue)){
                    switch($overdue){
                        case '':
                            break;
                        case '30':
                            $query->where("p.action_date_est >SUBDATE(NOW(),30) AND 
                                           p.create_ts <NOW()");
                            break;
                        case '60':
                            $query->where("p.action_date_est <SUBDATE(NOW(),30) AND 
                                           p.action_date_est >SUBDATE(NOW(),60)");
                            break;
                        case '90':
                            $query->where("p.action_date_est <SUBDATE(NOW(),60) AND 
                                           p.action_date_est >SUBDATE(NOW(),90)");
                            break;
                        case '120':
                            $query->where("p.action_date_est <SUBDATE(NOW(),90) AND
                                           p.action_date_est >SUBDATE(NOW(),120)");
                            break;
                        case 'greater':
                            $query->where("p.action_date_est < SUBDATE(NOW(),120)");
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
                $query = $db->select()->from(array('p'=>'poams'),array('n'=>'count(p.id)'))
                                      ->join(array('b'=>'blscrs'),'b.id = p.blscr_id',
                                             array('t'=>'b.code'))
                                      ->where("b.class = 'MANAGEMENT'")
                                      ->group("b.code");
                $result = $db->fetchAll($query);
                array_push($rpdata,$result);
                $query->reset();
                $query = $db->select()->from(array('p'=>'poams'),array('n'=>'count(p.id)'))
                                      ->join(array('b'=>'blscrs'),'b.id = p.blscr_id',
                                             array('t'=>'b.code'))
                                      ->where("b.class = 'OPERATIONAL'")
                                      ->group("b.code");
                $result = $db->fetchAll($query);
                array_push($rpdata,$result);
                $query->reset();
                $query = $db->select()->from(array('p'=>'poams'),array('n'=>'count(p.id)'))
                                      ->join(array('b'=>'blscrs'),'b.id = p.blscr_id',
                                             array('t'=>'b.code'))
                                      ->where("b.class = 'TECHNICAL'")
                                      ->group("b.code");
                $result = $db->fetchAll($query);
                array_push($rpdata,$result);
                break;
            case 2:
                //$query->reset();
                $query = $db->select()->from(array('s'=>'systems'),array('name'=>'s.name',
                                                                         'type'=>'s.type',
                                                                         'conf'=>'s.confidentiality',
                                                                         'integ'=>'s.availability',
                                                                         'avail'=>'s.availability'));
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
                $query = $db->select()->from(array('prod'=>'products'),array('Vendor'=>'prod.vendor',
                                                                       'Product'=>'prod.name',
                                                                       'Version'=>'prod.version',
                                                                       'NumoOV'=>'count(prod.id)'))
                  ->join(array('p'=>'poams'),'p.status IN ("OPEN","EN","UP","ES")',array())
                  ->join(array('a'=>'assets'),'a.id = p.asset_id AND a.prod_id = prod.id',array())
                  ->group("prod.vendor")
                  ->group("prod.name")
                  ->group("prod.version");
                $rpdata = $db->fetchAll($query);
                break;
            case 4:
                 $query = $db->select()->from(array('p'=>'products'),array('Vendor'=>'p.vendor',
                                                                           'Product'=>'p.name',
                                                                           'Version'=>'p.version'))
                                       ->join(array('a'=>'assets'),'a.source = "SCAN" AND a.prod_id = p.id',array());
                 $rpdata = $db->fetchAll($query);
                 break;
            case 5:
                 $rpdata = array();
                 $query = $db->select()->from(array('sys'=>'systems'),array('sysnick'=>'sys.nickname',
                                                                       'vulncount'=>'count(sys.id)'))
                                       ->join(array('p'=>'poams'),'p.type IN ("CAP","AR","ES") AND 
                                                    p.status IN ("OPEN","EN","EP","ES") AND p.system_id = sys.id',array())
                                       ->join(array('a'=>'assets'),'a.id = p.asset_id',array())
                                       ->group("p.system_id");
                 $sys_vulncounts = $db->fetchAll($query);

                  $query->reset();
                  $query = $db->select()->from(array('s'=>'systems'),
                                               array('system_nickname'=>'DISTINCT(s.nickname)'));
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
        $query = $db->select()->from(array('s'=>'systems'),array('sid'=>'id','sname'=>'name'));
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
            $query = $db->select()->from(array('p'=>'poams'),array('poam_id'=>'p.id'))                                    
                                  ->join(array('a'=>'assets'),'p.asset_id = a.id',array())
                                  ->where("p.threat_level != 'NONE'")
                                  ->where("p.cmeasure_effectiveness != 'NONE'")
                                  ->where("p.system_id = ".$system_id."");
            $poam_ids = $db->fetchAll($query);
            $num_poam_ids = count($poam_ids);
            $this->view->assign('poam_ids',$poam_ids);
            $this->view->assign('num_poam_ids',$num_poam_ids);
            $this->view->assign('system_id',$system_id);
        }
        $this->render();
    }

    public function fismaAction(){
        $req = $this->getRequest();
        $user = new User();
        $uid = $this->me->user_id;
        $ids = implode(',',$user->getMySystems($uid));
        $db = $user->getAdapter();
        $query = $db->select()->distinct()->from(array('s'=>'systems'),array('name'=>'s.nickname'))
                                          ->where("system_id in (".$ids.")");
        $systems = $db->fetchAll($query);
        foreach($systems as $k=>$v){
            $systems[$v['name']] = $v['name'];
            unset($systems[$k]);
        }
        array_unshift($systems,"select system");
        $this->view->assign('system_list',$systems);
        $criteria['system'] = $req->getParam('system','');
        $nowy = date('Y',time());
        $sy_list = array(''              => 'Select Fiscal Year',
                         ''.($nowy-3).'' => ''.($nowy-3).'',
                         ''.($nowy-2).'' => ''.($nowy-2).'',
                         ''.($nowy-1).'' => ''.($nowy-1).'',
                         ''.$nowy.''     => ''.$nowy.'',
                         ''.($nowy+1).'' => ''.($nowy+1).'');
        $this->view->assign('sy_list',$sy_list);
        $criteria['sy'] = $req->getParam('sy','');
        $sq_list = array(''=>'Select Fiscal Quarter','1'=>'1Q','2'=>'2Q','3'=>'3Q','4'=>'4Q');
        $criteria['sq'] = $req->getParam('sq','');
        $this->view->assign('sq_list',$sq_list);
        if('search' == $req->getParam('s')){
             $this->_helper->actionStack('fismasearch','Report',null,
                                            array('criteria' =>$criteria));
        }
        $this->view->assign('criteria',$criteria);
        $this->render();
    }

    public function fismasearchAction(){
        $poam = new poam();
        $req = $this->getRequest();
        $db = Zend_Registry::get('db');
        $dr = $req->getParam('dr');
        $sy = $req->getParam('sy');
        $sq = $req->getParam('sq');
        $startdate = $req->getParam('startdate');
        $enddate = $req->getParam('enddate');
        $system = $req->getParam('system');
        switch($dr){
            case 'y':
                $sy = $req->getParam('sy');
                $startdate = $sy."-01-01";
                $enddate = $sy."-12-31";
                break;
            case 'q':
                $sq = $req->getParam('sq');
                $sy = $req->getParam('sy');
                switch ($sq){
                    case 1:
                        $startdate = $sy."-01-01";
                        $enddate   = $sy."-03-31";
                        break;
                    case 2:
                        $startdate = $sy."-04-01";
                        $enddate   = $sy."-06-30";
                        break;
                    case 3:
                        $startdate = $sy."-07-01";
                        $enddate   = $sy."-09-30";
                        break;
                    case 4:
                        $startdate = $sy."10-01";
                        $enddate   = $sy."12-31";
                        break;
                }
                break;
            case 'c':
                $startdate = date('Y-m-d',strtotime($req->getParam('startdate')));
                $enddate   = date('Y-m-d',strtotime($req->getParam('enddate')));
                break;
        }

        $query = $db->select()->from(array('s'=>'systems'),array('id'=>'s.id'))
                              ->where("nickname = '".$system."'")
                              ->limit(1);
        $result = $db->fetchRow($query);
        if('' == $result){
            die("getFSASysID -no entry found in SYSTEMS for FSA");
        }
        $fsa_system_id = $result['id'];

        $query->reset();
        $query = $db->select()->from(array('sg'=>'system_groups'),array('id'=>'id'))
                              ->where("nickname = '".$system."'")
                              ->limit(1);
        $result = $db->fetchRow($query);
        if('' == $result){
            die("getFSASysGroupID -no entry found in SYSTEM_GROUPS for FSA");
        }
        $fsa_sysgroup_id = $result['id'];
        Zend_Registry::set('fsa_sysgroup_id', $fsa_sysgroup_id);
        Zend_Registry::set('fsa_system_id',   $fsa_system_id);
        Zend_Registry::set('startdate',       $startdate);
        Zend_Registry::set('enddate',         $enddate);
 
        $this->view->assign('AAW',$poam->fismasearch('aaw'));
        $this->view->assign('AS', $poam->fismasearch('as'));
        $this->view->assign('BAW',$poam->fismasearch('baw'));
        $this->view->assign('BS', $poam->fismasearch('bs'));
        $this->view->assign('CAW',$poam->fismasearch('caw'));
        $this->view->assign('CS', $poam->fismasearch('cs'));
        $this->view->assign('DAW',$poam->fismasearch('daw'));
        $this->view->assign('DS', $poam->fismasearch('ds'));
        $this->view->assign('EAW',$poam->fismasearch('eaw'));
        $this->view->assign('ES', $poam->fismasearch('es'));
        $this->view->assign('FAW',$poam->fismasearch('faw'));
        $this->view->assign('FS', $poam->fismasearch('fs'));
        $this->view->assign('dr', $dr );
        $this->render();
    }

}


