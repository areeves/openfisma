<?php
/**
* OpenFISMA
*
* MIT LICENSE
*
* @version $Id$
*/

require_once 'Zend/Db/Table.php';

/**
 *  POA&M model
 */
class Poams extends Zend_Db_Table
{
    protected $_name = 'POAMS';
    protected $_primary = 'poam_id';
}

?>
