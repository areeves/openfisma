<?php
/**
* OpenFISMA
*
* MIT LICENSE
*
* @version $Id$
*/

require_once CONTROLLERS . DS . 'PoamBaseController.php';

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
             ->addActionContext('poam', 'pdf')
             ->initContext();

    }

    public function fismaAction()
    {
        $req = $this->getRequest();
        $criteria['year']      = $req->get('y');
        $criteria['quarter']   = $req->get('q');
        $criteria['system_id'] = $system_id = $req->get('system');
        $criteria['startdate'] = $req->get('startdate');
        $criteria['enddate']   = $req->get('enddate');
        
        $this->view->assign('system_list', $this->_system_list);
        $this->view->assign('criteria',$criteria);
        $this->render();
        if('search' == $req->getParam('s')){
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
            $this->render('fismasearch');
        }
    }

    public function poamAction()
    {
        $req = $this->getRequest();
        $uid = $this->me->id;
        $criteria['system_id'] = $req->getParam('system_id');
        $criteria['source_id'] = $req->getParam('source_id');
        $criteria['type']   = $req->getParam('type');
        $criteria['year']     = $req->getParam('year');
        $criteria['status'] = $req->getParam('status');

        $this->view->assign('source_list',$this->_source_list);
        $this->view->assign('system_list',$this->_system_list);
        $this->view->assign('network_list',$this->_network_list);
        $this->view->assign('criteria',$criteria);
        if('search' == $req->getParam('s') || 'pdf' == $req->getParam('format')){
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
                                                         'action_est_date') ,$criteria);
            $this->view->assign('poam_list', $list);
        }
        $this->render();
    }
}
