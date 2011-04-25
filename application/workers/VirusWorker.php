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
 * Gearman Virus Worker
 *
 * @author     Christian Smith <christian.smith@endeavorsystems.com>
 * @copyright  (c) Endeavor Systems, Inc. 2011 {@link http://www.endeavorsystems.com}
 * @license    http://www.openfisma.org/content/license GPLv3
 * @package    Controller
 */

class VirusWorker extends Fisma_Gearman_Worker
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->addFunction("virus", array($this, 'virusFunction'));
        $this->setWorkerName('antivirus');
    }

    /**
     * @param  $job  GearmanJob object passed by Gearman
     * @return void
     */
    public function virusFunction($job)
    {
        $values = unserialize($job->workload());
        $id = $values['id'];
        $jobHandle = $job->handle();
        $uploadedFile = $values['uploadedFile'];
        $this->setup($id, $jobHandle);

        $job->sendStatus(1, 2);
        echo "Job handle: " . $job->handle() . "\n";
        echo "Workload size " . $job->workloadSize() . "\n";
        echo "File: " . $uploadedFile;

        $config = Fisma::$appConf['gearman'];
        $clamscan = $config['antivirus']['clamscan'];

        $command = $clamscan . ' --stdout --no-summary ' . escapeshellcmd($uploadedFile);
        exec($command, $avOutput, $avReturnCode);

        if ($avReturnCode) {
            //Virus discovered
            $gearman->success = '0';
        } else {
            //No virus discovered
            $gearman->success = '1';
        }

        $job->sendStatus(2, 2);
        $this->setStatus('finished');
    }
}