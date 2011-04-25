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
    protected $_functionName;
    protected $_servers;
    protected $_errors;
    protected $_messages;
    protected $_gearmanTable;
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
     * @param integer $id  Gearman table ID passed from the client
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
                break;
            case 'failed':
                $this->_gearmanTable->status = 'failed';
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

    /**
     * @return string
     */
    public function getErrors()
    {
        return $this->_errors;
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
                $this->_errors = $this->returnCode() . ': ' . $this->getErrno() . ': ' . $this->error();
                break;
            }
        }
    }
}
