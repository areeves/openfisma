<?php
/**
 * @file PoamBaseController.php
 *
 * @description PoamBase Controller
 *
 * @author     Jim <jimc@reyosoft.com>
 * @copyright  (c) Endeavor Systems, Inc. 2008 (http://www.endeavorsystems.com)
 * @license    http://www.openfisma.org/mw/index.php?title=License
 * @version $Id$
*/

require_once MODELS . DS . 'poam.php';
require_once MODELS . DS . 'system.php';
require_once MODELS . DS . 'source.php';
require_once MODELS . DS . 'network.php';
require_once CONTROLLERS . DS . 'SecurityController.php';

class PoamBaseController extends SecurityController 
{
    protected $_system_list =null;
    protected $_source_list =null;
    protected $_network_list =null;
    protected $_poam =null;
    protected $_paging = array('mode'        =>'Sliding',
                             'append'      =>false,
                             'urlVar'      =>'p',
                             'path'        =>'',
                             'currentPage' =>1,
                             'perPage'     =>20);

    public function init()
    {
        parent::init();
        $this->_poam = new Poam();
        $src = new Source();
        $net = new Network();
        $sys = new System();
        $this->_source_list  = $src->getList('name');
        $this->_system_list = $sys->getList('name',$this->me->systems );
        $this->_network_list = $net->getList('name');
        asort($this->_source_list);
        asort($this->_system_list);
        asort($this->_network_list);
    }

    public function preDispatch()
    {
        parent::preDispatch();
        $this->_req = $this->getRequest();
        $req = $this->_req;
        $this->_paging_base_path = $req->getBaseUrl();
        $this->_paging['currentPage'] = $req->getParam('p',1);
    }

    public function makeUrl($criteria)
    {
        foreach($criteria as $key=>$value){
            if(!empty($value) ){
                if($value instanceof Zend_Date){
                    $this->_paging_base_path .='/'.$key.'/'.$value->toString('Ymd').'';
                }else{
                    $this->_paging_base_path .='/'.$key.'/'.$value.'';
                }
            }
        }
    }
}
