<?php

/**
 * Source
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 5441 2009-01-30 22:58:43Z jwage $
 */
class Source extends BaseSource
{
    public function preSave()
    {
        Doctrine_Manager::connection()->beginTransaction();   
    }
    
    public function preDelete()
    {
         Doctrine_Manager::connection()->beginTransaction();       
    }

    public function postInsert()
    {
        $notification = new Notification();
        $notification->add(Notification::SOURCE_CREATED, $this, User::currentUser());
        Doctrine_Manager::connection()->commit();

        Fisma_Lucene::updateIndex('source', $this);
    }

    public function postUpdate()
    {
        $notification = new Notification();
        $notification->add(Notification::SOURCE_MODIFIED, $this, User::currentUser());
        Doctrine_Manager::connection()->commit();

        Fisma_Lucene::updateIndex('source', $this);
    }

    public function postDelete()
    {
        $notification = new Notification();
        $notification->add(Notification::SOURCE_DELETED, $this, User::currentUser());
        Doctrine_Manager::connection()->commit();

        Fisma_Lucene::deleteIndex('source', $this->id);

    }
}
