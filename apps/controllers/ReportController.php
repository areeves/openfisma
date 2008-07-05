<?php
/**
 * @file ReportController.php
 *
 * @description Report Controller
 *
 * @author     Ryan<ryan.yang@reyosoft.com>
 * @copyright  (c) Endeavor Systems, Inc. 2008 (http://www.endeavorsystems.com)
 * @license    http://www.openfisma.org/mw/index.php?title=License
 * @version $Id$
*/

require_once CONTROLLERS . DS . 'PoamBaseController.php';
require_once 'Pager.php';

class ReportController extends PoamBaseController
{
    public function preDispatch()
    {
       parent::preDispatch();
       $this->req = $this->getRequest();
       $this->_helper->contextSwitch()
             ->addContext('pdf',array('suffix'=>'pdf',
                                      'headers'=>array('Content-Type'=>'application/pdf',
                                                'Content-Disposition'=>'attachement;filename:"export.pdf"')) )
             ->addContext('xls',array('suffix'=>'xls') )
             ->addActionContext('poam', array('pdf','xls') )
             ->addActionContext('fisma', array('pdf','xls') )
             ->addActionContext('overdue', array('pdf','xls') )
             ->initContext();

    }

    public function fismaAction()
    {
        $req = $this->getRequest();
        $criteria['year']      = $req->getParam('y');
        $criteria['quarter']   = $req->getParam('q');
        $criteria['system_id'] = $system_id = $req->getParam('system');
        $criteria['startdate'] = $req->getParam('startdate');
        $criteria['enddate']   = $req->getParam('enddate');
        $this->view->assign('system_list', $this->_system_list);
        $this->view->assign('criteria',$criteria);
        $this->render();
        if('search' == $req->getParam('s')            
            || 'pdf' == $req->getParam('format')
            || 'xls' == $req->getParam('format')){
            if(!empty($criteria['startdate']) && !empty($criteria['enddate'])){
                $date_begin = new Zend_Date($criteria['startdate'],Zend_Date::DATES);
                $date_end   = new Zend_Date($criteria['enddate'],Zend_Date::DATES);
            }
            if(!empty($criteria['year'])){
                if(!empty($criteria['quarter'])){
                    switch($criteria['quarter']){
                        case 1:
                            $startdate = $criteria['year'].'-01-01';
                            $enddate   = $criteria['year'].'-03-31';
                            break;
                        case 2:
                            $startdate = $criteria['year'].'-04-01';
                            $enddate   = $criteria['year'].'-06-30';
                            break;
                        case 3:
                            $startdate = $criteria['year'].'-07-01';
                            $enddate   = $criteria['year'].'-09-30';
                            break;
                        case 4:
                            $startdate = $criteria['year'].'-10-01';
                            $enddate   = $criteria['year'].'-12-31';
                            break;
                    } 
                }else{
                    $startdate = $criteria['year'].'-01-01';
                    $enddate   = $criteria['year'].'-12-31';
                }
                $date_begin = new Zend_Date($startdate,Zend_Date::DATES);
                $date_end   = new Zend_Date($enddate,Zend_Date::DATES);
            }
            $system_array = array('system_id'=>$system_id);
            $aaw_array    = array('created_date_end'=>$date_begin,
                                  'closed_date_begin'=>$date_end);//or close_ts is null
            $baw_array    = array('created_date_end'=>$date_end,
                                  'est_date_end'=>$date_end,
                                  'actual_date_begin'=>$date_begin,
                                  'action_date_end'=>$date_end);
            $caw_array    = array('created_date_end'=>$date_end,
                                  'est_date_begin'=>$date_end);// and actual_date_begin is null
            $daw_array    = array('est_date_end'=>$date_end,
                                  'actual_date_begin'=>$date_end);//or action_actual_date is null
            $eaw_array    = array('created_date_begin'=>$date_begin,
                                  'created_date_end'=>$date_end);
            $faw_array    = array('created_date_end'=>$date_end,
                                  'closed_date_begin'=>$date_end);//or close_ts is null
            
            $criteria_aaw = array_merge($system_array,$aaw_array);
            $criteria_baw = array_merge($system_array,$baw_array);
            $criteria_caw = array_merge($system_array,$caw_array);
            $criteria_daw = array_merge($system_array,$daw_array);
            $criteria_eaw = array_merge($system_array,$eaw_array);
            $criteria_faw = array_merge($system_array,$faw_array);
            $this->view->assign('AAW',$this->_poam->search($this->me->systems,array('count'=>'count(*)'),$criteria_aaw));
            $this->view->assign('BAW',$this->_poam->search($this->me->systems,array('count'=>'count(*)'),$criteria_baw));
            $this->view->assign('CAW',$this->_poam->search($this->me->systems,array('count'=>'count(*)'),$criteria_caw));
            $this->view->assign('DAW',$this->_poam->search($this->me->systems,array('count'=>'count(*)'),$criteria_daw));
            $this->view->assign('EAW',$this->_poam->search($this->me->systems,array('count'=>'count(*)'),$criteria_eaw));
            $this->view->assign('FAW',$this->_poam->search($this->me->systems,array('count'=>'count(*)'),$criteria_faw));
            
            $url='/zfentry.php/panel/report/sub/fisma/s/search';

            if(isset($criteria['system_id']))
            {
                $url.='/system/'.$criteria['system_id'];
            }
            
            if(isset( $criteria['startdate']))
            {
                $url.='/startdate/'. $criteria['startdate'];
            }
            if(isset($criteria['enddate']))
            {
                $url.='/enddate/'.$criteria['enddate'];
            }
            
            $this->view->url=$url;
            $this->render('fismasearch');
        }
            
    }

