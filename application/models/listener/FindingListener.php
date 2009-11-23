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
 * <http://www.gnu.org/licenses/>.
 */

/**
 * Called by Finding changing
 *
 * @author     Ryan Yang <ryan@users.sourceforge.net>
 * @copyright  (c) Endeavor Systems, Inc. 2009 (http://www.endeavorsystems.com)
 * @license    http://www.openfisma.org/content/license
 * @package    Listener
 * @version    $Id$
 */
class FindingListener extends Doctrine_Record_Listener
{
    /**
     * these keys don't catch logs
     */
    private static $_excludeLogKeys = array(
        'currentEvaluationId',
        'status',
        'ecdLocked',
        'legacyFindingKey',
        'modifiedTs',
        'closedTs'
    );

    /**
     * Maps fields to their corresponding privileges. This is kind of ugly. A better solution would be to store this
     * information in the model itself, and then include it in a global listener.
     */
    private static $_requiredPrivileges = array(
        'type' => 'update_type',
        'description' => 'update_description',
        'recommendation' => 'update_recommendation',
        'mitigationStrategy' => 'update_course_of_action',
        'responsibleOrganizationId' => 'update_assignment',
        'securityControl' => 'update_control_assignment',
        'threatLevel' => 'update_threat',
        'threat' => 'update_threat',
        'countermeasures' => 'update_countermeasures',
        'countermeasuresEffectiveness' => 'update_countermeasures',
        'recommendation' => 'update_recommendation',
        'resourcesRequired' => 'update_resources'
    );

    /**
     * Notification type with each keys. The ECD logic is a little more complicated so it is handled separately.
     * Threat & countermeasures are also handled separately.
     */
    private static $_notificationKeys = array(
        'mitigationStrategy'        => 'UPDATE_COURSE_OF_ACTION',
        'securityControlId'         => 'UPDATE_SECURITY_CONTROL',
        'responsibleOrganizationId' => 'UPDATE_RESPONSIBLE_SYSTEM',
        'countermeasures'           => 'UPDATE_COUNTERMEASURES',
        'threat'                    => 'UPDATE_THREAT',
        'resourcesRequired'         => 'UPDATE_RESOURCES_REQUIRED',
        'description'               => 'UPDATE_DESCRIPTION',
        'recommendation'            => 'UPDATE_RECOMMENDATION',
        'type'                      => 'UPDATE_MITIGATION_TYPE'
    );
    
    /**
     * Set the status as "NEW"  for a new finding created or as "PEND" when duplicated unless duplicated finding is 
     * marked as closed.
     * write the audit log
     * 
     * @param Doctrine_Event $event
     */
    public function preInsert(Doctrine_Event $event)
    {
        $finding = $event->getInvoker();
        $duplicateFinding  = $finding->getTable()
                                     ->findByDql('description LIKE ?', $finding->description);
        if (!empty($duplicateFinding[0]) && $duplicateFinding[0]->status != 'CLOSED') {
            $finding->DuplicateFinding = $duplicateFinding[0];
            $finding->status           = 'PEND';
        } elseif (in_array($finding->type, array('CAP', 'AR', 'FP'))) {
            $finding->status           = 'DRAFT';
        } else {
            $finding->status           = 'NEW';
        }
        $finding->CreatedBy       = User::currentUser();
        $finding->updateNextDueDate();
        $finding->log('New Finding Created');
    }

    /**
     * Check ACL before updating a record. See if any notifications need to be sent.
     * 
     * @param Doctrine_Event $event
     */
    public function preUpdate(Doctrine_Event $event) 
    {
        $finding = $event->getInvoker();
        $modified = $finding->getModified(true);

        if (!empty($modified)) {
            foreach ($modified as $key => $value) {
                // Check whether the user has the privilege to update this column
                if (isset(self::$_requiredPrivileges[$key])) {
                    Fisma_Acl::requirePrivilege(
                        'finding', 
                        self::$_requiredPrivileges[$key], 
                        $finding->ResponsibleOrganization->nickname
                    );
                }
            
                // Check whether this field generates any notification events
                if (array_key_exists($key, self::$_notificationKeys)) {
                    $type = self::$_notificationKeys[$key];
                }

                if (isset($type)) {
                    Notification::notify($type, $finding, User::currentUser(), $finding->responsibleOrganizationId);
                }
            }
        }       
    }

