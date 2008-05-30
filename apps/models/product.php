<?php
/**
* OpenFISMA
*
* MIT LICENSE
*
* @version $Id$
*/

require_once 'Zend/Db/Table.php';

class Product extends Zend_Db_Table
{
    protected $_name = 'products';
    protected $_primary = 'id';

}

?>
