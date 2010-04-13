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
 * Provides several different debugging facilities.
 *
 * @author     Mark E. Haase <mhaase@endeavorsystems.com>
 * @copyright  (c) Endeavor Systems, Inc. 2009 {@link http://www.endeavorsystems.com}
 * @license    http://www.openfisma.org/content/license GPLv3
 * @package    Controller
 * @version    $Id$
 */
class DebugController extends Zend_Controller_Action
{
    /**
     * Prepares actions
     *
     * @return void
     * @throws Fisma_Exception if Debug mode is not enabled
     */
    public function preDispatch()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        if (!Fisma::debug())
            throw new Fisma_Exception('Action is only allowed in debug mode.');
    }

    /**
     * Display phpinfo()
     * 
     * @return void
     */
    public function phpinfoAction()
    {
        phpinfo();
    }

    /**
     * Display error log
     *
     * @return void
     */
    public function errorlogAction()
    {
        echo file_get_contents('../data/logs/error.log');
    }

    /**
     * Display php log
     *
     * @return void
     */
    public function phplogAction()
    {
        echo file_get_contents('../data/logs/php.log');
    }
    
    /**
     * Display APC system cache info
     */
    public function apcCacheAction()
    {
        $this->_helper->layout()->enableLayout();
        $this->_helper->viewRenderer->setNoRender(false);
        $this->_helper->actionStack('header', 'panel');
        
        // Cache type can be 'system' or 'user'. Defaults to 'system'.
        $cacheType = $this->getRequest()->getParam('type');
        
        if (!$cacheType) {
            $cacheType = 'system';
        }

        switch ($cacheType) {
            case 'system':
                $cacheInfo = apc_cache_info();
                break;
            case 'user':
                $cacheInfo = apc_cache_info('user');
                break;
            default:
                throw new Fisma_Exception("Invalid cache type: '$cacheType'");
                break;
        }

        // Cache info contains summary data and line item data. Separate these into two view variables for clarity.
        $cacheItems = $cacheInfo['cache_list'];
        unset($cacheInfo['cache_list']);
        
        $this->view->cacheType = ucfirst(htmlspecialchars($cacheType));
        $this->view->cacheSummary = $cacheInfo;

        if (count($cacheItems) > 0) {
            $this->view->cacheItemHeaders = array_keys($cacheItems[0]);
            $this->view->cacheItems = $cacheItems;
        }
    }
}
