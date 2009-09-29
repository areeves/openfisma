<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class AddIrClonedIncident extends Doctrine_Migration_Base
{
    public function up()
    {
		$this->createTable('ir_cloned_incident', array('id' => array('type' => 'integer', 'primary' => true, 'autoincrement' => true, 'length' => 8), 'origincidentid' => array('type' => 'integer', 'length' => 8), 'cloneincidentid' => array('type' => 'integer', 'length' => 8), 'createdts' => array('type' => 'timestamp', 'length' => 25), 'userid' => array('type' => 'integer', 'length' => 8), 'comments' => array('type' => 'string', 'length' => 1000)), array('indexes' => array(), 'primary' => array(0 => 'id')));
    }

    public function down()
    {
		$this->dropTable('ir_cloned_incident');
    }
}
