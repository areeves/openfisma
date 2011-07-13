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
 * Task Controller handles providing information related to background Tasks.
 *
 * @author     Christian Smith <christian.smith@endeavorsystems.com>
 * @copyright  (c) Endeavor Systems, Inc. 2011 {@link http://www.endeavorsystems.com}
 * @license    http://www.openfisma.org/content/license GPLv3
 * @package    Controller
 */

class TaskController extends Fisma_Zend_Controller_Action_Object
{
    /**
     * Store the current userId
     */
    protected $_userId;

    /**
     * Model name
     */
    protected $_modelName = 'Task';

    /**
     * Stores and retrieves the current userID
     *
     * Sets the contexts for actions
     */
    public function init()
    {
        parent::init();
        $this->_userId = CurrentUser::getInstance()->id;
        $this->_helper->fismaContextSwitch()
                      ->setActionContext('status', 'json')
                      ->initContext();
    }

    /**
     * Polled asynchronously by Task JS object to retrieve the number of running and pending processes.
     * When a task is running, information about that specific task is returned.  If a task
     * ID is specified, information about the particular task is returned for the ProgressBar.
     */
    public function statusAction()
    {
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);

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
            $tasksRunning = $tasksRunningQuery->fetchArray();
        } else {
            $tasksRunningQuery = Doctrine_Query::create()
                    ->select()
                    ->from('Task')
                    ->where('userId = ?', $this->_userId)
                    ->andWhere('status = ?', 'running')
                    ->orderBy('id')
                    ->limit(1);
            $tasksRunning = $tasksRunningQuery->fetchArray();
        }
        $array['running'] = $tasksRunning[0];
        $array['count'] = $count;

        $this->view->tasks = $array;
    }

    /**
     * Display list of tasks for the specific user
     */
    public function indexAction()
    {
        $this->_forward('list');
    }
}