    public function poamAction()
    {
        $req = $this->getRequest();
        $params = array( 'system_id'=>'system_id',
                         'source_id'=>'source_id',
                         'type'     =>'type',
                         'year'     =>'year',
                         'status'   =>'status');
        $criteria = $this->retrieveParam($req, $params); 
        $this->view->assign('source_list',$this->_source_list);
        $this->view->assign('system_list',$this->_system_list);
        $this->view->assign('network_list',$this->_network_list);
        $this->view->assign('criteria',$criteria);
        if('search' == $req->getParam('s') 
            || 'pdf' == $req->getParam('format')
            || 'xls' == $req->getParam('format')){
            $this->_paging_base_path .= '/panel/report/sub/poam/s/search';
            $this->makeUrl($criteria);
            if(!empty($criteria['year'])){
                $criteria['created_date_begin'] = new Zend_Date($criteria['year'],Zend_Date::YEAR);
                $criteria['created_date_end']   = clone $criteria['created_date_begin'];
                $criteria['created_date_end']->add(1,Zend_Date::YEAR);   
                unset($criteria['year']);
            }
            $list = &$this->_poam->search($this->me->systems, array('id',
                                                         'finding_data',
                                                         'system_id',
                                                         'network_id',
                                                         'source_id',
                                                         'asset_id',
                                                         'type',
                                                         'ip',
                                                         'port',
                                                         'status',
                                                         'action_suggested',
                                                         'action_planned',
                                                         'threat_level',
                                                         'action_est_date',
                                                         'count'=>'count(*)') ,$criteria,
                                        $this->_paging['currentPage'],
                                        $this->_paging['perPage']);
            $total = array_pop($list); 
            $this->_paging['totalItems'] = $total;
            $this->_paging['fileName'] = "{$this->_paging_base_path}/p/%d";
            $pager = &Pager::factory($this->_paging);
            $this->view->assign('poam_list', $list);
            $this->view->assign('links', $pager->getLinks());
        }
        $this->render();
    }

