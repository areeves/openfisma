<?php 
/**
* OpenFISMA
*
* MIT LICENSE
*
* @version $Id$
*/

require_once 'Zend/Controller/Action.php';
require_once 'Zend/Auth.php';
require_once 'Zend/Auth/Adapter/DbTable.php';
require_once( CONTROLLERS . DS . 'SecurityController.php');
require_once( MODELS . DS .'user.php');
require_once( MODELS . DS .'system.php');
require_once('Pager.php');
require_once 'Zend/Date.php';

class UserController extends SecurityController
{
    private $role_array;
    private $_paging = array(
            'mode'        =>'Sliding',
            'append'      =>false,
            'urlVar'      =>'p',
            'path'        =>'',
            'currentPage' => 1,
            'perPage'=>20);
    private $_user = null;

    public function init()
    {
        $this->_user = new User();
    }

    public function preDispatch()
    {
        $req = $this->getRequest();
        $this->_paging_base_path = $req->getBaseUrl() .'/panel/user/sub/list';
        $this->_paging['currentPage'] = $req->getParam('p',1);
        if($req->getActionName() != 'login'){
            // by pass the authentication when login
            parent::preDispatch();
        }
    }

    public function loginAction()
    {
        //We may need to findout user auth configuration and using properly method
        $now = new Zend_Date();
        $db = Zend_Registry::get('db'); 
        $authAdapter = new Zend_Auth_Adapter_DbTable($db, 'USERS', 'user_name', 'user_password');
        $auth = Zend_Auth::getInstance();
        $req = $this->getRequest();
        $username = $req->getPost('username');
        $password = md5($req->getPost('userpass'));
        $this->_helper->layout->setLayout('login');
        if( !empty($username) && !empty($password) ) {
            $authAdapter->setIdentity($username)->setCredential($password);
            $result = $auth->authenticate($authAdapter);
            if (!$result->isValid()) {
                if(Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID == $result->getCode()){
                    $whologin = $this->_user->fetchRow("user_name = '$username'");
                    $this->_user->log(User::LOGINFAILURE, $whologin->user_id,'Password Error');
                }
                // Authentication failed; print the reasons why
                $error = "";
                foreach ($result->getMessages() as $message) {
                     $error .= "$message<br>"; 
                }
                $this->view->assign('error', $error);
            } else {
                $me = $authAdapter->getResultRowObject(null, 'user_password');
                $period = readSysConfig('max_absent_time');
                $deactive_time = clone $now;
                $deactive_time->sub($period,Zend_Date::DAY);
                $last_login = new Zend_Date($me->user_date_last_login);

                if( !$last_login->equals(new Zend_Date('0000-00-00 00:00:00')) 
                    && $last_login->isEarlier($deactive_time) ){
                    $error = "your account was locked because of your last login date from now is aleady past
                             ".$period." days";
                    $this->view->assign('error',$error);
                } else {
                    $this->_user->log(User::LOGIN, $me->user_id, "Success");
                    $nickname = $this->_user->getRoles($me->user_id);
                    foreach($nickname as $n ) {
                        $me->role_array[] = $n;
                    }
                    $me->systems = $this->_user->getMySystems($me->user_id);
                    $auth->getStorage()->write($me);
                    return $this->_forward('index','Panel');
                }
            }
        }
        $this->render();
    } 
    

     /**
        Exam the Acl to decide permission or denial.
        @param $user array of User's roles
        @param $resource resources
        @param $action actions
        @return bool permit or not
    */
    
    public function logoutAction()
    {
        $this->_user->log(User::LOGOUT, $this->me->user_id,$this->me->user_name.' logout');
        Zend_Auth::getInstance()->clearIdentity();
        $this->_forward('login');
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
                     'username'=>'Username');
        $this->view->assign('fid_array',$fid_array);
        $req = $this->getRequest();
        $this->_paging_base_path = $req->getBaseUrl().'/panel/user/sub/list';
        $this->_paging['currentPage'] = $req->getParam('p',1);
        $fid = $req->getParam('fid');
        $qv = $req->getParam('qv');
        $res = $db->fetchRow("SELECT COUNT(*) AS count FROM `USERS` u,`ROLES` r WHERE u.role_id = r.role_id");
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
                    ->from(array('u'=>'USERS'),array('id'=>'user_id',
                                                     'username'=>'user_name',
                                                     'lastname'=>'user_name_last',
                                                     'firstname'=>'user_name_first',
                                                     'officephone'=>'user_phone_office',
                                                     'mobile'=>'user_phone_mobile',
                                                     'email'=>'user_email',
                                                     'roleid'=>'role_id'));
        $qry->join(array('r'=>'ROLES'),'u.role_id = r.role_id',array('rolename'=>'role_name'));
        if(!empty($qv)){
            $fid_array = array('user_name_last'=>'lastname',
                               'user_name_first'=>'firstname',
                               'user_phone_office'=>'officephone',
                               'user_phone_mobile'=>'mobile',
                               'user_email'=>'email',
                               'r.role_name'=>'role',
                               'user_title'=>'title',
                               'user_is_active'=>'status',
                               'user_name'=>'username');
            foreach($fid_array as $k=>$v){
                if($v == $fid){
                    $qry->where("$k = '$qv'");
                }
            }
        }
        $qry->order("user_name_last ASC");
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
        $do  = $req->getParam('do');
        assert($id);
        $user = new User();
        $sys = new System();
        $db = $user->getAdapter();
        $qry = $user->select()->setIntegrityCheck(false);
        /** get user detail */
        $qry->from(array('u'=>'USERS'),array('lastname'=>'user_name_last',
                                           'firstname'=>'user_name_first',
                                           'officephone'=>'user_phone_office',
                                           'mobilephone'=>'user_phone_mobile',
                                           'email'=>'user_email',
                                           'title'=>'user_title',
                                           'status'=>'user_is_active',
                                           'username'=>'user_name',
                                           'password'=>'user_password'))
            ->where("u.user_id = $id");
        $user_detail = $user->fetchRow($qry)->toArray();

