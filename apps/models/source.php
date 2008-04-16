<?php
/**
* OpenFISMA
*
* MIT LICENSE
*
* @version $Id$
*/

require_once 'Zend/Db/Table.php';

class Source extends Zend_Db_Table
{
    protected $_name = 'FINDING_SOURCES';
    protected $_primary = 'source_id';

}

?>
