<?php
/**
* OpenFISMA
*
* MIT LICENSE
*
* @version $Id$
*/

require_once 'Zend/Db/Table.php';

class User extends Zend_Db_Table
{
    protected $_name = 'USERS';
    protected $_primary = 'user_id';


    /**
        Get specified user's roles

        @param $id the user id
        @return array of role nickname
    */
    public function getRoles($id) {
        $role_array = array();
        $db = Zend_Registry::get('db');
        //select user's roles
        $query = "SELECT r.role_nickname FROM `ROLES` r,`USER_ROLES` ur
                  WHERE ur.user_id = $id
                  AND ur.role_id = r.role_id";
        $res = $db->fetchAll($query);
        foreach($res as $result){
            $role_array[] = $result['role_nickname'];
        }
        return $role_array;
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
        if($user['user_name'] == 'root') {
            $qry->where("user_id = $id");
        }
        $db->setFetchMode(Zend_Db::FETCH_OBJ);
        $sys = $db->fetchCol($qry);
        $db->setFetchMode($origin_mode);
        return $sys;
    }


}
?>
