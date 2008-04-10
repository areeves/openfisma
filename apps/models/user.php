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
    public $role_array;

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
   
}
?>
