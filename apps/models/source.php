<?php
/**
* OpenFISMA
*
* MIT LICENSE
*
* @version $Id$
*/

require_once 'Zend/Db/Table.php';

class Source extends Zend_Db_Table
{
    protected $_name = 'FINDING_SOURCES';
    protected $_primary = 'source_id';

    /**
    * List all the sources.
    * If the $fields contains string other than '*', the returned value of a array is string.
    * It's array otherwise.
    *
    * @param fields array indicating fields interested in.
    * @return array indexed by id
    */
    public function getList($fields = '*', $primary_key=null){
        $db = $this->_db;
        if(empty($primary_key)){
            $query = $db->select()->distinct()->from(array('s'=>'FINDING_SOURCES'),$fields)
                                              ->order("source_nickname ASC");
            $result = $db->fetchAll($query);
            foreach($result as $row){
                $list[$row['source_id']] = $row;
            }
        }
        else {
            assert(false);
        }
        return $list;
    }
}

?>
