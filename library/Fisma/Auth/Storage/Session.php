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
 * @category   Auth
 * @copyright (c) Endeavor Systems, Inc. 2008 (http://www.endeavorsystems.com)
 * @license   http://www.openfisma.org/mw/index.php?title=License
 * @package    Fisma_Auth
 */
class Fisma_Auth_Storage_Session extends Zend_Auth_Storage_Session 
{
    /**
     * Default session namespace
     */
    const NAMESPACE_DEFAULT = 'OpenFISMA';

    /**
     * Default session object member name
     */
    const MEMBER_DEFAULT = 'currentUser';

    /**
     * Default session timeout seconds
     */
    const INACTIVITY_SECONDS = 5400;

    /**
     * Sets session storage options and initializes session namespace object
     *
     * @param  mixed $namespace
     * @param  mixed $member
     * @return void
     */
    public function __construct($namespace = self::NAMESPACE_DEFAULT, $member = self::MEMBER_DEFAULT)
    {
        $this->_namespace = $namespace;
        $this->_member    = $member;
        $this->_session   = new Zend_Session_Namespace($this->_namespace);
        try {
            // Set up the session timeout for the authentication token
            $refreshSeconds = Configuration::getConfig('session_inactivity_period');
        } catch (Exception $e) {
            // in any case such as the database is not available during installation
            $refreshSeconds = self::INACTIVITY_SECONDS;
        }
        $this->_session->setExpirationSeconds($refreshSeconds);
    }

}

