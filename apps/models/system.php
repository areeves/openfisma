<?php
/**
* OpenFISMA
*
* MIT LICENSE
*
* @version $Id$
*/

require_once 'Zend/Db/Table.php';

class System extends Zend_Db_Table
{
    protected $_name = 'SYSTEMS';
    protected $_primary = 'system_id';

    /**
    * List all the systems.
    * If the $fields contains string other than '*', the returned value of a array is string.
    * It's array otherwise.
    *
    * @param fields array indicating fields interested in.
    * @return array indexed by id
    */
    public function getList($fields = '*', $primary_key = null){
        $db = $this->_db;
        assert($primary_key == null);
        if(empty($primary_key)){
            $query = $db->select()->distinct()->from(array('s'=>'SYSTEMS'),$fields)
                                              ->order("s.system_nickname ASC");
            $result = $db->fetchAll($query);
            foreach($result as $row){
                $list[$row['system_id']] = $row;
            }
        }
        else {
            assert(false);
        }
        return $list;
    }
}

?>
