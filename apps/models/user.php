<?php

require_once 'Zend/Db/Table.php';

class User extends Zend_Db_Table
{
    protected $_name = 'USERS';
    protected $_primary = 'user_id';
}

?>
