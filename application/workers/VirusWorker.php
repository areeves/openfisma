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
        $this->addFunction("antivirus", array($this, 'antiVirusFunction'));
        $this->setWorkerName('antivirus');
    }

    /**
     * @param  $job  GearmanJob object passed by Gearman
     * @return void
     */
    public function antiVirusFunction($job)
    {
        $this->setup($job);
        $values = $this->_workload;
        $uploadedFile = $values['filepath'];
        $filename = $values['filename'];
        $logger->log('Failed in move_uploaded_file(). ' . $absFile . "\n" . $file['error'], Zend_Log::ERR);
        echo "Job handle: " . $job->handle() . "\n";
        echo "Workload size " . $job->workloadSize() . "\n";
        echo "File: " . $uploadedFile . "\n";

        $this->setProgress('20');
        $config = Fisma::$appConf['gearman'];
        $clamscan = $config['antivirus']['clamscan'];

        if (is_executable($clamscan)) {
            $command = $clamscan . ' --stdout --no-summary ' . escapeshellcmd($uploadedFile);
            exec($command, $avOutput, $avReturnCode);
        } else {
            throw new Fisma_Zend_Exception('clamscan is not an executable binary.');
        }

        $evidence = Doctrine::getTable('Evidence')->findOneByFileName("$filename");

        if (!$evidence) {
            throw new Fisma_Zend_Exception("$filename is not in the evidence table");
        }

        if ($avReturnCode) {
            //Virus discovered
            $evidence->antiVirus = 'failed';
            $this->setSuccess('0');
        } else {
            //No virus discovered
            $evidence->antiVirus = 'passed';
            $this->setSuccess('1');
        }

        $evidence->save();

        $this->setProgress('100');
        $this->setStatus('finished');
    }
}
