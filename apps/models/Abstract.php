<?php
/**
* OpenFISMA
*
* MIT LICENSE
*
* @version $Id$
*/

require_once 'Zend/Db/Table.php';

abstract class Fisma_Model extends Zend_Db_Table
{
    /**
    * List all entries in the table
    * If the $fields contains string other than '*', the returned value of a array is string.
    * It's array otherwise.
    *
    * @param fields array indicating fields interested in.
    * @return array indexed by primary key(id)
    */
    public function getList($fields = '*', $primary_key = null){

        assert($primary_key == null); //not supported yet.

        if($fields != '*'){
            array_merge(array($this->_primary), $fields);
            assert(is_array($fields));
        }

        $list = array();
        if(empty($primary_key)){
            $query = $this->_db->select()->distinct()->from($this->_name,$fields);
            $result = $this->fetchAll($query);
        } else {
            $result = $this->find($primary_key);
        }
        if($fields == '*' ){
            $fields = array_flip($this->cols);
        }else{
            $fields = array_flip($fields);
        }
        unset($fields[$this->_primary_key]);

        foreach($result as $row){
            $list[$row->{$this->_primary_key}] = 
                    array_intersect_assoc($row->toArray(), $fields);
        }
        return $list;
    }
}
