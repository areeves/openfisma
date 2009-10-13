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
 * @author    Ryan Yang <ryan@users.sourceforge.net>
 * @copyright (c) Endeavor Systems, Inc. 2008 (http://www.endeavorsystems.com)
 * @license   http://www.openfisma.org/mw/index.php?title=License
 * @version   $Id$
 * @package   Fisma
 */

/**
 * Send mail to user for validate email, account notification etc. 
 *
 * @copyright (c) Endeavor Systems, Inc. 2008 (http://www.endeavorsystems.com)
 * @license    http://www.openfisma.org/mw/index.php?title=License
 * @package    Fisma
 */
class Fisma_Mail extends Zend_Mail
{
    protected $_contentTpl = null;

    public function __construct()
    {
        $view = new Zend_View();
        $this->_contentTpl = $view->setScriptPath(Fisma::getPath('application') . '/views/scripts/mail');
        $view->addHelperPath(Fisma::getPath('viewHelper'), 'View_Helper_');
        
        $this->setFrom(Configuration::getConfig('sender'), Configuration::getConfig('system_name'));
    }

   /**
     * Validate the user's e-mail change.
     *
     * @param object @user User
     * @param string $email the email need to validate
     * @return true|false
     */
    public function validateEmail($user, $email)
    {
        $this->addTo($email);
        $this->setSubject("Confirm Your E-mail Address");

        $this->_contentTpl->host  = Zend_Controller_Front::getInstance()->getRequest()->getHttpHost();
        $this->_contentTpl->validateCode = $user->EmailValidation->getLast()->validationCode;
        $this->_contentTpl->user         = $user;

        $content    = $this->_contentTpl->render('validate.phtml');
        $this->setBodyText($content);
        try {
            $this->send($this->_getTransport());
            return true;
        } catch (Exception $excetpion) {
            return false;
        }
    }

    /**
     * Compose and send the notification email for user.
     *
     * @todo hostUrl can't be get in the CLI script
     *
     * @param array $notifications A group of rows from the notification table
     */
    public function sendNotification($notifications)
    {
        $user = $notifications[0]->User;
        $receiveEmail = empty($user->notifyEmail)
                      ? $user->email
                      : $user->notifyEmail;

        $this->addTo($receiveEmail, $user->nameFirst . $user->nameLast);
        $this->setSubject("Your notifications for " . Configuration::getConfig('system_name'));
        $this->_contentTpl->notifyData = $notifications;
        $this->_contentTpl->user       = $user;
        $content = $this->_contentTpl->render('notification.phtml');
        $this->setBodyText($content);

        try {
            $this->send($this->_getTransport());    
            print(Fisma::now() . " Email was sent to $receiveEmail\n");
        } catch (Exception $e) {
            print($e->getMessage() . "\n");
            exit();
        }
    }

    /**
     * Send email to a new created user to tell user what the username and password is.
     *
     * @param object $user include the unencrypt password
     * @throw
     */
    public function sendAccountInfo(User $user)
    {
        $systemName = Configuration::getConfig('system_name');
        $this->addTo($user->email, $user->nameFirst . ' ' . $user->nameLast);
        $this->setSubject("Your new account for $systemName has been created");
        $this->_contentTpl->user = $user;
        $this->_contentTpl->host = Configuration::getConfig('host_url');
        $content = $this->_contentTpl->render('sendaccountinfo.phtml');
        $this->setBodyText($content);

        try {
            $this->send($this->_getTransport());
        } catch (Exception $excetpion) {
        }
    }

    /**
     * Send the new password to the user
     *
     * @param object $user include the unencrypt password
     * @return bool
     */
    public function sendPassword(User $user)
    {
        $systemName = Configuration::getConfig('system_name');
        $this->addTo($user->email, $user->nameFirst . ' ' . $user->nameLast);
        $this->setSubject("Your password for $systemName has been changed");
        $this->_contentTpl->user = $user;
        $this->_contentTpl->host = Zend_Controller_Front::getInstance()->getRequest()->getHttpHost();
        $content = $this->_contentTpl->render('sendpassword.phtml');
        $this->setBodyText($content);

        try {
            $this->send($this->_getTransport());
        } catch (Exception $excetpion) {
        }
    }

    /**
     * Notify users a new incident has been reported
     *
     * @param int $userId id of the user that will receive the email
     * @param int $incidentId id of the incident that the email is referencing 
     * 
     */
    public function IRReport($userId, $incidentId)
    {
        $user = new User();
        $user = $user->getTable()->find($userId);

        $this->addTo($user->email, $user->nameFirst . ' ' . $user->nameLast);
        $this->setSubject("A new incident has been reported.");
        
        $this->_contentTpl->incidentId = $incidentId;
        
        $content = $this->_contentTpl->render('IRReported.phtml');
        
        $this->setBodyText($content);

        try {
            $this->send($this->_getTransport());
        } catch (Exception $excetpion) {
        }
    }

    /**
     * Notify users they have been assigned to a new incident
     *
     * @param int $userId id of the user that will receive the email
     * @param int $incidentId id of the incident that the email is referencing 
     * 
     */
    public function IRAssign($userId, $incidentId)
    {
        $user = new User();
        $user = $user->getTable()->find($userId);

        $this->addTo($user->email, $user->nameFirst . ' ' . $user->nameLast);
        $this->setSubject("You have been assigned to a new incident.");
        
        $this->_contentTpl->incidentId = $incidentId;
        
        $content = $this->_contentTpl->render('IRAssign.phtml');
        
        $this->setBodyText($content);

        try {
            $this->send($this->_getTransport());
        } catch (Exception $excetpion) {
        }
    }

    /**
     * Notify users that an incident has been opened
     *
     * @param int $userId id of the user that will receive the email
     * @param int $incidentId id of the incident that the email is referencing 
     * 
     */
    public function IROpen($userId, $incidentId)
    {
        $user = new User();
        $user = $user->getTable()->find($userId);

        $this->addTo($user->email, $user->nameFirst . ' ' . $user->nameLast);
        $this->setSubject("An incident has been opened.");
        
        $this->_contentTpl->incidentId = $incidentId;
        
        $content = $this->_contentTpl->render('IROpen.phtml');
        
        $this->setBodyText($content);

        try {
            $this->send($this->_getTransport());
        } catch (Exception $excetpion) {
        }
    }

    /**
     * Notify a user that an incident workflow step has been completed
     *
     * @param int $userId ID of the user that will receive the email
     * @param int $incidentId
     * @param string $workflowStep Description of the completed step
     * @param string $workflowCompletedBy Name of user who completed the step
     */
    public function IRStep($userId, $incidentId, $workflowStep, $workflowCompletedBy)
    {
        $user = new User();
        $user = $user->getTable()->find($userId);

        $this->addTo($user->email, $user->nameFirst . ' ' . $user->nameLast);
        $this->setSubject("A workflow step has been completed.");
        
        $this->_contentTpl->workflowStep = $workflowStep;
        $this->_contentTpl->workflowCompletedBy = $workflowCompletedBy;
        $this->_contentTpl->incidentId = $incidentId;
        
        $content = $this->_contentTpl->render('IRStep.phtml');
        
        $this->setBodyText($content);

        try {
            $this->send($this->_getTransport());
        } catch (Exception $excetpion) {
        }
    }

    /**
     * Notify users that a comment has been added to an incident
     *
     * @param int $userId id of the user that will receive the email
     * @param int $incidentId id of the incident that the email is referencing 
     * 
     */
    public function IRComment($userId, $incidentId)
    {
        $user = new User();
        $user = $user->getTable()->find($userId);

        $this->addTo($user->email, $user->nameFirst . ' ' . $user->nameLast);
        $this->setSubject("A comment has been added to an incident.");
        
        $this->_contentTpl->incidentId = $incidentId;
        
        $content = $this->_contentTpl->render('IRComment.phtml');
        
        $this->setBodyText($content);

        try {
            $this->send($this->_getTransport());
        } catch (Exception $excetpion) {
        }
    }
    
    /**
     * Notify users that an incident has been resolved
     *
     * @param int $userId id of the user that will receive the email
     * @param int $incidentId id of the incident that the email is referencing 
     * 
     */
    public function IRResolve($userId, $incidentId)
    {
        $user = new User();
        $user = $user->getTable()->find($userId);

        $this->addTo($user->email, $user->nameFirst . ' ' . $user->nameLast);
        $this->setSubject("An incident has been resolved.");
        
        $this->_contentTpl->incidentId = $incidentId;
        
        $content = $this->_contentTpl->render('IRResolve.phtml');
        
        $this->setBodyText($content);

        try {
            $this->send($this->_getTransport());
        } catch (Exception $excetpion) {
        }
    }
    
    /**
     * Notify users that an incident has been closed
     *
     * @param int $userId id of the user that will receive the email
     * @param int $incidentId id of the incident that the email is referencing 
     * 
     */
    public function IRClose($userId, $incidentId)
    {
        $user = new User();
        $user = $user->getTable()->find($userId);

        $this->addTo($user->email, $user->nameFirst . ' ' . $user->nameLast);
        $this->setSubject("An incident has been closed.");
        
        $this->_contentTpl->incidentId = $incidentId;
        
        $content = $this->_contentTpl->render('IRClose.phtml');
        
        $this->setBodyText($content);

        try {
            $this->send($this->_getTransport());
        } catch (Exception $excetpion) {
        }
    }

    /**
     * Return the appropriate Zend_Mail_Transport subclass,
     * based on the system's configuration.
     *
     * @return Zend_Mail_Transport_Smtp|Zend_Mail_Transport_Sendmail
     */
    private function _getTransport()
    {
        $transport = null;
        if ( 'smtp' == Configuration::getConfig('send_type')) {
            $username = Configuration::getConfig('smtp_username');
            $password = Configuration::getConfig('smtp_password');
            $port     = Configuration::getConfig('smtp_port');
            if (empty($username) && empty($password)) {
                //Un-authenticated SMTP configuration
                $config = array('port' => $port);
            } else {
                $config = array('auth'     => 'login',
                                'port'     => $port,
                                'username' => $username,
                                'password' => $password);
            }
            $transport = new Zend_Mail_Transport_Smtp(Configuration::getConfig('smtp_host'), $config);
        } else {
            $transport = new Zend_Mail_Transport_Sendmail();
        }
        return $transport;
    }
}
