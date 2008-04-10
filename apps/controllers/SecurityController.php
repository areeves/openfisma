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
require_once 'Zend/Db.php';
require_once MODELS . DS . 'user.php';

require_once 'Zend/Acl.php';
require_once 'Zend/Acl/Role.php';
require_once 'Zend/Acl/Resource.php';



class SecurityController extends Zend_Controller_Action
{
    /**
       authenticated user instance
    */
	protected $me = null;

    public function preDispatch()
    {
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity()){
            $this->view->identity = $auth->getIdentity()->user_name;
            $this->me = new User(Zend_Registry::get('db'));
            $this->initializeAcl($auth->getIdentity()->user_id);

        }else{
            $this->_forward('login','user');
        }
    }

    protected function initializeAcl($uid){
        if( !Zend_Registry::isRegistered('acl') )  {
            $acl = new Zend_Acl();
            $db = Zend_Registry::get('db');
            $role_array = $db->fetchAll("SELECT role_nickname FROM `ROLES` r,`USER_ROLES` ur
                                       WHERE ur.user_id = $uid
                                       AND r.role_id = ur.role_id");
            foreach($role_array as $result){
                $acl->addRole(new Zend_Acl_Role($result['role_nickname']));
            }

            $resource = $db->fetchAll("SELECT distinct function_screen FROM `FUNCTIONS`");
            foreach($resource as $result){
                $acl->add(new Zend_Acl_Resource($result['function_screen']));
            }
            $res = $db->fetchAll("SELECT  r.role_nickname,f.function_screen,f.function_action
                                  FROM `ROLES` r,`ROLE_FUNCTIONS` rf,`FUNCTIONS` f,`USER_ROLES` ur
                                  WHERE ur.user_id = $uid
                                  AND ur.role_id = r.role_id
                                  AND r.role_id = rf.role_id
                                  AND rf.function_id = f.function_id");
            foreach($res as $result){
                $acl->allow($result['role_nickname'],$result['function_screen'],$result['function_action']);
            }
            Zend_Registry::set('acl',$acl);
        }else{
            $acl = Zend_Registry::get('acl');
        }
        return $acl;
    }
}


?>
