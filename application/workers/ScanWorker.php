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
 * Gearman Scan Worker
 *
 * @author     Christian Smith <christian.smith@endeavorsystems.com>
 * @copyright  (c) Endeavor Systems, Inc. 2011 {@link http://www.endeavorsystems.com}
 * @license    http://www.openfisma.org/content/license GPLv3
 * @package    Controller
 */

class ScanWorker extends Fisma_Gearman_Worker
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->addFunction('scan', array($this, 'scanFunction'));
        $this->setWorkerName('injection');
    }

    /**
     * @param  $job GearmanJob objected passed by Gearman
     * @return void
     */
    public function scanFunction($job)
    {
        $values = unserialize($job->workload());
        $id = $values['id'];
        unset($values['id']);
        $userId = $values['userid'];
        unset($values['userid']);
        $jobHandle = $job->handle();
        $this->setup($id, $jobHandle);
        $totalCount = count($params);
        $currentCount = 1;
        $filePath = $values['filepath'];

        echo "Job handle: " . $job->handle() . "\n";
        echo "Workload size " . $job->workloadSize() . "\n";
        echo "FilePath: $filePath\n";
        $this->setProgress('10');

        try {
            $plugin = Fisma_Inject_Factory::create(NULL, $values);
            // get original file name
            $originalName = pathinfo(basename($filePath), PATHINFO_FILENAME);
            // get current time and set to a format like '20090504_112202'
            $dateTime = Zend_Date::now()->toString(Fisma_Date::FORMAT_FILENAME_DATETIMESTAMP);
            // define new file name
            $newName = str_replace($originalName, $originalName . '_' . $dateTime, basename($filePath));
            // organize upload data
            $upload = new Upload();
            $upload->userId = $userId;
            $upload->fileName = $newName;
            $upload->save();
            $this->setProgress('60');
            // parse the file
            $plugin->parse($upload->id);
            // rename the file by ts
            rename($filePath, dirname($filePath) . '/' . $newName);

            $createdWord = ($plugin->created > 1 || $plugin->created === 0)
                    ?  ' vulnerabilities were' : ' vulnerability was' ;
            $reopenedWord = ($plugin->reopened > 1 || $plugin->reopened  === 0)
                    ? ' vulnerabilities were' : ' vulnerability was' ;
            $suppressedWord = ($plugin->suppressed > 1 || $plugin->suppressed === 0)
                    ? ' vulnerabilities were' : ' vulnerability was' ;

            $messages = 'Your scan report was successfully uploaded.<br>'
                        . $plugin->created . $createdWord . ' created.<br>'
                        . $plugin->reopened . $reopenedWord . ' reopened.<br>'
                        . $plugin->suppressed . $suppressedWord . ' suppressed.';
            $this->addMessage($messages);

            echo "Finished processing: $filePath";
            $this->setProgress('100');
            $this->setSuccess('1');
            $this->setStatus('finished');
        } catch (Fisma_Zend_Exception_InvalidFileFormat $e) {
            $this->addError($e->getMessage());
            $this->setSuccess('0');
            $this->setStatus('failed');
        }
    }
}
