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
 * Bootstrap class for Zend_Application 
 * 
 * @uses Zend_Application_Bootstrap_Bootstrap
 * @version $Id$
 * @copyright (c) Endeavor Systems, Inc. 2009 {@link http://www.endeavorsystems.com}
 * @author Josh Boyd <joshua.boyd@endeavorsystems.com> 
 * @license http://www.openfisma.org/content/license GPLv3
 */
class Bootstrap extends Fisma_Zend_Application_Bootstrap_SymfonyContainerBootstrap
{
    /**
     * Register shutdown function 
     * 
     * @access protected
     * @return void
     */
    protected function _initShutdown()
    {
        register_shutdown_function(array("Zend_Session", "writeClose"), true);
    }

    /**
     * Initialize the error handler 
     * 
     * @access protected
     * @return void
     */
    protected function _initErrorHandler()
    {
        $errorHandler = create_function(
            '$code, $error, $file = NULL, $line = NULL', '
            if (error_reporting() & $code) {
                // This error is not suppressed by current error reporting settings
                // Convert the error into an ErrorException
                throw new ErrorException($error, $code, 0, $file, $line);
            }

            // Do not execute the PHP error handler
            return TRUE;'
        );

        set_error_handler($errorHandler);
    }

    /**
     * Initialize configuration 
     * 
     * @access protected
     * @return void
     */
    protected function _initConfiguration()
    {
        Fisma::setConfiguration(new Fisma_Configuration_Database());
    }

    /**
     * Initialize and connect to the database 
     * 
     * @access protected
     * @return void
     */
    protected function _initDb()
    {
        // Connect to the database
        if (Fisma::mode() != Fisma::RUN_MODE_TEST) {
            $db = Fisma::$_appConf['db'];
        } else {
            $db = Fisma::$_appConf['testdb'];
        }
        $connectString = $db['adapter'] . '://' . $db['username'] . ':' 
                         . $db['password'] . '@' . $db['host'] 
                         . ($db['port'] ? ':' . $db['port'] : '') . '/' . $db['schema'];

        Doctrine_Manager::connection($connectString);
        $manager = Doctrine_Manager::getInstance();
        $manager->setAttribute(Doctrine::ATTR_USE_DQL_CALLBACKS, true);
        $manager->setAttribute(Doctrine::ATTR_USE_NATIVE_ENUM, true);
        $manager->setAttribute(Doctrine::ATTR_AUTOLOAD_TABLE_CLASSES, true);
        $manager->registerValidators(
            array('Fisma_Doctrine_Validator_Ip', 'Fisma_Doctrine_Validator_Url', 'Fisma_Doctrine_Validator_Phone')
        );
        $manager->setAttribute(Doctrine::ATTR_VALIDATE, Doctrine::VALIDATE_CONSTRAINTS);

        /**
         * Set up the cache driver and connect to the manager.
         * Make sure that we only cache in web app mode, and that the application is installed.
         **/
        if (function_exists('apc_fetch') && Fisma::mode() == Fisma::RUN_MODE_WEB_APP) {
            $cacheDriver = new Doctrine_Cache_Apc();
            $manager->setAttribute(Doctrine::ATTR_QUERY_CACHE, $cacheDriver);
        }

        Zend_Registry::set(
            'doctrine_config', 
            array(
                'data_fixtures_path'  =>  Fisma::getPath('fixture'),
                'models_path'         =>  Fisma::getPath('model'),
                'migrations_path'     =>  Fisma::getPath('migration'),
                'yaml_schema_path'    =>  Fisma::getPath('schema'),
                'generate_models_options' => array(
                    'generateTableClasses' => true,
                    'baseClassName' => 'Fisma_Doctrine_Record'
                )
            )
        );
    }

    /**
     * Run Fisma::dispatch()
     *
     *
     * @todo Get everything out of Fisma::dispatch() and into the Bootstrap
     * @access protected
     * @return void
     */
    protected function _initFisma()
    {
        Fisma::dispatch();
    }

    /**
     * Initialize the layout 
     * 
     * @access protected
     * @return void
     */
    protected function _initLayout()
    {
        Zend_Layout::startMvc(
            array(
                'layoutPath' => Fisma::getPath('layout'),
                'view' => new Fisma_Zend_View()
            )
        );
    }

    /**
     * Configure the view 
     * 
     * @access protected
     * @return void
     */
    protected function _initView()
    {
        // Configure the views
        $view = Zend_Layout::getMvcInstance()->getView();
        $view->addHelperPath(Fisma::getPath('viewHelper'), 'View_Helper_');
        $view->addScriptPath(Fisma::getPath('application') . '/modules/default/views/scripts');
        $view->doctype('HTML4_STRICT');
        // Make sure that we don't double encode
        $view->setEscape(array('Fisma', 'htmlentities'));
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        $viewRenderer->setView($view);
        $viewRenderer->setViewSuffix('phtml');
    }
}
