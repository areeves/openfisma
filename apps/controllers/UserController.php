<?php 

require_once 'Zend/Controller/Action.php';
require_once 'Zend/Auth.php';
require_once 'Zend/Auth/Adapter/DbTable.php';

class UserController extends Zend_Controller_Action 
{
    public function loginAction()
    {
        //We may need to findout user auth configuration and using properly method
        $authAdapter = new Zend_Auth_Adapter_DbTable(Zend_Registry::get('db'), 
                            'USERS', 'user_name', 'user_password');
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
                $auth->getStorage()->write($authAdapter->getResultRowObject(null, 'user_password'));
                return $this->_forward('index','Panel');
            }
        }
        $this->render();
    }

    public function logoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        $this->_forward('login');
    }
}
?>
