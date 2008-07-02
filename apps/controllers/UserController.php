<?php 
/**
 * @file UserController.php
 *
 * @description User Controller
 *
 * @author     Jim <jimc@reyosoft.com>
 * @copyright  (c) Endeavor Systems, Inc. 2008 (http://www.endeavorsystems.com)
 * @license    http://www.openfisma.org/mw/index.php?title=License
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
                $error = "Incorrect username or password";
                $this->view->assign('error', $error);
            } else {
                $me = $authAdapter->getResultRowObject(null,'password');
                $period = readSysConfig('max_absent_time');
                $deactive_time = clone $now;
                $deactive_time->sub($period,Zend_Date::DAY);
                $last_login = new Zend_Date($me->last_login_ts);

                if( !$last_login->equals(new Zend_Date('0000-00-00 00:00:00')) 
                    && $last_login->isEarlier($deactive_time) ){
                    $error = "Your account was locked due to $period days of inactivity. Please contact an administrator.";
                    $this->view->assign('error',$error);
                } else {
                    $this->_user->log(User::LOGIN, $me->id, "Success");
                    $nickname = $this->_user->getRoles($me->id);
                    foreach($nickname as $n ) {
                        $me->role_array[] = $n['nickname'];
                    }
                    if( empty( $me->role_array ) ) {
                        $me->role_array[] = $me->account . '_r';
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
        $auth = Zend_Auth::getInstance();
        $me = $auth->getIdentity();
        if( !empty($me) ) {
            $this->_user->log(User::LOGOUT, $me->id,$me->account.' logout');
            Zend_Auth::getInstance()->clearIdentity();
        }
        $this->_forward('login');
    }

    public function pwdchangeAction()
    {
        $req = $this->getRequest();
        if('save' == $req->getParam('s')){
            $auth = Zend_Auth::getInstance();
            $me = $auth->getIdentity();
            $id   = $me->id;
            $pwds = $req->getPost('pwd');
            $oldpass = md5($pwds['old']);
            $newpass = md5($pwds['new']);
            $res = $this->_user->find($id)->toArray();
            $password = $res[0]['password'];
            $history_pass = $res[0]['history_password'];
            if($pwds['new'] != $pwds['confirm']){
                $msg = 'the new password does not match the confirm password, please try again.';
            }else{
                if($oldpass != $password){
                    $msg = 'The old password supplied does not match what we have on file, please try again.';
                }else{
                    if(!$this->checkPassword($pwds['new'],2)){
                        /*$msg = 'This password does not meet the password complexity requirements.<br>
Please create a password that adheres to these complexity requirements:<br>
--The password must be at least 8 character long<br>
--The password must contain at least 1 lower case letter (a-z), 1 upper case letter (A-Z), and 1 digit (0-9)<br>
--The password can also contain National Characters if desired (Non-Alphanumeric, !,@,#,$,% etc.)<br>
--The password cannot be the same as your last 3 passwords<br>
--The password cannot contain your first name or last name<br>";';*/
                        $msg = 'The password doesn\'t meet the required complexity!';

                    }else{
                        if($newpass == $password){
                            $msg = 'Your new password cannot be the same as your old password.';
                        }else{
                            if(strpos($history_pass,$newpass) > 0 ){
                                $msg = 'Your password must be different from the last three passwords you have used. Please pick a different password.';
                            }else{
                                if(strpos($history_pass,$password) > 0){
                                    $history_pass = ':'.$newpass.$history_pass;
                                }else{
                                    $history_pass = ':'.$newpass.':'.$password.$history_pass;
                                }
                                $history_pass = substr($history_pass,0,99);
                                $now = date('Y-m-d H:i:s');
                                $data = array('password'=>$newpass,
                                              'history_password'=>$history_pass,
                                              'password_ts'=>$now);
                                $result = $this->_user->update($data,'id = '.$id);
                                if(!$result){
                                    $msg = 'Password Changed Failed';
                                }else{
                                    $msg = 'Password Changed Successfully';
                                }
                            }
                        }
                    }   
                }
            }
            $this->message($msg,self::M_NOTICE);
        }
        $this->_helper->actionStack('header','Panel');
        $this->render();
    }

    function checkPassword($pass, $level = 1) {
        if($level > 1) {

            $nameincluded = true;
            // check last name
            if(empty($this->user_name_last) || strpos($pass, $this->user_name_last) === false) {
                $nameincluded = false;
            }
            if(!$nameincluded) {
                // check first name
                if(empty($this->user_name_first) || strpos($pass, $this->user_name_first) === false)
                    $nameincluded = false;
                else
                    $nameincluded = true;
            }
            if($nameincluded)
                return false; // include first name or last name

            // high level
            if(strlen($pass) < 8)
                return false;
            // must be include three style among upper case letter, lower case letter, symbol, digit.
            // following rule: at least three type in four type, or symbol and any of other three types
            $num = 0;
            if(preg_match("/[0-9]+/", $pass)) // all are digit
                $num++;
            if(preg_match("/[a-z]+/", $pass)) // all are digit
                $num++;
            if(preg_match("/[A-Z]+/", $pass)) // all are digit
                $num++;
            if(preg_match("/[^0-9a-zA-Z]+/", $pass)) // all are digit
                $num += 2;

            if($num < 3)
                return false;
        }
        else if($level == 1) {
            // low level
            if(strlen($pass) < 3)
                return false;
            // must include three style among upper case letter, lower case letter, symbol, digit.
            // following rule: at least two type in four type
            if(preg_match("/^[0-9]+$/", $pass)) // all are digit
                return false;

            if(preg_match("/^[a-z]+$/", $pass)) // all are lower case letter
                return false;

            if(preg_match("/^[A-Z]+$/", $pass)) // all are upper case letter
                return false;
        }

        return true;
    }

}
