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
 * @author     Christian Smith <christian.smith@endeavorsystems.com>
 * @copyright  (c) Endeavor Systems, Inc. 2011 {@link http://www.endeavorsystems.com}
 * @license    http://www.openfisma.org/content/license GPLv3
 * @package    View_Helper
 */

class View_Helper_TaskProgressLauncher extends Zend_View_Helper_Abstract
{
    /**
     * Kick-off the Task ProgressBar if a user has a task running
     *
     * @return string
     */
    public function taskProgressLauncher()
    {
        $userId = CurrentUser::getInstance()->id;
        $runningCountQuery = Doctrine_Query::create()
                ->from('Task')
                ->where('userId = ?', $userId)
                ->andWhere('status = ?', 'running')
                ->orderBy('id');
        $runningCount = $runningCountQuery->fetchArray();

        if ($runningCount) {
            return '<script type="text/javascript">Fisma.Task.start();</script>';
        } else {
            return '';
        }
    }
}