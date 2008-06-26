<?php
/**
 * @file SourceController.php
 *
 * Source Controller
 *
 * @author     Ryan <ryan.yang@reyosoft.com>
 * @copyright  (c) Endeavor Systems, Inc. 2008 (http://www.endeavorsystems.com)
 * @license    http://www.openfisma.org/mw/index.php?title=License
 * @version $Id$
*/

require_once CONTROLLERS . DS . 'SecurityController.php';
require_once MODELS . DS . 'source.php';
require_once 'Pager.php';

class SourceController extends SecurityController
{
    private $_paging = array(
            'mode'        =>'Sliding',
            'append'      =>false,
            'urlVar'      =>'p',
            'path'        =>'',
            'currentPage' => 1,
            'perPage'=>20);
    
    public function init()
    {
        parent::init();
        $this->_source = new Source();
    }

    public function preDispatch()
    {
        $req = $this->getRequest();
        $this->_paging_base_path = $req->getBaseUrl() .'/panel/source/sub/list';
        $this->_paging['currentPage'] = $req->getParam('p',1);
        if(!in_array($req->getActionName(),array('login','logout') )){
            // by pass the authentication when login
            parent::preDispatch();
        }
    }   
     
    public function searchboxAction()
    {
        $req = $this->getRequest();
        $fid = $req->getParam('fid');
        $qv = $req->getParam('qv');
        $query = $this->_source->select()->from(array('s'=>'sources'),array('count'=>'COUNT(s.id)'))
                                         ->order('s.name ASC');
        $res = $this->_source->fetchRow($query)->toArray();
        $count = $res['count'];
        $this->_paging['totalItems'] = $count;
        $this->_paging['fileName'] = "{$this->_paging_base_path}/p/%d";
        $pager = &Pager::factory($this->_paging);
        $this->view->assign('fid',$fid);
        $this->view->assign('qv',$qv);
        $this->view->assign('total',$count);
        $this->view->assign('links',$pager->getLinks());
        $this->render();
    }

    public function listAction()
    {
        $req = $this->getRequest();
        $field = $req->getParam('fid');
        $value = trim($req->getParam('qv'));
        $query = $this->_source->select()->from('sources','*');
        if(!empty($value)){
            $query->where("$field = ?",$value);
        }
        $query->order('name ASC')
              ->limitPage($this->_paging['currentPage'],$this->_paging['perPage']);
        $source_list = $this->_source->fetchAll($query)->toArray();
        $this->view->assign('source_list',$source_list);
        $this->render();
    }
   
    public function createAction()
    {
        $req = $this->getRequest();
        if('save' == $req->getParam('s')){
            $post = $req->getPost();
            foreach($post as $k=>$v){
                if('source_' == substr($k,0,7)){
                    $k = substr($k,7);
                    $data[$k] = $v;
                }
            }
            $res = $this->_source->insert($data);
            if(!$res){
                $msg = "Error Create Source";
            } else {
                $msg = "Successfully Create a Source.";
            }
            $this->message($msg,self::M_NOTICE);
        }
        $this->render();
    }

    public function deleteAction()
    {
        $req = $this->getRequest();
        $id  = $req->getParam('id');
        $res = $this->_source->delete('id = '.$id);
        if(!$res){
            $msg = "Error for Delete Source";
        } else {
            $msg = "Successfully Delete a Source.";
        }
        $this->message($msg,self::M_NOTICE);
        $this->_forward('list');
    }

    public function viewAction()
    {
        $req = $this->getRequest();
        $id  = $req->getParam('id');
        $result = $this->_source->find($id)->toArray();
        foreach($result as $v){
            $source_list = $v;
        }
        $this->view->assign('id',$id);
        $this->view->assign('source',$source_list);
        if('edit' == $req->getParam('v')){
            $this->render('edit');
        }else{
            $this->render();
        }
    }

    public function updateAction()
    {
        $req = $this->getRequest();
        $id  = $req->getParam('id');
        $post = $req->getPost();
        foreach($post as $k=>$v){
            if('source_' == substr($k,0,7)){
                $k = substr($k,7);
                $data[$k] = $v;
            }
        }
        $res = $this->_source->update($data,'id = '.$id);
        if(!$res){
            $msg = "Edit Source Failed";
        } else {
            $msg = "Successfully Edit Source.";
        }
        $this->message($msg,self::M_NOTICE);
        $this->_forward('view',null,'id = '.$id);
    }

}
