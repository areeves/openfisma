<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class AddIrStep extends Doctrine_Migration_Base
{
    public function up()
    {
		$this->createTable('ir_step', array('id' => array('type' => 'integer', 'primary' => true, 'autoincrement' => true, 'length' => 8), 'createdts' => array('notnull' => true, 'type' => 'timestamp', 'length' => 25), 'modifiedts' => array('notnull' => true, 'type' => 'timestamp', 'length' => 25), 'workflowid' => array('type' => 'integer', 'length' => 8), 'roleid' => array('type' => 'integer', 'length' => 8), 'sortorder' => array('type' => 'integer', 'length' => 8), 'name' => array('type' => 'string', 'length' => 255), 'description' => array('type' => 'string', 'extra' =>  array( 'purify' => 'html', ), 'length' => 1000), 'deleted_at' => array('default' => NULL, 'notnull' => false, 'type' => 'timestamp', 'length' => 25)), array('indexes' => array(), 'primary' => array(0 => 'id')));
    }

    public function down()
    {
		$this->dropTable('ir_step');
    }
}