    /**
     * Write the audit logs
     * @todo the log need to get the user who did it
     * @param Doctrine_Event $event
     */
    public function preSave(Doctrine_Event $event)
    {
        $finding = $event->getInvoker();
        $modifyValues = $finding->getModified(true);

        if (!empty($modifyValues)) {
            foreach ($modifyValues as $key => $value) {
                $newValue = $finding->$key;

                // Now address business rules for each field individually
                switch ($key) {
                    case 'type':
                        $finding->status = 'DRAFT';
                        $finding->updateNextDueDate();
                        break;
                    case 'securityControlId':
                        $key      = 'Security Control';
                        $value    = isset(Doctrine::getTable('SecurityControl')->find($value)->code) 
                                  ? Doctrine::getTable('SecurityControl')->find($value)->code 
                                  : null;
                        $newValue = $finding->SecurityControl->code;
                        break;
                    case 'responsibleOrganizationId':
                        $key      = 'Responsible Organization';
                        $value    = isset(Doctrine::getTable('Organization')->find($value)->name) 
                                  ? Doctrine::getTable('Organization')->find($value)->name 
                                  : null;
                        $newValue = $finding->ResponsibleOrganization->name;
                        break;
                    case 'status':
                        if ('MSA' == $value && 'EN' == $newValue) {
                            Notification::notify(
                                'MITIGATION_APPROVED', 
                                $finding, 
                                User::currentUser(), 
                                $finding->responsibleOrganizationId
                            );
                        } elseif ('EN' == $value && 'DRAFT' == $newValue) {
                            Notification::notify(
                                'MITIGATION_REVISE', 
                                $finding, 
                                User::currentUser(), 
                                $finding->responsibleOrganizationId
                            );
                        } elseif ('EA' == $newValue) {
                            Notification::notify(
                                'EVIDENCE_UPLOADED', 
                                $finding, 
                                User::currentUser(), 
                                $finding->responsibleOrganizationId
                            );
                            $finding->actualCompletionDate = Fisma::now();
                        } elseif ( ('EA' == $value && 'EN' == $newValue)
                             || ('MSA' == $value && 'DRAFT' == $newValue) ) {
                            Notification::notify(
                                'APPROVAL_DENIED', 
                                $finding, 
                                User::currentUser(), 
                                $finding->responsibleOrganizationId
                            );
                        } elseif ('EA' == $value && 'CLOSED' == $newValue) {
                            Notification::notify(
                                'FINDING_CLOSED', 
                                $finding, 
                                User::currentUser(), 
                                $finding->responsibleOrganizationId
                            );
                            $finding->closedTs = Fisma::now();
                            $finding->log('Finding closed');
                        }
                        
                        // Once the mitigation strategy is approved, the original ECD becomes locked. Going forward,
                        // only the current ECD is allowed to be edited.
                        if ('EN' == $newValue) {
                            $finding->ecdLocked = true;
                        }
                        break;
                    case 'currentEvaluationId':
                        $event = isset($finding->CurrentEvaluation->Event->name) 
                               ? $finding->CurrentEvaluation->Event->name 
                               : null;
                        // If the event is null, then that indicates this was the last evaluation within its approval
                        // process. That condition is handled above.
                        if (isset($event)) {
                            Notification::notify(
                                $event, 
                                $finding, 
                                User::currentUser(), 
                                $finding->responsibleOrganizationId
                            );
                        }
                        break;
                    case 'originalEcd':
                        throw new Fisma_Exception('The original ECD cannot be set directly.');
                        break;
                    case 'currentEcd':
                        if ($finding->ecdLocked && empty($finding->ecdChangeDescription)) {
                            $error = 'When the ECD is locked, the user must provide a change description'
                                   . ' in order to modify the ECD.';
                            throw new Fisma_Exception($error);
                        }
                        if (!$finding->ecdLocked) {
                            Fisma_Acl::requirePrivilege(
                                'finding', 
                                'update_ecd', 
                                $finding->ResponsibleOrganization->nickname
                            );
                            $finding->originalEcd = $finding->currentEcd;
                            Notification::notify(
                                'UPDATE_ECD', 
                                $finding, 
                                User::currentUser(), 
                                $finding->responsibleOrganizationId
                            );
                        } else {
                            Fisma_Acl::requirePrivilege(
                                'finding', 
                                'update_locked_ecd', 
                                $finding->ResponsibleOrganization->nickname
                            );
                            Notification::notify(
                                'UPDATE_LOCKED_ECD', 
                                $finding, 
                                User::currentUser(), 
                                $finding->responsibleOrganizationId
                            );
                        }
                        break;
                    default:
                        break;
                }
                if (in_array($key, self::$_excludeLogKeys)) {
                    continue;
                }

                $value    = $value ? html_entity_decode(strip_tags($value)) : 'NULL';
                $newValue = html_entity_decode(strip_tags($newValue));

                // Only log if $newValue is actually different from $value. 
                // Ignore changes to NULL/""/NONE if one of these was present 
                // in the original $value.
                if ( (!(is_null($value) || empty($value) || $value == 'NONE') && 
                      !(is_null($newValue) || empty($newValue) || $newValue == 'NONE'))
                      || ($value != $newValue)) {
                    // See if you can look up a logical name for this column in the schema definition. 
                    // If its not defined, then use the physical name instead
                    $column = $finding->getTable()->getColumnDefinition(strtolower($key));
                    $logicalName = (isset($column['extra']) && isset($column['extra']['logicalName']))
                                 ? $column['extra']['logicalName']
                                 : $key;
 
                    $message = "UPDATE: $logicalName\n ORIGINAL: $value\nNEW: $newValue";
                    $finding->log($message);
                }
            }
        }
    }

    /**
     * Insert or Update finding lucene index
     */
    public function postSave(Doctrine_Event $event)
    {
        $finding  = $event->getInvoker();
        $modified = $finding->getModified($old = false, $last = true);
        $index = new Fisma_Index('Finding');
        $index->update($finding);
        
        // Invalidate the caches that contain this finding. This will ensure that users always see
        // accurate summary counts on the finding summary screen.
        $finding->ResponsibleOrganization->invalidateCache();
    }
}
