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
 * @author    Mark E. Haase <mhaase@endeavorsystems.com>
 * @copyright (c) Endeavor Systems, Inc. 2008 (http://www.endeavorsystems.com)
 * @license   http://www.openfisma.org/mw/index.php?title=License
 * @version   $Id$
 * @package   View_Helper
 */
 
/**
 * Construct a base URI which references the application
 * 
 * The logic is simple. Use https protocol in prod mode and http protocol in dev mode. The host name
 * is fetched from the configuration table.
 *  
 * @package   View_Helper
 * @copyright (c) Endeavor Systems, Inc. 2008 (http://www.endeavorsystems.com)
 * @license   http://www.openfisma.org/mw/index.php?title=License
 */
class View_Helper_BaseUri extends Zend_View_Helper_Abstract
{
    /**
     * Construct a base URI which references the application. DOES NOT INCLUDE TRAILING SLASH.
     *
     * @return string
     */
    public function baseUri()
    {
        $protocol = Fisma::debug() ? 'http' : 'https';
        $host = Configuration::getConfig('host_url');
        $baseUri = "$protocol://$host";
        
        return $baseUri;
    } 
}
