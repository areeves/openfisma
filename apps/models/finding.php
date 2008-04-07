<?php

require_once 'Zend/Db/Table.php';

class Finding extends Zend_Db_Table
{
    protected $_name = 'FINDINGS';
    protected $_primary = 'finding_id';
}

?>
