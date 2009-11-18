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
 * @author    Xhorse 
 * @copyright (c) Endeavor Systems, Inc. 2008 (http://www.endeavorsystems.com)
 * @license   http://www.openfisma.org/mw/index.php?title=License
 * @version   $Id$
 * @package   Fisma_Auth
 */

/**
 * An authentication adapter for Doctrine (aka database) 
 * 
 * @category   Fisma
 * @copyright  Copyright (c) 2005-2008
 * @license    http://www.openfisma.org/mw/index.php?title=License
 * @package    Fisma_Auth
 */
class Fisma_Auth_Adapter_Doctrine implements Zend_Auth_Adapter_Interface
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
     * @param User $user
     * @param string $password
     */
    public function __construct(User $user, $password) 
    {
        $this->_user = $user;
        $this->_credential = $password;
    }

    /**
     * Implements the required interface
     *
     * @return Zend_Auth_Result
     */
    public function authenticate()
    {
        // If password has expired, then the user cannot be authenticated
        if ($this->_passwordIsExpired()
            && !$this->_user->locked) {

            $this->_user->lockAccount(User::LOCK_TYPE_EXPIRED);
            $reason = $this->_user->getLockReason();
            throw new Fisma_Exception_AccountLocked("Account is locked ($reason)");
        }

        // Check password
        if ($this->_user->hash($this->_credential) == $this->_user->password) {
            $authResult = new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $this->_user);
        } else {
            $authResult = new Zend_Auth_Result(Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID, $this->_user);
        }
        
        // If password is wrong, determine whether the account needs to be locked due to password failures
        if (!$authResult->isValid()) {
            $this->_user->failureCount++;
            if ($this->_user->failureCount >= Configuration::getConfig('failure_threshold')) {
                $this->_user->lockAccount(User::LOCK_TYPE_PASSWORD);
                $reason = $this->_user->getLockReason();
                throw new Fisma_Exception_AccountLocked("Account is locked ($reason)");
            }
            $this->_user->save();
        }
        
        return $authResult;
    }

    /**
     * Check if the password has expired
     * 
     * @return boolean
     */
    private function _passwordIsExpired()
    {
        $passExpireTs = new Zend_Date($this->_user->passwordTs, Zend_Date::ISO_8601);
        $passExpirePeriod = Configuration::getConfig('pass_expire');
        $passExpireTs->add($passExpirePeriod, Zend_Date::DAY);
        $expired = $passExpireTs->isEarlier(Zend_Date::now());

        return $expired;
    }
}

