<?PHP
/**
* OpenFISMA
*
* MIT LICENSE
*
* @version $ID$
*/

require_once 'Zend/Db/Table.php';

class remediation extends Zend_Db_Table
{
    protected $_name = 'POAMS';
    protected $_primary = 'poam_id';
}
?>
