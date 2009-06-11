<?php

/**
 * BaseOrganization
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property timestamp $createdTs
 * @property timestamp $modifiedTs
 * @property string $name
 * @property string $nickname
 * @property enum $orgType
 * @property integer $systemId
 * @property string $description
 * @property System $System
 * @property Doctrine_Collection $Users
 * @property Doctrine_Collection $Findings
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 5441 2009-01-30 22:58:43Z jwage $
 */
abstract class BaseOrganization extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('organization');
        $this->hasColumn('createdTs', 'timestamp', null, array('type' => 'timestamp'));
        $this->hasColumn('modifiedTs', 'timestamp', null, array('type' => 'timestamp'));
        $this->hasColumn('name', 'string', 255, array('type' => 'string', 'length' => '255'));
        $this->hasColumn('nickname', 'string', 255, array('type' => 'string', 'unique' => 'true;', 'length' => '255'));
        $this->hasColumn('orgType', 'enum', null, array('type' => 'enum', 'values' => array(0 => 'agency', 1 => 'bureau', 2 => 'organization', 3 => 'system'), 'length' => ''));
        $this->hasColumn('systemId', 'integer', null, array('type' => 'integer'));
        $this->hasColumn('description', 'string', 255, array('type' => 'string', 'length' => '255'));
    }

    public function setUp()
    {
        $this->hasOne('System', array('local' => 'systemId',
                                      'foreign' => 'id'));

        $this->hasMany('User as Users', array('refClass' => 'UserOrganization',
                                              'local' => 'organizationId',
                                              'foreign' => 'userId'));

        $this->hasMany('Finding as Findings', array('local' => 'id',
                                                    'foreign' => 'responsibleOrganizationId'));

        $nestedset0 = new Doctrine_Template_NestedSet();
        $softdelete0 = new Doctrine_Template_SoftDelete();
        $timestampable0 = new Doctrine_Template_Timestampable(array('created' => array('name' => 'createdTs', 'type' => 'timestamp'), 'updated' => array('name' => 'modifiedTs', 'type' => 'timestamp')));
        $this->actAs($nestedset0);
        $this->actAs($softdelete0);
        $this->actAs($timestampable0);
    }
}