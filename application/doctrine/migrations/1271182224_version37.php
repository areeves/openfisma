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
 * Add privilege for incident locking  
 * 
 * @package Migration
 * @version $Id$
 * @copyright (c) Endeavor Systems, Inc. 2009 {@link http://www.endeavorsystems.com}
 * @author Josh Boyd <joshua.boyd@endeavorsystems.com> 
 * @license http://www.openfisma.org/content/license GPLv3
 */
class Version37 extends Doctrine_Migration_Base
{
    /**
     * Add the Lock Incident privilege 
     * 
     * @access public
     * @return void
     */
    public function up()
    {
        $privilege = new Privilege();
        $privilege->resource = 'incident';
        $privilege->action = 'lock';
        $privilege->description = 'Lock Incident';
        $privilege->save();

        $role = Doctrine_Query::create()
            ->from('Role r')
            ->where('r.nickname = ?', 'OIG')
            ->setHydrationMode(Doctrine::HYDRATE_ARRAY)
            ->execute();

        $rolePrivilege = new RolePrivilege();
        $rolePrivilege->roleId = $role[0]['id'];
        $rolePrivilege->privilegeId = $privilege->id;
        $rolePrivilege->save();
    }

    /**
     * Remove the Lock Incident privilege
     * 
     * @return void
     */
    public function down()
    {
        $privilege = Doctrine_Query::create()
            ->from('Privilege p')
            ->where('p.resource = ?', 'incident')
            ->andWhere('p.action = ?', 'lock')
            ->andWhere('p.description = ?', 'Lock Incident')
            ->execute();

        foreach ($privilege as $p) {
            $p->unlink('Role');
            $p->save();
            $p->delete();
        }
    }
}
