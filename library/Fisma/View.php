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
 * Overridden version of Zend_View to enable the escaping of variables passed into a view.
 * 
 * @author     Andrew Reeves <andrew.reeves@endeavorsystems.com>
 * @copyright  (c) Endeavor Systems, Inc. 2009 {@link http://www.endeavorsystems.com}
 * @license    http://www.openfisma.org/content/license GPLv3
 * @package    Fisma
 * @version    $Id$
 */
class Fisma_View extends Zend_View
{
    /**
     * Whether or not to enable autoescaping (default: true)
     * @var bool
     */
    public $autoEscape = true;

    /**
     * Raw values set when $autoEscape == true.  Value is an array
     * of variableName => originalValue pairs.
     * @var array
     */
    protected $_raw = null;

    /**
     * Includes the view script in a scope with only public $this variables.
     *
     * @param string The view script to execute.
     */
    protected function _run()
    {
        $varBackup = null;
        if ($this->autoEscape) {
            $vars = $this->getVars();
            $varBackup = $vars;
            array_walk_recursive($vars, array($this,'deepEscape'));
            $this->_raw = $varBackup;
        }
 
        parent::_run(func_get_arg(0));
 
        if($varBackup !== null)
            $this->assign($varBackup);
    }

    /**
     * Callback for array_walk_recursive to escape view input
     *
     * @param mixed Input value, passed by reference to be modified.
     * @param mixed Key of passed input value.
     * @param string A message string passed by reference onto which we append error messages.
     * @return bool true on success, false otherwise.
     */
    protected function deepEscape(&$input, $key)
    {
        if (is_string($input)) {
            $input = $this->escape($input);
        }
        return true;
    }

}
