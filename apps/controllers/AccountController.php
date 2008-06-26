<?php 
/**
 * @file:AccountController.php
 *
 * @description Account Controller
 *
 * @author     Ryan <ryan.yang@reyosoft.com>
 * @copyright  (c) Endeavor Systems, Inc. 2008 (http://www.endeavorsystems.com)
 * @license    http://www.openfisma.org/mw/index.php?title=License
 * @version $Id$
*/

require_once( CONTROLLERS . DS . 'PoamBaseController.php');
require_once( MODELS . DS .'user.php');
require_once( MODELS . DS .'system.php');
require_once('Pager.php');
require_once 'Zend/Date.php';

class AccountController extends PoamBaseController
{
    private $role_array;
    private $_user = null;

    public function init()
    {
        parent::init();
        $this->_user = new User();
    }

    public function preDispatch()
    {
        parent::preDispatch();
        $req = $this->getRequest();
        $this->_paging_base_path = $req->getBaseUrl() .'/panel/account/sub/list';
        $this->_paging['currentPage'] = $req->getParam('p',1);
    }

    /**
    Search user
    */
    public function searchboxAction()
    {
        $db = Zend_Registry::get('db');
        $fid_array = array('lastname'=>'Last Name',
                     'firstname'=>'First Name',
                     'officephone'=>'Office Phone',
                     'mobile'=>'Mobile Phone',
                     'email'=>'Email',
                     'role'=>'Role',
                     'title'=>'Title',
                     'status'=>'Status',
                     'account'=>'Username');
        $this->view->assign('fid_array',$fid_array);
        $req = $this->getRequest();
        $this->_paging_base_path = $req->getBaseUrl().'/panel/account/sub/list';
        $this->_paging['currentPage'] = $req->getParam('p',1);
        $fid = $req->getParam('fid');
        $qv = $req->getParam('qv');
        $query = $db->select()->from(array('u'=>'users'),array('count'=>'COUNT(u.id)'))
                              ->join(array('ur'=>'user_roles'),'u.id = ur.user_id',array())
                              ->join(array('r'=>'roles'),'ur.role_id = r.id',array());
        $res = $db->fetchRow($query);
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

    /**
      Get User acount infomation
    */
    public function listAction()
    {
        $user = new user();
        $db = Zend_Registry::get('db');
        $req = $this->getRequest();
        $qv = $req->getParam('qv');
        $fid = $req->getParam('fid');
        $qry = $user->select()->setIntegrityCheck(false)
                    ->from(array('u'=>'users'),array('id'=>'id',
                                                     'username'=>'account',
                                                     'lastname'=>'name_last',
                                                     'firstname'=>'name_first',
                                                     'officephone'=>'phone_office',
                                                     'mobile'=>'phone_mobile',
                                                     'email'=>'email'))
                    ->join(array('ur'=>'user_roles'),'u.id = ur.user_id',array())
                    ->join(array('r'=>'roles'),'ur.role_id = r.id',array('rolename'=>'r.name'));
        if(!empty($qv)){
            $fid_array = array('name_last'=>'lastname',
                               'name_first'=>'firstname',
                               'phone_office'=>'officephone',
                               'phone_mobile'=>'mobile',
                               'email'=>'email',
                               'r.role_name'=>'role',
                               'title'=>'title',
                               'is_active'=>'status',
                               'account'=>'account');
            foreach($fid_array as $k=>$v){
                if($v == $fid){
                    $qry->where("$k = '$qv'");
                }
            }
        }
        $qry->order("name_last ASC");
        $qry->limitPage($this->_paging['currentPage'],$this->_paging['perPage']);
        $data = $user->fetchAll($qry);
        $user_list = $data->toArray();
        $this->view->assign('user_list',$user_list);
        $this->render();
    }

    /**
     User detail
    */
    public function viewAction()
    {
        $req = $this->getRequest();
        $id  = $req->getParam('id');
        $v   = $req->getParam('v');
        assert($id);
        $user = new User();
        $sys = new System();
        $db = $user->getAdapter();
        $qry = $user->select()->setIntegrityCheck(false);
        /** get user detail */
        $qry->from(array('u'=>'users'),array('lastname'=>'name_last',
                                           'firstname'=>'name_first',
                                           'officephone'=>'phone_office',
                                           'mobilephone'=>'phone_mobile',
                                           'email'=>'email',
                                           'title'=>'title',
                                           'status'=>'is_active',
                                           'username'=>'account',
                                           'password'=>'password'))
            ->where("u.id = $id");
        $user_detail = $user->fetchRow($qry)->toArray();

        $roles = $user->getRoles($id, array('name'=>'name'));

        $this->view->assign('id',$id);
        $this->view->assign('user',$user_detail);
        $this->view->assign('roles',$roles);
        $this->view->assign('my_systems',$user->getMySystems($id));
        $this->view->assign('all_sys',$sys->getList('name') );
        $this->render($v);
    }

    /**
      update user 
    */
    public function updateAction(){
        $req = $this->getRequest();
        $id = $req->getParam('id');
        $u_data = $req->getPost('user');
        $sys_data = $req->getPost('system');
        $confirm_pwd = $req->getPost('confirm_password');
        if( isset($u_data['password']) ) {
            /// @todo validate the password complexity
            if( $u_data['password'] != $confirm_pwd){
                throw new fisma_Exception(
                "Password dosen't match confirmation.Please submit password and confirmation");
            }
            $u_data['password'] = md5($u_data['password']);
        }
        if(!empty($u_data)){
            if($u_data['is_active'] == 0){
                $u_data['termination_ts'] = self::$now->toString("Y-m-d H:i:s");
            }
            $n = $this->_user->update($u_data, "id=$id");
            if( $n > 0) {
                $this->_user->log(User::MODIFICATION, $this->me->id, $u_data['account']);
            }
            if(!empty($sys_data)){
                $my_sys = $this->_user->getMySystems($id);
                $new_sys = array_diff($sys_data,$my_sys); 
                $remove_sys = array_diff($my_sys,$sys_data);
            
                $n = $this->_user->associate($id, User::SYS, $new_sys);
                $n = $this->_user->associate($id, User::SYS, $remove_sys, true);
            }
        }
        $this->_forward('view');
    }

    /**
     Delete user
    */
    public function deleteAction(){
        $req = $this->getRequest();
        $id = $req->getParam('id');
        assert($id);
        $msg ="";
        $rows = $this->_user->getList('account',$id);
        $user_name = $rows[$id];
        $res = $this->_user->delete('id = '.$id);
        $res = $this->_user->getAdapter()->delete('user_systems','user_id = '.$id);
        $res = $this->_user->getAdapter()->delete('user_roles','user_id = '.$id);
        if($res){
            $msg ="<p><b>User Deleted successfully</b></p>";
            $this->_user->log(USER::TERMINATION,$this->me->id,'delete user '.$user_name); 
        }
        else {
            $msg ="<p><b>User Deleted failed</b></p>";
        }
        $this->view->assign('msg',$msg);
        $this->_forward('list');
    }
    /**
       Create user
    **/
    public function createAction()
    {
        require_once(MODELS . DS . 'role.php');
        $r = new Role();
        $system = new system();

        $this->view->roles = $r->getList('name');
        $this->view->systems = $system->getList(array('id'=>'id','name'=>'name'));
        $this->render();
    }

    /**
       Save new user
    **/
    public function saveAction()
    {
        require_once(MODELS . DS . 'role.php');
        $req = $this->getRequest();
        foreach($req->getPost() as $k=>$v){
            if(substr($k,0,6) != 'system' ){
                if( !in_array($k,array('user_role_id','confirm_password','user_password'))){
                    $key = substr($k,5);
                    $data[$key] = $v;
                }else{
                    ///< @todo compare the password
                    if($k == 'user_password'){
                        $data['password'] = md5($v);
                    }
                }
            }else{
                $systems[] = $v;
            }
        }
        $data['created_ts'] = date('Y-m-d H:i:s');
        $data['auto_role'] = $req->getParam('user_account').'_r';
       
        $user_id = $this->_user->insert($data);
        $role_id = $req->getParam('user_role_id');
        $this->_user->associate($user_id, User::ROLE, $role_id);
         
        if(!empty($systems)){
            $this->_user->associate($user_id, User::SYS, $systems);
        }

        $this->_user->log(User::CREATION ,$this->me->id,'create user('.$data['account'].')');
        $this->message("User({$data['account']}) added", self::M_NOTICE);
        $this->_forward('create');
    }


}
