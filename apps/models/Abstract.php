<?php
/**
 * @file Abstract.php
 *
 * @description Abstract Model
 *
 * @author     Jim <jimc@reyosoft.com>
 * @copyright  (c) Endeavor Systems, Inc. 2008 (http://www.endeavorsystems.com)
 * @license    http://www.openfisma.org/mw/index.php?title=License
 * @version $Id$
*/

abstract class Fisma_Model extends Zend_Db_Table
{
    /**
    * List all entries in the table
    * If the $fields contains string other than '*', the value of returned array is string.
    * It's array otherwise. 
    *
    * @param fields array indicating fields interested in.
    * @param primary_key int|string|array primary key(s) 
    * @return array indexed by primary key(id)
    */
    public function getList($fields = '*', $primary_key = null){

        $primary = $this->_primary;
        $id_name = array_pop($primary);

        $is_pair = false;
        if($fields != '*'){
            if( is_string($fields) ){
                $is_pair = true;
                $fields = array($id_name, $fields);
            }else{
                assert(is_array($fields));
                $fields = array_merge(array($id_name), $fields);
            }
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
                    if( $is_pair ) {
                        $list[$row->$id_name] = $row->$v;
                    }else{
                        if( is_string($k) ){
                            $list[$row->$id_name][$k] = $row->$k;
                        }else{
                            $list[$row->$id_name][$v] = $row->$v;
                        }
                    }
                }
            }
        }
        return $list;
    }
}
