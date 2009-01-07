<?php
/**
 * Copyright (c) 2008 Endeavor Systems, Inc.
 *
 * This file is part of OpenFISMA.
 *
 * OpenFISMA is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OpenFISMA is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OpenFISMA.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author    Woody Lee <woody712@users.sourceforge.net>
 * @copyright (c) Endeavor Systems, Inc. 2008 (http://www.endeavorsystems.com)
 * @license   http://www.openfisma.org/mw/index.php?title=License
 * @version   $Id$
 */

/**
 * @see Zend_Controller_Action_Helper_Abstract
 */
require_once 'Zend/View/Helper/Abstract.php';

/**
 * Helper for determining if the specified poam is on time. 
 *
 */
class View_Helper_IsOnTime extends Zend_View_Helper_Abstract
{
    /**
     * To determind if the specified poam is on time. 
     * Return 'N/A','Overdue' or 'On Time'.
     *
     * @param string $dueTime overdue time
     * @return string 'on time',' overdue','N/A'
     */
    public function IsOnTime($dueTime)
    {
        $dueTime = strtotime($dueTime);
        if ($dueTime == false || $dueTime == -1) {
            return 'N/A';
        } elseif ($dueTime < Config_Fisma::now()) {
            return 'Overdue';
        } else {
            return 'On Time';
        }
    } 
}
