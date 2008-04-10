<?php
/**
* OpenFISMA
*
* MIT LICENSE
*
* @version $Id$
*/

    require_once(APPS . DS .'Exception.php');

    function uses() {
        $args = func_get_args();
        foreach ($args as $file) {
            require_once(LIBS . DS . strtolower($file) . '.php');
        }
    }

    function import() {
        $args = func_get_args();
        $target_path = null;
        foreach ($args as $dir) {
            if( is_dir($dir) ) {
                $target_path .= $dir . PATH_SEPARATOR ;
            }else{
                throw new Sws_Exception();
            }
        }
        if(! empty($target_path) ){
            $include_path = ini_get('include_path');
            ini_set('include_path',  $target_path . $include_path);
        }
    }
 
    /** 
        Exam the Acl to decide permission or denial.
        @param $user array of User's roles
        @param $resource resources
        @param $action actions
        @return bool permit or not
    */
    function isAllow($resource, $action) {
        $auth = Zend_Auth::getInstance();
        $me = $auth->getIdentity();
        if($me->user_name == "root"){
            return true;
        }
        $role_array = $me->role_array;
        $acl = Zend_Registry::get('acl');
        try{
            foreach ($role_array as $role){
                if(true == $acl->isAllowed($role,$resource,$action)){
                    return true;
                }
            }
        } catch(Zend_Acl_Exception $e){
            //log information
        }
        return false;
    }
 
?>
