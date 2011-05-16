<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Version106 extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->removeColumn('asset', 'deleted_at');
        $this->removeColumn('network', 'deleted_at');
        $this->removeColumn('product', 'deleted_at');
        $this->removeColumn('role', 'deleted_at');
        $this->removeColumn('source', 'deleted_at');
    }

    public function down()
    {
        $this->addColumn('asset', 'deleted_at', 'timestamp', '25', array(
             'default' => '',
             'notnull' => '',
             ));
        $this->addColumn('network', 'deleted_at', 'timestamp', '25', array(
             'default' => '',
             'notnull' => '',
             ));
        $this->addColumn('product', 'deleted_at', 'timestamp', '25', array(
             'default' => '',
             'notnull' => '',
             ));
        $this->addColumn('role', 'deleted_at', 'timestamp', '25', array(
             'default' => '',
             'notnull' => '',
             ));
        $this->addColumn('source', 'deleted_at', 'timestamp', '25', array(
             'default' => '',
             'notnull' => '',
             ));
    }
}