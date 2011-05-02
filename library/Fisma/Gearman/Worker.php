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
     * Gearman model
     *
     * @var object
     */
    protected $_gearmanTable;

    /**
     * Name of the current worker
     *
     * @var string
     */
    protected $_workerName;

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
     * @param integer $id Gearman table ID passed from the client
     * @param string $handle GearmanJob jobhandle
     * @return void
     */
    protected function setup($id, $handle)
    {
        $gearmanTable = Doctrine::getTable('Gearman')->find($id);
        if (!$gearmanTable) {
            throw new Fisma_Zend_Exception("Invalid Gearman ID");
        }
        $gearmanTable->worker = $this->getWorkerName();
        $gearmanTable->jobHandle = $handle;
        $gearmanTable->save();
        $this->_gearmanTable = $gearmanTable;
        $this->setStatus('running');
    }

    /**
     * Set the current status of the worker in the Gearman Table
     * @throws Fisma_Zend_Exception
     * @param  string $status Set the status condition
     * @return void
     */
    public function setStatus($status)
    {
        switch (strtolower($status)) {
            case 'pending':
                $this->_gearmanTable->status = 'pending';
                break;
            case 'running':
                $this->_gearmanTable->startedTs = Fisma::now();
                $this->_gearmanTable->status = 'running';
                break;
            case 'finished':
                $this->_gearmanTable->finishedTs = Fisma::now();
                $this->_gearmanTable->status = 'finished';
                $this->_gearmanTable->messages = json_encode($this->getMessages());
                $this->_gearmanTable->errors = json_encode($this->getErrors());
                break;
            case 'failed':
                $this->_gearmanTable->finishedTs = Fisma::now();
                $this->_gearmanTable->status = 'failed';
                $this->_gearmanTable->messages = json_encode($this->getMessages());
                $this->_gearmanTable->errors = json_encode($this->getErrors());
                break;
            default:
                throw new Fisma_Zend_Exception('Invalid status');
                break;
        }
        $this->_gearmanTable->save();
    }

    /**
     * @param string $name Set worker name
     * @return void
     */
    public function setWorkerName($name)
    {
        $this->_workerName = $name;
    }

    /**
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
     * @return string
     */
    public function getMessages()
    {
        return $this->_messages;
    }

    /**
     * @param  $error
     * @return void
     */
    public function addError($error)
    {
        array_push($this->_errors, $error);
    }

    /**
     * @return string
     */
    public function getErrors()
    {
        return $this->_errors;
    }

    public function setProgress($progress)
    {
        if (is_numeric($progress)) {
            $this->_gearmanTable->progress = $progress;
            $this->_gearmanTable->save();
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
        $this->_gearmanTable->success = (bool) $success;
    }

    /**
     * Worker run loop
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
