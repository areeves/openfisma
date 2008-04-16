<?php
/**
* OpenFISMA
*
* MIT LICENSE
*
* @version $Id$
*/

require_once 'Zend/Db/Table.php';

class Network extends Zend_Db_Table
{
    protected $_name = 'NETWORKS';
    protected $_primary = 'network_id';

}

?>
