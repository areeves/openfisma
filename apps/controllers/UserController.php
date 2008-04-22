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
require_once('Pager.php');

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
    /**
        Override the parent preDispatch for this is the first time of Authentication
     */
    public function preDispatch()
    {
        $req = $this->getRequest();
        $this->_paging_base_path = $req->getBaseUrl() .'/panel/user';
        $this->_paging['currentPage'] = $req->getParam('p',1);
    }

    public function loginAction()
    {
        //We may need to findout user auth configuration and using properly method
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
                // Authentication failed; print the reasons why
                $error = "";
                foreach ($result->getMessages() as $message) {
                     $error .= "$message<br>"; 
                }
                $this->view->assign('error', $error);
            } else {
                $me = $authAdapter->getResultRowObject(null, 'user_password');
                $user = new User($db);
                $me->role_array = $user->getRoles($me->user_id);
                $auth->getStorage()->write($me);
                return $this->_forward('index','Panel');
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
        Zend_Auth::getInstance()->clearIdentity();
        $this->_forward('login');
    }

    /**
    Search user
    */
    public function searchboxAction()
    {
        $this->_helper->actionStack('header','panel');
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
        $this->_paging_base_path = $req->getBaseUrl().'/panel/user';
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
        $this->_helper->actionStack('list','user');
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
}
?>
