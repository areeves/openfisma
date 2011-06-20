<?php
/**
 * Copyright (c) 2011 Endeavor Systems, Inc.
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
 * Add the remote_user (Apache's BasicAuth) option to the database
 *
 * @package   Migration
 * @copyright (c) Endeavor Systems, Inc. 2011 {@link http://www.endeavorsystems.com}
 * @author    Andrew Reeves <andrew.reeves@endeavorsystems.com>
 * @license   http://www.openfisma.org/content/license GPLv3
 */
class Version115 extends Doctrine_Migration_Base
{

    /** 
    * Add the remote_user (Apache's BasicAuth) option to the configuration as an auth_type option (enum)
    * 
    * @return void 
    */
    public function up()
    {
        $this->changeColumn(
            'configuration',
            'auth_type',
            null,
            'enum',
            array(
                'values' => array(
                    'database',
                    'ldap',
                    'remote_user'
                )
            )
        );
    }

    /** 
    * Remove the remote_user (Apache's BasicAuth) option to the configuration as an auth_type option (enum)
    * 
    * @return void 
    */
    public function down()
    {
        $this->changeColumn(
            'configuration',
            'auth_type',
            null,
            'enum',
            array(
                'values' => array(
                    'database',
                    'ldap'
                )
            )
        );
    }
}
