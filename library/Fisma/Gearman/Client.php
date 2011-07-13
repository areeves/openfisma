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
 * Gearman Client
 *
 * @author     Christian Smith <christian.smith@endeavorsystems.com>
 * @copyright  (c) Endeavor Systems, Inc. 2011 {@link http://www.endeavorsystems.com}
 * @license    http://www.openfisma.org/content/license GPLv3
 * @package    Controller
 */

class Fisma_Gearman_Client extends GearmanClient
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $config = Fisma::$appConf['gearman'];
        $this->addServer($config['server'], $config['port']);
    }

    /**
     * Setup the Task model, return the ID
     * @return integer Task ID
     */
    public function setup($worker)
    {
        $task = new Task();
        $task->userId = CurrentUser::getInstance()->id;
        $task->worker = $worker;
        $task->save();
        return $task->id;
    }

    /**
     * Override to setup and add entries to the Task table
     *
     * @param $worker Name of the worker
     * @param $data Data to be passed to the worker
     * @param null $unique A unique ID used to identify a particular task
     */
    public function doBackground($worker, $data, $unique = null)
    {
        $workerData['id'] = $this->setup($worker);
        $workerData['data'] = $data;
        parent::doBackground($worker, serialize($workerData), $unique);
    }

    /**
     * Override to setup and add entries to the Task table,
     * returning the ID of the table which is stored in the array passed to the worker.
     * Data needed for the process is also stored and passed to the worker.
     *
     * @param $worker Name of the worker
     * @param $data Data to be passed to the worker
     * @param null $context Application context to associate with a task
     * @param null $unique A unique ID used to identify a particular task

     */
    public function addTaskBackground($worker, $data, $context = null, $unique = null)
    {
        $workerData['id'] = $this->setup($worker);
        $workerData['data'] = $data;
        parent::addTaskBackground($worker, serialize($workerData), $context, $unique);
    }
}
