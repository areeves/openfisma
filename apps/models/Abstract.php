<?php
/**
* OpenFISMA
*
* MIT LICENSE
*
* @version $Id$
*/

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
        $primary = $this->_primary;
        $id_name = array_pop($primary);

        if($fields != '*'){
            assert(is_array($fields));
            $fields = array_merge(array($id_name), $fields);
        }else{
            $fields = $this->_cols;
        }

        $list = array();

        if(empty($primary_key)){
            $query = $this->select()->distinct()->from($this->_name,$fields);
            $result = $this->fetchAll($query);
            
        } else {
            $result = $this->find($primary_key);
        }
        
        foreach($result as $row){
            foreach( $fields as $k=>$v ){
                if( $v != $id_name ){
                    if( is_string($k) ){
                        $list[$row->$id_name][$k] = $row->$k;
                    }else{
                        $list[$row->$id_name][$v] = $row->$v;
                    }
                }
            }
        }
        return $list;
    }
}
