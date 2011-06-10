<?php
/**
* Copyright (c) 2010 Aaron J. Zirbes, University of Minnesota
*
* This file is an extension of OpenFISMA.
*
* This is a basic extension to OpenFISMA to allow the use of the REMOTE_USER variable for login to OpenFISMA.
* It was written so that Shibboleth authentiation could be used with OpenFISMA, but it has many other uses.
*
* INSTALLATION
* After this file has been placed in 'library/Fisma/Zend/Auth/Adapter/'
* and 'application/modules/default/controllers/AuthController.php' has been patched,
* you need to update the database to add support. This can be done by running the following
* SQL command:
* ALTER TABLE openfisma.configuration MODIFY COLUMN auth_type enum('database','ldap','remote_user');
*
* OpenFISMA is free software: you can redistribute it and/or modify it under the terms of the GNU General Public
* License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later
* version.
*
* OpenFISMA is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
* warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more
* details.
*
* You should have received a copy of the GNU General Public License along with OpenFISMA. If not, see
* {@link http://www.gnu.org/licenses/}.
*/

/**
* An authentication adapter for Doctrine (aka database) 
* 
* @author Aaron J. Zirbes 
* @copyright (c) University of Minnesota 2010 {@link http://www.umn.edu}
* @license http://www.openfisma.org/content/license GPLv3
* @package Fisma
* @subpackage Fisma_Zend_Auth
* @version $Id$
*/
class Fisma_Zend_Auth_Adapter_RemoteUser implements Zend_Auth_Adapter_Interface
{
    /**
    * The user to be authenticated
    *
    * @var User
    */
    private $_user = null;

    /**
    * Constructor
    *
    * @param User $user The user name of the account to authenticate
    * @return void
    */
    public function __construct(User $user)
    {
        $this->_user = $user;
    }

    /**
    * Implements the required interface
    *
    * @return Zend_Auth_Result The instance of Zend_Auth_Result
    * @throws Fisma_Zend_Exception_AccountLocked if the account is locked
    */
    public function authenticate()
    {
        /* If we are sure that BasicAuth is setup in the .htaccess file, then PHP_AUTH_USER should be 
           set upon SUCCESSFUL authentication */

        if (!isset($_SERVER['PHP_AUTH_USER'])) {
            $authResult = new Zend_Auth_Result(Zend_Auth_Result::FAILURE, $this->_user);
        } else {
            $authResult = new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $this->_user);
        }
        
        return $authResult;
    }

    private function _passwordIsExpired()
    {
        return false;
    }
}