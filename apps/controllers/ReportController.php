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
    }

    public function fismaAction()
    {
        $this->view->assign('system_list', $this->_system_list);
        $this->render();
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
        $this->render();
        if('search' == $req->getParam('s')){
            if(!empty($criteria['year'])){
                $criteria['created_date_begin'] = new Zend_Date($criteria['year'],Zend_Date::YEAR);
                $criteria['created_date_end']   = clone $criteria['created_date_begin'];
                $criteria['created_date_end']->add(1,Zend_Date::YEAR);   
                unset($criteria['year']);
            }
            $list = $this->_poam->search($this->me->systems, array('id',
                                                         'finding_data',
                                                         'system_id',
                                                         'network_id',
                                                         'source_id',
                                                         'action_planned',
                                                         'type',
                                                         'ip',
                                                         'port',
                                                         'status',
                                                         'action_suggested',
                                                         'cmeasure_effectiveness',
                                                         'action_planned',
                                                         'threat_level',
                                                         'action_est_date') ,$criteria);
            $this->view->assign('poam_list', $list);
            $this->render('poamsearch');
        }
    }
}
