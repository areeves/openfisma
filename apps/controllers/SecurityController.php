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
    const M_NOTICE = 'notice';
    const M_WARNING= 'warning';

    /**
     * Authentication check and ACL initialization
     * @todo cache the acl
     */
    public function preDispatch()
    {
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity()){
            $this->me = $auth->getIdentity();
            $this->view->identity = $this->me->user_name;
            $this->initializeAcl($this->me->user_id);
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

    /**
     * Show messages to Users
     */
    public function message( $msg , $model ){
        assert(in_array($model, array(self::M_NOTICE, self::M_WARNING) ));
        $this->view->msg = $msg;
        $this->view->model= $model;
        $this->_helper->viewRenderer->renderScript('message.tpl');
    }
}
?>
