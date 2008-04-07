<?php

require_once 'Zend/Controller/Action.php';
require_once 'Zend/Auth.php';

class SecurityController extends Zend_Controller_Action
{
    public function preDispatch() 
    {
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity()){
            $this->view->identity = $auth->getIdentity()->user_name;
        }else{
            $this->_forward('login','user');
        }
    }

}


?>
