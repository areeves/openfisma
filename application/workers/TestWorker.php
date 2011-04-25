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
 * Gearman Test Worker
 *
 * @author     Christian Smith <christian.smith@endeavorsystems.com>
 * @copyright  (c) Endeavor Systems, Inc. 2011 {@link http://www.endeavorsystems.com}
 * @license    http://www.openfisma.org/content/license GPLv3
 * @package    Controller
 */

class TestWorker extends Fisma_Gearman_Worker
{
    public function __construct()
    {
        parent::__construct();
        $this->addFunction("test", array($this, 'testFunction'));
        $this->setWorkerName('test');
    }

    public function testFunction($job)
    {
        $values = unserialize($job->workload());
        $jobHandle = $job->handle();
        $id = $values['id'];
        $data = $values['data'];
        if (!$data) {
            $data = "blah";
        }
        echo "Id: $id\n";
        echo "Job handle: " . $job->handle() . "\n";
        echo "Date: $data\n";
        $this->setup($id, $jobHandle);
        $this->setStatus('running');
        echo "Workload size " . $job->workloadSize() . "\n";
        echo "Workload:" . $job->workload() . "\n";
        echo strrev($data) . "\n";;
        foreach (range(1,10) as $number) {
            $job->sendStatus($number, 10);
            echo "$number / 10\n";
            sleep(1);
        }
        echo "Finished\n";
        $this->setStatus('finished');
    }
}