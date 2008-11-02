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
 * @version   $Id: ErrorController.php 1012 2008-10-20 05:20:50Z mehaase $
 */
 
/**
 * The error controller implements error-handling logic and displays user-level
 * errors to the user.
 *
 * @package   Controller
 * @copyright (c) Endeavor Systems, Inc. 2008 (http://www.endeavorsystems.com)
 * @license   http://www.openfisma.org/mw/index.php?title=License
 */
class ErrorController extends Zend_Controller_Action
{
    /**
     * This action handles
     *    - Application errors
     *    - Errors in the controller chain arising from missing
     *     controller classes and/or action methods
     */
    public function errorAction()
    {
        $content = null;
        $errors = $this->_getParam('error_handler');
        $this->_helper->layout->setLayout('error');
        $this->getResponse()->clearBody();
        $content = '<p>'
                 . $errors->exception->getMessage()
                 . '</p>'
                 . '<pre>'
                 . $errors->exception->getTraceAsString()
                 . '</pre>';
        $logger = Config_Fisma::getLogInstance();
        $logger->log($content, Zend_Log::ERR);
        $this->view->content = $content;
        $this->_helper->actionStack('header', 'panel');
        $this->render();
    }
    /**
     * Error handler for input validation error
     */
    public function inputerrorAction()
    {
        $content = null;
        $errors = $this->_getParam('inputerror');
        $this->_helper->layout->setLayout('error');
        $content = "<h1>Input Error!</h1>" . PHP_EOL;
        $content.= "Error input fields:";
        foreach ($errors as $fieldname => $erroritem) {
            $content.= " $fieldname  ";
        }
        $this->getResponse()->clearBody();
        $this->view->content = $content . '<p>';
        $this->_helper->actionStack('header', 'panel');
        $this->render('error');
    }
}
