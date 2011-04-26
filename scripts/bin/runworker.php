#!/usr/bin/env php
<?php
/**
 * Copyright (c) 2010 Endeavor Systems, Inc.
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

require_once(realpath(dirname(__FILE__) . '/bootstrap.php'));

$workerName = ucfirst(strtolower($argv['1'])) . 'Worker';
$workerFile = APPLICATION_PATH . '/workers/' . $workerName . '.php';
echo "WorkerName: $workerName\n";
echo "WorkerFile: $workerFile\n";

if (!file_exists($workerFile)) {
    throw new Fisma_Zend_Exception("No worker found");
}

require $workerFile;

$worker = new $workerName;
$worker->run();
