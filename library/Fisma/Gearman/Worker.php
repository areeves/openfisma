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
 * Gearman Worker
 *
 * @author     Christian Smith <christian.smith@endeavorsystems.com>
 * @copyright  (c) Endeavor Systems, Inc. 2011 {@link http://www.endeavorsystems.com}
 * @license    http://www.openfisma.org/content/license GPLv3
 * @package    Controller
 */

class Fisma_Gearman_Worker extends GearmanWorker
{
    /**
     * Error messages
     *
     * @var array
     */
    protected $_errors = array();

    /**
     * Messages to be passed back to the application
     *
     * @var array
     */
    protected $_messages = array();

    /**
     * Task model
     *
     * @var object Task model that we will be working with
     */
    protected $_task;

    /**
     * Name of the current worker
     *
     * @var string Name of the worker
     */
    protected $_workerName;

    /**
     * ID of the database row the Worker should be using
     *
     * @var integer Current Task model ID
     */
    protected $_id;

    /**
     * @var
     */
    protected $_workload;

    /**
     * Current job handle identifier
     *
     * @var string JobHandle identifer
     */
    protected $_jobHandle;

    /**
     * Constructor
     */
    public function  __construct()
    {
        parent::__construct();
        $config = Fisma::$appConf['gearman'];
        $this->addServer($config['server'], $config['port']);
    }

    /**
     * @param integer $id Task ID passed from the client
     * @param string $handle GearmanJob jobhandle
     * @return void
     */
    protected function setup($job)
    {
        $workload = unserialize($job->workload());
        $this->_id = $workload['id'];
        $this->_jobHandle = $job->handle();
        $this->_workload = $workload['data'];
        $task = Doctrine::getTable('Task')->find($this->_id);
        if (!$task) {
            throw new Fisma_Zend_Exception("Invalid task ID");
        }
        $task->worker = $this->getWorkerName();
        $task->jobHandle = $this->_jobHandle;
        $task->save();
        $this->_task = $task;
        $this->setStatus('running');
    }

    /**
     * Set the current status of the worker in the Task model
     * @throws Fisma_Zend_Exception
     * @param  string $status Set the status condition
     * @return void
     */
    public function setStatus($status)
    {
        $this->_task->status = $status;
        if ($status === 'finished' || $status === 'failed') {
            $this->_task->messages = json_encode($this->getMessages());
            $this->_task->errors = json_encode($this->getErrors());
        }
        $this->_task->save();
    }

    /**
     * Set the worker name
     *
     * @param string $name Worker name
     * @return void
     */
    public function setWorkerName($name)
    {
        $this->_workerName = $name;
    }

    /**
     * Get worker name
     *
     * @return string Get worker name
     */
    public function getWorkerName()
    {
        return $this->_workerName;
    }

    public function addMessage($message)
    {
        array_push($this->_messages, $message);
    }

    /**
     * Retrieve messages
     *
     * @return string
     */
    public function getMessages()
    {
        return $this->_messages;
    }

    /**
     * Add error messages
     *
     * @param string $error
     * @return void
     */
    public function addError($error)
    {
        array_push($this->_errors, $error);
    }

    /**
     * Retrieve error messages
     *
     * @return string
     */
    public function getErrors()
    {
        return $this->_errors;
    }

    /**
     * Set progress value (usually between 1 and 100)
     *
     * @param integer $progress Progress value
     * @return void
     */
    public function setProgress($progress)
    {
        if (is_numeric($progress)) {
            $this->_task->progress = $progress;
            $this->_task->save();
        }
    }

    /**
     * Set the return code of the process
     *
     * @param  $success True or false
     * @return void
     */
    public function setSuccess($success)
    {
        $this->_task->success = (bool) $success;
    }

    /**
     * Worker run loop
     *
     * @return void
     */
    public function run()
    {
        while ($this->work())  {
            if ($this->returnCode() != GEARMAN_SUCCESS)
            {
                $error = $this->returnCode() . ': ' . $this->getErrno() . ': ' . $this->error();
                $this->addError($error);
                break;
            }
        }
    }
}
