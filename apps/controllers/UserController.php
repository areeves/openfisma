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
require_once 'Zend/Date.php';

class UserController extends SecurityController
{
    private $_user = null;

    public function init()
    {
        $this->_user = new User();
    }

    public function preDispatch()
    {
    }

    public function loginAction()
    {
        //We may need to findout user auth configuration and using properly method
        $now = new Zend_Date();
        $db = Zend_Registry::get('db'); 
        $authAdapter = new Zend_Auth_Adapter_DbTable($db, 'users', 'account', 'password');
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
                    $whologin = $this->_user->fetchRow("account = '$username'");
                    if( !empty($whologin) ){
                        $this->_user->log(User::LOGINFAILURE, $whologin->id,'Password Error');
                    }
                }
                // Authentication failed; print the reasons why
                $error = "";
                foreach ($result->getMessages() as $message) {
                     $error .= "$message<br>"; 
                }
                $this->view->assign('error', $error);
            } else {
                $me = $authAdapter->getResultRowObject(null, 'password');
                $period = readSysConfig('max_absent_time');
                $deactive_time = clone $now;
                $deactive_time->sub($period,Zend_Date::DAY);
                $last_login = new Zend_Date($me->last_login_ts);

                if( !$last_login->equals(new Zend_Date('0000-00-00 00:00:00')) 
                    && $last_login->isEarlier($deactive_time) ){
                    $error = "your account was locked because of your last login date from now is aleady past
                             ".$period." days";
                    $this->view->assign('error',$error);
                } else {
                    $this->_user->log(User::LOGIN, $me->id, "Success");
                    $nickname = $this->_user->getRoles($me->id);
                    foreach($nickname as $n ) {
                        $me->role_array[] = $n['nickname'];
                    }
                    $me->systems = $this->_user->getMySystems($me->id);
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
        if( !empty($this->me ) ) {
            $this->_user->log(User::LOGOUT, $this->me->id,$this->me->account.' logout');
            Zend_Auth::getInstance()->clearIdentity();
        }
        $this->_forward('login');
    }
}