    public function overdueAction()
    {
        $req = $this->getRequest();
        $params = array( 'system_id'=>'system_id',
                         'source_id'=>'source_id',
                         'overdue'     =>'overdue',  //array(type=>x,day=>x);
                         'year'     =>'year');
        $criteria = $this->retrieveParam($req, $params); 

        $this->view->assign('source_list',$this->_source_list);
        $this->view->assign('system_list',$this->_system_list);
        $this->view->assign('criteria',$criteria);
        if('search' == $req->getParam('s') || 'pdf' == $req->getParam('format')){
            $this->_paging_base_path .= '/panel/report/sub/overdue/s/search';
            $this->makeUrl($criteria);

            if(!empty($criteria['year'])){
                $criteria['created_date_begin'] = new Zend_Date($criteria['year'],Zend_Date::YEAR);
                $criteria['created_date_end']   = clone $criteria['created_date_begin'];
                $criteria['created_date_end']->add(1,Zend_Date::YEAR);   
                unset($criteria['year']);
            }

            if(!empty($criteria['overdue'])){
                $date = clone self::$now;
                $date->sub(($criteria['overdue']['day']-1)*30,Zend_Date::DAY);
                $criteria['overdue']['begin_date'] = clone $date;
                $date->sub(30,Zend_Date::DAY);
                $criteria['overdue']['end_date'] = $date;
                if( $criteria['overdue']['day']==5 ) { ///@todo hardcode greater than 120
                    unset($criteria['overdue']['begin_date'] );
                }
            }
            $list = &$this->_poam->search($this->me->systems, array('id',
                                                         'finding_data',
                                                         'system_id',
                                                         'network_id',
                                                         'source_id',
                                                         'asset_id',
                                                         'type',
                                                         'ip',
                                                         'port',
                                                         'status',
                                                         'action_suggested',
                                                         'action_planned',
                                                         'threat_level',
                                                         'action_est_date',
                                                         'count'=>'count(*)') ,$criteria,
                                        $this->_paging['currentPage'],
                                        $this->_paging['perPage']);
            $total = array_pop($list); 
            $this->_paging['totalItems'] = $total;
            $this->_paging['fileName'] = "{$this->_paging_base_path}/p/%d";
            $pager = &Pager::factory($this->_paging);
            $this->view->assign('poam_list', $list);
            $this->view->assign('links', $pager->getLinks());
        }
        $this->render();
    }

