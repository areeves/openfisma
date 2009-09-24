<?php
/**
 * Copyright (c) 2009 Endeavor Systems, Inc.
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
 * @author    Josh Boyd <joshua.boyd@endeavorsystems.com> 
 * @copyright (c) Endeavor Systems, Inc. 2009 (http://www.endeavorsystems.com)
 * @license   http://openfisma.org/content/license 
 * @version   $Id$
 * @package   Fisma_Loader
 */

require_once (realpath(dirname(__FILE__) . '/../../public/phploader/loader.php'));

/**
 * Loader class for loading JS and CSS via the YUI phploader library 
 *
 * @copyright (c) Endeavor Systems, Inc. 2009 (http://www.endeavorsystems.com)
 * @license    http://www.openfisma.org/mw/index.php?title=License
 * @package    Fisma_Loader
 */
class Fisma_Loader
{
    private $_appVersion;
    private $_yuiVersion;
    private $_debug;
    private $_loader;
    private $_config;

    /**
     * Set up and initialize the phploader object
     *
     * @param array $config Custom module metadata set for YUI phploader
     * @todo Change to use local combo loader
     */
    public function __construct($config = NULL)
    {
        $this->_appVersion   = Fisma::version();
        $this->_yuiVersion   = Fisma::yuiVersion();
        $this->_debug        = Fisma::debug();

        if(is_null($config)) {
            $this->_loader = new YAHOO_util_Loader($this->_yuiVersion);
        }
        else {
            $this->_prepareConfig($config);
            $this->_loader = new YAHOO_util_Loader($this->_yuiVersion, 
                'custom_config_'.$this->_appVersion, $this->_config);
        }

        if($this->_debug) {
            $this->_loader->allowRollups = FALSE;
            $this->_loader->filter       = YUI_DEBUG;
            $this->_loader->combine      = FALSE;
        }
        else {
            $this->_loader->allowRollups = TRUE;
            $this->_loader->combine      = TRUE;
        }
    }

    /**
     * Load components into the YUI phploader
     *
     * @param array $components Array of components to load
     */
    public function load($components)
    {
        foreach($components as $component)
        {
            $this->_loader->load($component);
        }
    }

    /**
     * Wrapper for phploader script tags
     *
     * @return string Script tags
     */
    public function script()
    {
        return $this->_loader->script();
    }

    /**
     * Wrapper for phploader CSS link tags
     *
     * @return string CSS link tags
     */
    public function css()
    {
        return $this->_loader->css();
    }

    /**
     * toString method Wrapper for script() and css()
     *
     * @return string
     */
    public function __toString()
    {
        $script = $this->script();
        $css    = $this->css();
        return $script . $css;
    }

    /**
     * Sets custom configuration for JS/CSS specific to the application
     *
     * @param array Array of basic custom configuration information
     * @return array Custom configuration array for application JS/CSS
     */
    private function _prepareConfig($config)
    {
        // @todo Parse array into phploader correct array
        // @todo Grab debug level, put -min into script names if not in debug

        $this->_config = $config;
    }
}
