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
                throw new fisma_Exception($dir . ' is missing or not a directory');
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
        if($me->account == "root"){
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


    define('SYSCONFIG','sysconf');
    /** 
        Read configurations of any sections.
        This function manages the storage, the cache, lazy initializing issue.
        
        @param $key string key name
        @param $is_fresh boolean to read from persisten storage or not.
        @return string configuration value.
     */
    function readSysConfig($key, $is_fresh = false)
    {
        assert( !empty($key) && is_bool($is_fresh) );
        if( ! Zend_Registry::isRegistered(SYSCONFIG) || $is_fresh ){
            require_once( MODELS . DS . 'config.php' );
            $m = new Config();
            $pairs = $m->fetchAll();
            $configs = array();
            foreach( $pairs as $v ) {
                $configs[$v->key] = $v->value;
            }
            Zend_Registry::set(SYSCONFIG, new Zend_Config($configs) );
        }
        if( !isset(Zend_Registry::get(SYSCONFIG)->$key) ){
            throw new fisma_Exception("$key does not exist in system configuration");
        }
        return Zend_Registry::get(SYSCONFIG)->$key;
    }

    function makeSqlInStmt($array)
    {
        assert( is_array($array) );
        return "'" . implode("','", $array). "'"; 
    }

    function echoDefault(&$value, $default='')
    {
        echo nullGet($value, $default);
    }

    function nullGet(&$value, $default='')
    {
        if( isset($value) ) {
            return $value;
        }else{
            return $default;
        }
    }
 
