<?php
/**
* OpenFISMA
*
* MIT LICENSE
*
* @version $Id$
*/

require_once 'Zend/Db/Table.php';
require_once  MODELS . DS . 'Abstract.php';
require_once('Zend/Log/Writer/Db.php');
require_once('Zend/Log.php');

class User extends Fisma_Model
{
    protected $_name = 'USERS';
    protected $_primary = 'user_id';
    protected $_log_name = 'account_log';
    protected $_logger = null;
    protected $_log_map = array('priority'=>'priority','timestamp'=>'timestamp',
                                'user_id' => 'uid', 'event_type' => 'type',
                                'message'=>'message','priority_name' => 'priorityName');

    protected $_map = array(self::SYS=>array('table'=>'USER_SYSTEM_ROLES','field'=>'system_id'),
                            self::ROLE=>array('table'=>'USER_ROLES','field'=>'role_id') );

    const SYS = 'system';
    const ROLE = 'role';

    const CREATION = 'creation';
    const MODIFICATION= 'modification';
    const DISABLING= 'disabling';
    const TERMINATION = 'termination';
    const LOGINFAILURE = 'loginfailure';
    const LOGIN = 'login';
    const LOGOUT = 'logout';

    public function init()
    {
        $writer = new Zend_Log_Writer_Db($this->_db, $this->_log_name, $this->_log_map );
        if( empty($this->_logger) ){
            $this->_logger = new Zend_Log($writer);
        }
    }

    /**
        Get specified user's roles

        @param $id the user id
        @return array of role nickname
    */
    public function getRoles($id, $fields=array('nickname'=>'role_nickname')) {
        $role_array = array();
        $db = $this->_db;

        $qry = $db->select()
                  ->from(array('u'=>'USERS'),array())
                  ->join(array('ur'=>'USER_ROLES'),'u.user_id = ur.user_id',array())
                  ->join(array('r'=>'ROLES'),'r.role_id = ur.role_id',$fields)
                  ->where("u.user_id = $id and r.role_name != 'none'");

        return  $db->fetchAll($qry);
    }

    /**
        Retrieve the systems that the user belongs to

        @param $id user id
        @return array the system ids

    */
    public function getMySystems($id)
    {
        assert($id);
        $db = Zend_Registry::get('db');
        $origin_mode = $db->getFetchMode();
        $qry = $db->select()->from($this->_name, 'user_name')->where('user_id = ?', $id);
        $user = $db->fetchOne($qry); 

        $qry = $db->select()->distinct()->from('USER_SYSTEM_ROLES', 'system_id');
        if($user != 'root') {
            $qry->where("user_id = $id");
        }
        $db->setFetchMode(Zend_Db::FETCH_OBJ);
        $sys = $db->fetchCol($qry);
        $db->setFetchMode($origin_mode);
        if( empty($sys) ){
            throw new fisma_Exception('ATTENTION: No systems found! Please
                    make sure the fundamental data is injected');
        }
        return $sys;
    }

    /** 
        Log any creation, modification, disabling and termination of account.

        @param $type constant {CREATION,MODIFICATION,DISABLING,TERMINATION,
                        LOGIN,LOGINFAILURE,LOGOUT}
        @param $uid int action taken user id
        @param $extra_msg string extra message to be logged.
    */
    public function log($type, $uid, $msg=null)
    {
        assert(in_array($type, array(self::CREATION,self::MODIFICATION,
                                    self::DISABLING,self::TERMINATION,
                                    self::LOGINFAILURE,self::LOGIN,self::LOGOUT)) );
        assert(is_string($msg));
        assert($this->_logger);

        $rows = $this->find($uid);
        $row = $rows->current();

        if( $type == self::LOGINFAILURE ) {
            $row->failure_count++;
            $row->save();
        }
        if( $type == self::LOGIN ) {
            $row->failure_count=0;
            $row->user_date_last_login = date("YmdHis");
            $row->save();
        }

        $this->_logger->setEventItem('uid', $uid);
        $this->_logger->setEventItem('type', $type);
        $this->_logger->info($msg);
    }

    /**
        Associate systems to a user.

        @param uid int the user id
        @param type type of associated data, one of system, role.
        @param data array|int system or role id or array of them
    */
    public function associate($uid, $type, $data)
    {
        assert( !empty($uid) && (is_numeric($data) || is_array( $data )) );
        assert( in_array($type, array(self::SYS, self::ROLE) ) );

        if(is_numeric($data) ){
            $data = array($data);
        }
        $ins_data['user_id'] = $uid;
        foreach($data as $id){
            $ins_data[$this->_map[$type]['field']] = $id;
            $this->_db->insert($this->_map[$type]['table'],$ins_data);
        }
    }


}