    public function generalAction()
    {
        require_once CONTROLLERS . DS . 'RiskAssessment.class.php';
        $req = $this->getRequest();
        $type = $req->getParam('type','');
        $this->view->assign('type',$type);
        $this->render();
        if('search' == $req->getParam('s') && !empty($type) || 'pdf'==$req->getParam('format')){
            $db = $this->_poam->getAdapter();
            $system = new system();
            switch($type){
                case 1:
                    $rpdata = array();
                    $query = $db->select()->from(array('p'=>'poams'),array('num'=>'count(p.id)'))
                                ->join(array('b'=>'blscrs'),'b.code = p.blscr_id',array('blscr'=>'b.code'))
                                ->where("b.class = 'MANAGEMENT'")
                                ->group("b.code");
                    $rpdata[] = $db->fetchAll($query);
                    $query->reset();
                    $query = $db->select()->from(array('p'=>'poams'),array('num'=>'count(p.id)'))
                                ->join(array('b'=>'blscrs'),'b.code = p.blscr_id',array('blscr'=>'b.code'))
                                ->where("b.class = 'OPERATIONAL'")
                                ->group("b.code");
                    $rpdata[] = $db->fetchAll($query);
                    $query->reset();
                    $query = $db->select()->from(array('p'=>'poams'),array('num'=>'count(p.id)'))
                                ->join(array('b'=>'blscrs'),'b.code = p.blscr_id',array('blscr'=>'b.code'))
                                ->where("b.class = 'TECHNICAL'")
                                ->group("b.code");
                    $rpdata[] = $db->fetchAll($query);
                    break;
                case 2:
                    $systems = $system->getList(array('name'=>'name','type'=>'type','conf'=>'confidentiality',
                                                      'avail'=>'availability','integ'=>'availability'));
                    $fips_totals = array();
                    $fips_totals['LOW'] = 0;
                    $fips_totals['MODERATE'] = 0;
                    $fips_totals['HIGH']     = 0;
                    $fips_totals['n/a'] = 0;
                    foreach($systems as &$system){
                        if(strtolower($system['conf']) != 'none'){
                            $risk_obj = new RiskAssessment($system['conf'],$system['avail'],$system['integ'],null,null,null);
                            $fips199 = $risk_obj->get_data_sensitivity();
                        }else{
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
                    $query = $db->select()->from(array('prod'=>'products'),
                                                 array('Vendor'=>'prod.vendor','Product'=>'prod.name',
                                                       'Version'=>'prod.version','NumoOV'=>'count(prod.id)'))
                                ->join(array('p'=>'poams'),'p.status IN ("OPEN","EN","UP","ES")',array())
                                ->join(array('a'=>'assets'),'a.id = p.asset_id AND a.prod_id = prod.id',array())
                                ->group("prod.vendor")
                                ->group("prod.name")
                                ->group("prod.version");
                    $rpdata = $db->fetchAll($query);
                    break;
                case 4:
                    $query = $db->select()->from(array('p'=>'products'),
                                                 array('Vendor'=>'p.vendor','Product'=>'p.name',
                                                       'Version'=>'p.version'))
                                ->join(array('a'=>'assets'),'a.source = "SCAN" AND a.prod_id = p.id',array());
                    $rpdata = $db->fetchAll($query);
                    break;
                case 5:
                    $rpdata = array();
                    $query = $db->select()->from(array('sys'=>'systems'),array('sysnick'=>'sys.nickname',
                                                                               'vulncount'=>'count(sys.id)'))
                                ->join(array('p'=>'poams'),'p.type IN ("CAP","AR","FP") AND
                                       p.status IN ("OPEN","EN","EP","ES") AND p.system_id = sys.id',array())
                                ->join(array('a'=>'assets'),'a.id = p.asset_id',array())
                                ->group("p.system_id");
                    $sys_vulncounts = $db->fetchAll($query);
                    $systems = $system->getList(array('system_nickname'=>'nickname'));
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
            $this->view->assign('rpdata',$rpdata);
            $this->render('generalsearch-'.$type);
        }
    }

    public function rafsAction()
    {
        $sid = $this->_req->getParam('system_id');
        $this->view->assign('system_list',$this->_system_list);
        if( !empty($sid) ) {
            $this->_helper->layout->setLayout('rafs');
            $query = $this->_poam->select()->from($this->_poam,array('id') )
                           ->where('system_id=?',$sid)
                           ->where('threat_level IS NOT NULL AND threat_level != \'NONE\'')
                           ->where('cmeasure_effectiveness IS NOT NULL AND 
                                    cmeasure_effectiveness != \'NONE\'');
            $poam_ids = $this->_poam->getAdapter()->fetchCol($query);
            /*
            $count = count($poam_ids);
            if( $count > 0 ) {
                $fname = tempnam(OVMS_WEB_TEMP, "RAF");
                @unlink($fname);
                if(class_exists('ZipArchive')) {
                    $fname .= '.zip';
                    $flag = 'zip';
                    $zip = new ZipArchive;
                    $ret = $zip->open($fname, ZIPARCHIVE::CREATE);
                    if(!($ret === TRUE) ) {
                        throw new fisma_Exception('Cannot create file '. $fname);
                    }
                }else{
                    throw new fisma_Exception('ZipAchive required to use this function');
                    $flag = 'tgz';
                    $files = array();
                }
            }
            */
            $this->view->assign('source_list',$this->_source_list);
            foreach( $poam_ids as $id ) {
                $poam_detail = &$this->_poam->getDetail($id);
                $this->view->assign('poam',$poam_detail);
                $this->render('remediation/raf',null,true);
            }
        }else{
            $this->render();
        }
    }

}
