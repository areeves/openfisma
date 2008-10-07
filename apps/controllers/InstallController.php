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
 * @author    Jim Chen <xhorse@users.sourceforge.net>
 * @copyright (c) Endeavor Systems, Inc. 2008 (http://www.endeavorsystems.com)
 * @license   http://www.openfisma.org/mw/index.php?title=License
 * @version   $Id$
 */

/**
 * The install controller handles all of the actions for the installer program.
 *
 * @package   Controller
 * @copyright (c) Endeavor Systems, Inc. 2008 (http://www.endeavorsystems.com)
 * @license   http://www.openfisma.org/mw/index.php?title=License
 */
class InstallController extends Zend_Controller_Action
{
    public function preDispatch()
    {
        $this->_helper->layout->setLayout('install');
        //Judge if there is necessary to install
        
    }
    public function indexAction()
    {
        $this->view->back = '';
        $this->view->next = '/install/envcheck';
        $this->render();
    }
    public function envcheckAction()
    {
        define('REQUEST_PHP_VERSION', '5');
        $this->view->back = '/install';
        if (version_compare(phpversion(), REQUEST_PHP_VERSION, '>')) {
            $this->view->next = '/install/checking';
            $this->view->checklist = array(
                'version' => 'ok'
            );
        } else {
            $this->view->checklist = array(
                'version' => 'failure'
            );
            $this->view->next = '';
        }
        $this->render();
    }
    public function checkingAction()
    {
        $wDirectories = array(
            WEB_ROOT . DS . 'temp',
            ROOT . DS . 'log',
            WEB_ROOT . DS . 'evidence',
            CONFIGS . DS . CONFIGFILE_NAME
        );
        $notwritables = array();
        foreach ($wDirectories as $k => $wok) {
            if (!is_writeable($wok)) {
                array_push($notwritables, $wok);
                unset($wDirectories[$k]);
            }
        }
        $this->view->notwritables = $notwritables;
        $this->view->writables = $wDirectories;
        $this->view->back = '/install/envcheck';
        if (empty($notwritables)) {
            $this->view->next = '/install/dbsetting';
        } else {
            $this->view->next = '';
        }
        $this->render();
    }
    public function dbsettingAction()
    {
        $this->view->installpath = dirname(dirname(dirname(__FILE__)));
        $this->view->dsn = array(
            'host' => 'localhost',
            'port' => '3306'
        );
        $this->view->title = 'General settings';
        $this->view->back = '/install/checking';
        $this->view->next = '/install/dbreview';
        $this->render();
    }
    public function dbreviewAction()
    {
        $dsn = $this->_getParam('dsn');
        if (empty($dsn['name_c'])
            && empty($dsn['pass_c'])
            && empty($dsn['pass_c_ag'])) {
            $dsn['name_c'] = $dsn['uname'];
            $dsn['pass_c'] = $dsn['upass'];
            $dsn['pass_c_ag'] = $dsn['upass'];
        }
        $passwordCompare = array(
            $dsn['pass_c']
        );
        $filter = array(
            '*' => array(
                'StringTrim',
                'stripTags'
            )
        );
        $validator = array(
            'type' => 'Alnum',
            'host' => array(
                'NotEmpty',
                new Zend_Validate_Hostname(
                        Zend_Validate_Hostname::ALLOW_LOCAL | 
                        Zend_Validate_Hostname::ALLOW_IP 
                    )
            ) ,
            'port' => array(
                'Int',
                new Zend_Validate_Between(0, 65535)
            ) ,
            'uname' => 'NotEmpty',
            'upass' => 'NotEmpty',
            'dbname' => 'NotEmpty',
            'name_c' => 'NotEmpty',
            'pass_c' => 'NotEmpty',
            'pass_c_ag' => array(
                'NotEmpty',
                new Zend_Validate_InArray($passwordCompare)
            )
        );
        $fv = new Zend_Filter_Input($filter, $validator);
        $input = $fv->setData($dsn);
        $this->view->title = 'General settings';
        $this->view->dsn = $dsn;
        if ($input->hasInvalid() || $input->hasMissing()) {
            $message = $input->getMessages();
            $this->view->back = '/install/checking';
            $this->view->next = '/install/dbreview';
            $this->view->message = $message;
            $this->render('dbsetting');
        } else {
            $this->view->back = '/install/dbsetting';
            $this->view->next = '/install/initial';
            $this->render();
        }
    }
    public function initialAction()
    {
        $dsn = $this->_getParam('dsn');
        $checklist = array(
            'connection' => 'failure',
            'creation' => 'failure',
            'grant' => 'failure',
            'schema' => 'failure',
            'savingconfig' => 'failure'
        );
        $method = 'connection / creation';
        $errMessage = '';
        $ret = false;
        if (mysql_connect($dsn['host'] . ':' . $dsn['port'], $dsn['uname'],
            $dsn['upass'])) {
            if (mysql_select_db($dsn['dbname'])) {
                $method = 'connection';
                $checklist['connection'] = 'ok';
                $ret = true;
            } elseif (mysql_query("CREATE DATABASE `{$dsn['dbname']}`;")) {
                $method = 'creation';
                $checklist['creation'] = 'ok';
                $ret = true;
            } else {
                $errMessage.= mysql_error();
            }
            if ($ret && ($dsn['uname'] != $dsn['name_c'])) {
                $host = ('localhost' == strtolower($dsn['host'])) ?
                    'localhost' : '%';
                $qry = "GRANT ALL PRIVILEGES ON `{$dsn['dbname']}`. * TO
                       '{$dsn['name_c']}'@'{$host}'
                       IDENTIFIED BY '{$dsn['pass_c']}' WITH GRANT OPTION";
                if (TRUE == ($ret = mysql_query($qry))) {
                    $checklist['grant'] = 'ok';
                } else {
                    $errMessage.= mysql_error();
                }
            }
            if ($ret) {
                require_once(CONTROLLERS . '/components/sqlimport.php');
                $zendDsn = array(
                    'adapter' => 'mysqli',
                    'params' => array(
                        'host' => $dsn['host'],
                        'port' => $dsn['port'],
                        'username' => $dsn['name_c'],
                        'password' => $dsn['pass_c'],
                        'dbname' => $dsn['dbname'],
                        'profiler' => false
                    )
                );
                try {
                    $db = Zend_Db::factory(new Zend_Config($zendDsn));
                    $initFiles = array(MIGRATIONS . '/base.sql');
                    if ($ret = import_data($db, $initFiles)) {
                        $checklist['schema'] = 'ok';
                    }
                }
                catch(Zend_Exception $e) {
                    $errMessage.= $e->getMessage();
                    $ret = false;
                }
            }
        } else {
            $errMessage.= mysql_error();
        }
        $this->view->dsn = $dsn;
        if ($ret) {
            if (is_writable(CONFIGS . DS . CONFIGFILE_NAME)) {
                $confTpl = $this->_helper->viewRenderer
                                         ->getViewScript('config');

                // Set the host URL. This value is saved into the install.conf
                if (isset($_SERVER['HTTPS']) && 'on' == $_SERVER['HTTPS']) {
                    $hostUrl = 'https://';
                } else {
                    $hostUrl = 'http://';
                }
                if (isset($_SERVER['HTTP_HOST'])) {
                    $hostUrl .= $_SERVER['HTTP_HOST'];
                } else {
                    $hostUrl .= $_SERVER['SERVER_NAME'];
                }
                $this->view->hostUrl = $hostUrl;

                $dbconfig = $this->view->render($confTpl);
                if (0 < file_put_contents(CONFIGS . DS . CONFIGFILE_NAME,
                    $dbconfig)) {
                    $checklist['savingconfig'] = 'ok';
                } else {
                    $ret = false;
                    $errMessage.= 'Write no content to the file.';
                }
            } else {
                $errMessage.= 'Write config file error. ';
                $ret = false;
            }
        }
        $this->view->title = 'Initial Database';
        $this->view->method = $method;
        if ($ret) {
            $this->view->next = '/install/complete';
        } else {
            $this->view->next = '/install/dbsetting';
            $this->view->message = $errMessage;
        }
        $this->view->checklist = $checklist;
        $this->view->back = '/install/dbsetting';
        $this->render('initial');
    }
    public function completeAction()
    {
        $this->view->title = 'Install complete';
        $this->view->next = '/user/login';
        $this->render();
    }
    public function errorAction()
    {
        $content = null;
        $errors = $this->_getParam('error_handler');
        //$this->_helper->layout->setLayout('error');
        switch ($errors->type) {
        case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
        case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
        default:
            // 404 error -- controller or action not found
            $this->getResponse()->setRawHeader('HTTP/1.1 404 Not Found');
            break;
        }
        $this->getResponse()->clearBody();
        $this->render();
    }
}