        $roles = $user->getRoles($id, array('name'=>'role_name'));

        /** get user systems */
        $ids = implode(',',$user->getMySystems($id));
        $qry->reset();
        $systems = $db->fetchPairs($qry->from($sys->info(Zend_Db_Table::NAME),
                               array('id'=>'system_id','name'=>'system_name'))
                      ->where("system_id IN ( $ids )")
                      ->order('id ASC'));
        $this->view->assign('id',$id);
        $this->view->assign('user',$user_detail);
        $this->view->assign('roles',$roles);
        $this->view->assign('systems',$systems);
        if('edit' == $v){
            $qry = $db->select()->from(array('s'=>'SYSTEMS'),array('sid'=>'system_id',
                                                                        'sname'=>'system_name'));
            $sys = $db->fetchAll($qry);
            foreach($systems as $k=>$v){
                $sid[] = $k;
            }
            $this->view->assign('sid_arr',$sid);
            $this->view->assign('sys',$sys);
            $this->render('edit');
        }
        else {
            $this->render();
        }
    }

    /**
      update user 
    */
    public function updateAction(){
        $req = $this->getRequest();
        $id = $req->getParam('id');
        $post = $req->getPost();
        $msg = '';
        if(!empty($post)){
            if($post['user_password'] != $post['confirm_password']){
                $msg = "Password dosen't match confirmation.Please submit password and confirmation";
            }
            else {
                foreach($post as $k=>$v){
                    if('user_' == substr($k,0,5) ){
                        if('password' == substr($k,5,8)){
                            if(!empty($v)){
                                $field[$k] = md5($v);
                                $field['user_date_password'] = 0;
                            }
                        }
                        else {
                            $field[$k] = $v;
                        }
                    }
                    if('user_is_active' == 0){
                        $field['user_date_deleted'] = date("Y-m-d H:m:s");
                    }
                    if('user_is_active' == 1){
                        $field['user_date_deleted'] = '';
                    }
                    if('system' == substr($k,0,6)){
                        $sys_field[] = $v;
                    }
                }
                $user = $this->_user;
                $db = $user->getAdapter();
                $res = $db->update('USERS',$field,'user_id = '.$id.'');
                $res .=$db->delete('USER_SYSTEM_ROLES','user_id = '.$id.'');
                foreach($sys_field as $v){
                    $data = array('user_id'=>$id,'system_id'=>$v);
                    $res .=$db->insert('USER_SYSTEM_ROLES',$data);
                }
                if($res){
                    $msg = '<p>User <b>modified</b> successful!</p>';
                    $this->_user->log(User::MODIFICATION, $this->me->user_id, $field['user_name']);
                }
                else {
                    $msg = '<p>User <b>modified</b> failed!</p>';
                }
            }
        }
        $this->view->assign('msg',$msg);
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
        $user = $this->_user;
        $db = $user->getAdapter();
        $res = $db->delete('USERS','user_id = '.$id);
        $res = $db->delete('USER_SYSTEM_ROLES','user_id = '.$id);
        $res = $db->delete('USER_ROLES','user_id = '.$id);
        if($res){
            $msg ="<p><b>User Deleted successfully</b></p>";
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

        $this->view->roles = $r->getList('role_name');
        $this->view->systems = $system->getList(array('id'=>'system_id','name'=>'system_name'));
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
                if( !in_array($k,array('role','confirm_password','user_password'))){
                    $data[$k] = $v;
                }else{
                    ///< @todo compare the password
                    if($k == 'user_password'){
                        $data[$k] = md5($v);
                    }
                }
            }else{
                $systems[] = $v;
            }
        }
        $data['user_date_created'] = date('Y-m-d H:i:s');
        $data['extra_role'] = $req->getParam('user_name').'_r';

        $user_id = $this->_user->insert($data);
        $role_id = $req->getParam('role_id');
        $this->_user->associate($user_id, User::ROLE, $role_id);

        $this->_user->associate($user_id, User::SYS, $systems);

        $this->_user->log(User::CREATION ,$this->me->user_id,'create user('.$data['user_name'].')');
        $this->message("User({$data['user_name']}) added", self::M_NOTICE);
        $this->_forward('create');
    }


}
?>
