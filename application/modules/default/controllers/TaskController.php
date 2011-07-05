<?php

/**
 * Copyright (c) 2011 Endeavor Systems, Inc.
 *
 * This file is part of OpenFISMA.
 *
 * OpenFISMA is free software: you can redistribute it and/or modify it under the terms of the GNU General Public
 * License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later
 * version.
 *
 * OpenFISMA is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License along with OpenFISMA.  If not, see
 * {@link http://www.gnu.org/licenses/}.
 */

/**
 * Task Controller *
 * @author     Christian Smith <christian.smith@endeavorsystems.com>
 * @copyright  (c) Endeavor Systems, Inc. 2011 {@link http://www.endeavorsystems.com}
 * @license    http://www.openfisma.org/content/license GPLv3
 * @package    Controller
 */

class TaskController extends Fisma_Zend_Controller_Action_Object
{
    protected $_userId;

    protected $_modelName = 'Task';

    public function init()
    {
        parent::init();
        $this->_userId = CurrentUser::getInstance()->id;

        $this->_helper->fismaContextSwitch()
                      ->setActionContext('status-data', 'json')
                      ->setActionContext('status-task', 'json')
                      ->initContext();
    }

    public function testAction()
    {
        $values = 'test';
        $client = new Fisma_Gearman_Client;
        $client->doBackground('test', $values);
    }

    /**
     * @return void
     */
    public function tasksAction()
    {
        $client = new Fisma_Gearman_Client();
        $items = array("item1", "item2", "item3", "item4", "item5");

        foreach ($items as $item)
        {
            $client->addTaskBackground('test', $item);
        }
        $client->runTasks();
        $this->_redirect('/gearman/list');

    }

    /**
     * @return void
     */
    public function statusDataAction()
    {
        $this->_helper->layout()->setLayout('ajax');
        $this->_helper->viewRenderer->setNoRender(true);
        $userId = CurrentUser::getInstance()->id;

        /*
        $query = Doctrine_Query::create()
                select("IFNULL(pending,0) AS pending, IFNULL(running,0) AS running, IFNULL(finished,0) AS finished, IFNULL(failed,0) AS failed")
                FROM (SELECT COUNT(status) AS pending FROM Task where status='pending') AS t1,
                (SELECT COUNT(status) AS running FROM Task WHERE status='running') AS t2,
                (SELECT COUNT(status) AS finished FROM Task WHERE status='finished') AS t3,
                (SELECT count(status) AS failed FROM Task WHERE status='failed') AS t4"
        )
        */
        $query = Doctrine_Query::create()
                ->select('status, count(*)')
                ->from('Task')
                ->whereIn('status', array('failed', 'pending', 'running', 'finished'))
                ->andWhere('userid = ?', $this->_userId)
                ->groupBy('status');
        $statusCount = $query->fetchArray();

        foreach ($statusCount as $status) {
           $count[$status['status']] = $status['count'];
        }

        foreach (array('failed', 'pending', 'running', 'finished') as $value) {
            if (!isset($count[$value])) {
                $count[$value] = 0;
            }
        }

        if ($id = $this->_request->getParam('id')) {
            $tasksRunningQuery = Doctrine_Query::create()
                    ->select()
                    ->from('Task')
                    ->where('userId = ?', $this->_userId)
                    ->andWhere('id = ?', $id)
                    ->orderBy('id')
                    ->limit(1);
            $running = $tasksRunningQuery->fetchArray();
        } else {
            $tasksRunningQuery = Doctrine_Query::create()
                    ->select()
                    ->from('Task')
                    ->where('userId = ?', $this->_userId)
                    ->andWhere('status = ?', 'running')
                    ->orderBy('id')
                    ->limit(1);
            $running = $tasksRunningQuery->fetchArray();
        }
        $array['running'] = $running[0];
        $array['count'] = $count;
        $this->view->tasks = $array;
    }

    /**
     * @return void
     */
    public function indexAction()
    {
        $this->_forward('list');
    }
}
