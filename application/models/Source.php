<?php
/**
 * Copyright (c) 2008 Endeavor Systems, Inc.
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
 * Source
 * 
 * @author     Ryan Yang <ryan@users.sourceforge.net>
 * @copyright  (c) Endeavor Systems, Inc. 2009 {@link http://www.endeavorsystems.com}
 * @license    http://www.openfisma.org/content/license GPLv3
 * @package    Model
 * @version    $Id$
 */
class Source extends BaseSource
{
    /**
     * preDelete 
     * 
     * @param Doctrine_Event $event 
     * @access public
     * @return void
     */
    public function preDelete($event)
    {
        if (count($this->Findings) > 0) {
            throw new Fisma_Zend_Exception_User('This source cannot be deleted because it is already associated with' .
                ' one or more findings.');
        }
    }
}
