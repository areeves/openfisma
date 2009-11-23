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
 * <http://www.gnu.org/licenses/>.
 */

/**
 * An object which represents a menubar in YUI
 * 
 * @author     Mark E. Haase <mhaase@endeavorsystems.com>
 * @copyright  (c) Endeavor Systems, Inc. 2009 (http://www.endeavorsystems.com)
 * @license    http://www.openfisma.org/content/license
 * @package    Fisma
 * @subpackage Fisma_Yui
 * @version    $Id$
 */
class Fisma_Yui_MenuBar
{
    /**
     * Internal representation of the items in this menu bar
     * @var array
     */
    private $_menus = array();
    
    /**
     * Add the specified item to this menu bar.
     * 
     * @param Fisma_Yui_Menu|Fisma_Yui_MenuItem $item
     */
    function add($item) 
    {
        if ($item instanceof Fisma_Yui_Menu || $item instanceof Fisma_Yui_MenuItem) {
            $this->_menus[] = $item;
        } else {
            throw new Fisma_Exception("Can only add Menus and MenuItems to this class.");
        }
    }
    
    /**
     * Return the menus
     * 
     * @return array
     */
    function getMenus() 
    {
        return $this->_menus;
    }
}