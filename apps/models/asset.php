<?php
/**
* OpenFISMA
*
* MIT LICENSE
*
* @version $ID$
*/

require_once 'Zend/Db/Table.php';

class asset extends Zend_Db_Table
{
    protected $_name = 'ASSETS';
    protected $_primary = 'asset_id';

}

?>
