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
    protected $_name = 'PRODUCTS';
    protected $_primary = 'prod_id';

}

?>
