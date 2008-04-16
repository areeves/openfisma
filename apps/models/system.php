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

}

?>
