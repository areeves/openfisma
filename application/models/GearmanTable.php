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
 * Gearman Table
 *
 * @author     Christian Smith <mhaase@endeavorsystems.com>
 * @copyright  (c) Endeavor Systems, Inc. 2011 {@link http://www.endeavorsystems.com}
 * @license    http://www.openfisma.org/content/license GPLv3
 * @package    Models
 */
class GearmanTable extends Fisma_Doctrine_Table implements Fisma_Search_Searchable
{
    /**
     * Implement the interface for Searchable
     */
    public function getSearchableFields()
    {
        return array (
            'id' => array(
                'initiallyVisible' => true,
                'label' => 'ID',
                'sortable' => true,
                'type' => 'integer'
            ),
            'createdTs' => array(
                'initiallyVisible' => true,
                'label' => 'Created',
                'sortable' => true,
                'type' => 'datetime'
            ),

            'startedTs' => array(
                'initiallyVisible' => true,
                'label' => 'Started Timestamp',
                'sortable' => true,
                'type' => 'datetime'
            ),
            'finishedTs' => array(
                'initiallyVisible' => true,
                'label' => 'Finished Timestamp',
                'sortable' => true,
                'type' => 'datetime'
            ),
            'jobHandle' => array(
                'initiallyVisible' => true,
                'label' => 'Job Handle',
                'sortable' => true,
                'type' => 'text'
            ),
            'worker' => array(
                'initiallyVisible' => true,
                'label' => 'Worker',
                'sortable' => true,
                'type' => 'text'
            ),
            'userId' => array(
                'initiallyVisible' => true,
                'label' => 'User ID',
                'sortable' => true,
                'type' => 'integer'
            ),
            'status' => array(
                'initiallyVisible' => true,
                'label' => 'Status',
                'sortable' => true,
                'type' => 'text'
            ),
            'progress' => array(
                'initiallyVisible' => true,
                'label' => 'Progress',
                'sortable' => false,
                'type' => 'integer'
            ),
            'success' => array(
                'initiallyVisible' => true,
                'label' => 'Success',
                'sortable' => false,
                'type' => 'text'
            ),
        );
    }

    /**
     * Return a list of fields which are used for access control
     *
     * @return array
     */
    public function getAclFields()
    {
        $aclFields = array();
        $currentUser = CurrentUser::getInstance();

        // Invoke the ACL constraint only if the user doesn't have the "unaffiliated assets" privilege
        if (!$currentUser->acl()->hasPrivilegeForClass('unaffiliated', 'Asset')) {
            $aclFields['organizationId'] = 'VulnerabilityTable::getOrganizationIds';
        }

        return $aclFields;
    }

    /**
     * Provide ID list for ACL filter
     *
     * @return array
     */
    static function getOrganizationIds()
    {
        $currentUser = CurrentUser::getInstance();

        $organizations = $currentUser->getOrganizationsByPrivilege('vulnerability', 'read');
        $organizationIds = $organizations->toKeyValueArray('id', 'id');

        return $organizationIds;
    }
}
