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

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

/**
 * Bootstrap class for Zend_Application
 *
 * @uses Zend_Application_Bootstrap_Bootstrap
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
        $this->bootstrap('Session');
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
     * Initialize Entity Manager
     *
     * @return Doctrine\ORM\EntityManager
     */
    protected function _initEntityManager()
    {
        /* set up doctrine2 autoloader */
        Setup::registerAutoloadDirectory(
            realpath(APPLICATION_PATH . '/../library')
        );

        $db = Fisma::$appConf['db'];
        // $db['host'] $db['port']
        return EntityManager::create(
            array(
                'driver'   => 'pdo_mysql',
                'user'     => $db['username'],
                'password' => $db['password'],
                'dbname'   => $db['schema']
            ),
            Setup::createAnnotationMetadataConfiguration(
                array(realpath(APPLICATION_PATH . '/models')),
                APPLICATION_ENV === 'development'
            )
        );
    }

    /**
     * Instantiate a search engine and save it in the registry
     *
     * @access protected
     * @return void
     */
    protected function _initSearchEngine()
    {
        $searchConfig = Fisma::$appConf['search'];

        $searchEngine = new Fisma_Search_Engine($searchConfig['host'], $searchConfig['port'], $searchConfig['path']);

        Zend_Registry::set('search_engine', $searchEngine);
    }

    /**
     * _initRegisterLogger
     *
     * @access protected
     * @return void
     */
    protected function _initRegisterLogger()
    {
        $this->bootstrap('Log');

        $logger = $this->getResource('Log');

        Zend_Registry::set('Zend_Log', $logger);
    }

    /**
     * _initHelperBroker
     *
     * @access protected
     * @return void
     */
    protected function _initHelperBroker()
    {
        Zend_Controller_Action_HelperBroker::addPrefix('Fisma_Zend_Controller_Action_Helper');
        Zend_Controller_Action_HelperBroker::addHelper(new Fisma_Zend_Controller_Action_Helper_ForcedPostRequest);
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
        $view->setEncoding('UTF-8');
        $view->doctype('HTML4_STRICT');
        // Make sure that we don't double encode
        $view->setEscape(array('Fisma', 'htmlentities'));
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        $viewRenderer->setView($view);
        $viewRenderer->setViewSuffix('phtml');
    }

    /**
     * Initialize the File Manager
     *
     * @access protected
     * @return void
     */
    protected function _initFileManager()
    {
        Zend_Registry::set(
            'fileManager',
            new Fisma_FileManager(Fisma::getPath('fileStorage'), new finfo(FILEINFO_MIME))
        );
    }

    /**
     * Instantiate a mail handler
     *
     * @access protected
     * @return void
     */
    protected function _initMailHandler()
    {
        $mailHandler = new Fisma_MailHandler_Queue();

        Zend_Registry::set('mail_handler', $mailHandler);
    }
}
