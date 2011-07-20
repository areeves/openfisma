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
 * The role controller handles CRUD for role objects.
 *
 * @author     Ryan Yang <ryan@users.sourceforge.net>
 * @copyright  (c) Endeavor Systems, Inc. 2009 {@link http://www.endeavorsystems.com}
 * @license    http://www.openfisma.org/content/license GPLv3
 * @package    Controller
 */
class RoleController extends Fisma_Zend_Controller_Action_Object
{
    /**
     * The main name of the model.
     * 
     * This model is the main subject which the controller operates on.
     * 
     * @var string
     */
    protected $_modelName = 'Role';

    /**
     * Override the parent class to add a link for editing privileges
     * 
     * @param Fisma_Doctrine_Record $subject
     */
    public function getViewLinks(Fisma_Doctrine_Record $subject)
    {
        $links = array();
        
        if ($this->_acl->hasPrivilegeForObject('read', $subject)) {
            $links['Privileges'] = "{$this->_moduleName}/{$this->_controllerName}"
                                 . "/right/id/{$subject->id}";
            
            $links['Edit Privilege Matrix'] = '/role/view-matrix';
        }
        
        $links = array_merge($links, parent::getViewLinks($subject));

        return $links;
    }
    
    /**
     * Assign privileges to a single role
     * 
     * @return void
     */
    public function rightAction()
    {   
        $req = $this->getRequest();
        $do = $req->getParam('do');
        $roleId = $req->getParam('id');
        $screenName = $req->getParam('screen_name');
        
        $role = Doctrine::getTable('Role')->find($roleId);
        $this->_acl->requirePrivilegeForObject('assignPrivileges', $role);
                
        $existFunctions = $role->Privileges->toArray();
        if ('availableFunctions' == $do) {
            $existFunctionIds = explode(',', $req->getParam('existFunctions'));
            $q = Doctrine_Query::create()
                 ->from('Privilege');
            if (!empty($screenName)) {
                $q->where('resource = ?', $screenName);
            }
            $allFunctions = $q->execute()->toArray();
            $availableFunctions = array();
            foreach ($allFunctions as $v) {
                if (!in_array($v['id'], $existFunctionIds)) {
                    $availableFunctions[] = $v;
                }
            }
            $this->_helper->layout->setLayout('ajax');
            $this->view->assign('functions', $availableFunctions);
            $this->render('funcoptions');
        } elseif ('existFunctions' == $do) {
            $this->_helper->layout->setLayout('ajax');
            $this->view->assign('functions', $existFunctions);
            $this->render('funcoptions');
        } elseif ('update' == $do) {
            $functionIds = $req->getParam('existFunctions');
            $errno = 0;
            if (!Doctrine::getTable('RolePrivilege')->findByRoleId($roleId)->delete()) {
                $errno++;
            }

            if ($functionIds) {
                foreach ($functionIds as $fid) {
                    $rolePrivilege = new RolePrivilege();
                    $rolePrivilege->roleId = $roleId;
                    $rolePrivilege->privilegeId = $fid;
                    if (!$rolePrivilege->trySave()) {
                        $errno++;
                    }
                }
            }

            if ($errno > 0) {
                $msg = "Set right for role failed.";
                $model = 'warning';
            } else {
                $msg = "Successfully set right for role.";
                $model = 'notice';
            }
            $this->view->priorityMessenger($msg, $model);
            $this->_redirect('role/right/id/' . $roleId);
        } else {
            $role = Doctrine::getTable('Role')->find($roleId)->toArray();
            $q = Doctrine_Query::create()
                          ->from('Privilege')
                          ->groupBy('resource');
            $screenList = $q->execute()->toArray();
            $this->view->assign('role', $role);
            $this->view->assign('screenList', $screenList);
            $this->view->assign('existFunctions', $existFunctions);
            $this->render('right');
        }
    }

    /**
     * Sisplays a (checkbox-)table of privileges associated with each role
     * 
     * @return void
     */
     public function viewMatrixAction()
     {
        $this->_acl->requirePrivilegeForClass('update', 'Role');

        // Add button to save changes (submit form)
        $this->view->toolbarButtons = array();
        $this->view->toolbarButtons[] = new Fisma_Yui_Form_Button_Submit(
            'saveChanges',
            'Save Changes',
            array(
                'label' => 'Save Changes'
            )
        );

        // YUI data-table to show user
        $dataTable = new Fisma_Yui_DataTable_Local();
        $dataTable->setGroupBy('privilegeResource');
        
        // Each row (array) must be an array of ColumnName => CellValue
        $blankRow = array();
        
        // Add event handler pointer (on checkboxClickEvent, call dataTableCheckboxClick
        $dataTable->addEventListener('checkboxClickEvent', 'Fisma.Role.dataTableCheckboxClick');
        
        // The first column will the be privilege-description
        $dataTable->addColumn(
            new Fisma_Yui_DataTable_Column(
                'Privilege',
                false,
                'Fisma.TableFormat.formatHtml',
                null,
                'privilegeDescription'
            )
        );
        
        // Add this key (column-name) to the row template
        $blankRow['privilegeDescription'] = '';
        
        // The second column will be the privilege-id (hidden)
        $dataTable->addColumn(
            new Fisma_Yui_DataTable_Column(
                'Privilege ID',
                false,
                'YAHOO.widget.DataTable.formatText',
                null,
                'privilegeId',
                true
            )
        );
        
        // Add this key (column-name) to the row template
        $blankRow['privilegeId'] = '';

        // Get a list of all roles
        $rolesQuery = Doctrine_Query::create()
            ->select('r.nickname')
            ->from('Role r')
            ->orderBy('r.nickname')
            ->setHydrationMode(Doctrine::HYDRATE_ARRAY);
        $roles = $rolesQuery->execute();

        // Add a column for each role
        foreach ($roles as $role) {
        
            // Add column
            $dataTable->addColumn(
                new Fisma_Yui_DataTable_Column(
                    $role['nickname'],
                    false,
                    'YAHOO.widget.DataTable.formatCheckbox',
                    'dataTableCheckboxClick',
                    $role['nickname']
                )
            );
            
            // Add this key (column-name) to the row template
            $blankRow[$role['nickname']] = '';

        }

        $dataTable->addColumn(
            new Fisma_Yui_DataTable_Column(
                'privilegeResource',
                false,
                'YAHOO.widget.DataTable.formatText',
                null,
                'privilegeResource',
                true 
            )
        );

        // Get a list of what role each privilege is associated with
        $privilegeQuery = Doctrine_Query::create()
            ->select('r.nickname, p.description, p.action, p.resource')
            ->from('Privilege p')
            ->leftJoin('p.Roles r')
            ->orderBy('p.resource, p.description')
            ->setHydrationMode(Doctrine::HYDRATE_ARRAY);
        $privileges = $privilegeQuery->execute();

        // Add a row for each privilege
        $dataTableRows = array();
        foreach ($privileges as $privilege) {

            // Copy from blank row, so that all column-names exists as keys in this row array
            $newRow = $blankRow;
            
            $newRow['privilegeDescription'] = $privilege['description'];
            $newRow['privilegeId'] = $privilege['id'];
            $newRow['privilegeResource'] = ucfirst($privilege['resource']);

            // Update (set true) any cell of this privilege row, that has this role
            foreach ($privilege['Roles'] as $role) {
                $newRow[$role['nickname']] = true;
            }

            // Add row to data-table
            $dataTableRows[] = $newRow;
        }

        $dataTable->setData($dataTableRows);
        $this->view->dataTable = $dataTable;
     }
    
    /**
     * If rolePrivChanges exists (post/get), will save the role/privilege changes, Redirects to viewMatrixAction.
     * 
     * rolePrivChanges is expected to be a string/json-object, when json-decoded, to be an array of 
     * objects, each with a newValue, privilegeId, and roleName property.
     *
     * @return void
     */
    public function saveMatrixAction()
    {
        $this->_acl->requirePrivilegeForClass('update', 'Role');
        
        // Check if there are changes to apply
        $rolePrivChanges = $this->getRequest()->getParam('rolePrivChanges');
        if (!is_null($rolePrivChanges)) {
        
            $rolePrivChanges = json_decode($rolePrivChanges, true);

            // Priority messenger
            $msg = array();

            // Apply each requested change
            Doctrine_Manager::connection()->beginTransaction();
            try {
                foreach ($rolePrivChanges as $change) {

                    $roleName = $change['roleName'];
                    $privilegeId = $change['privilegeId'];
                    $roleId = Doctrine::getTable('Role')->findOneByNickname($roleName)->id;
                    $privilegeDescription = Doctrine::getTable('Privilege')->findOneById($privilegeId)->description;
                    
                    // Check if this role has this privilege already
                    $targetRolePrivilegeCount = Doctrine_Query::create()
                        ->select('roleid')
                        ->from('RolePrivilege')
                        ->where('roleid = ' . $roleId)
                        ->andWhere('privilegeid = ' . $privilegeId)
                        ->setHydrationMode(Doctrine::HYDRATE_ARRAY)
                        ->count();
                    $roleHasPrivilege = $targetRolePrivilegeCount > 0 ? true : false;
                    
                    // The checkbox was either checked (add) or unchecked (deleted)
                    $operation = (int) $change['newValue'] === 1 ? 'add' : 'delete';

                    // Add this privilege for this role if that was requested
                    if ($operation === 'add' && $roleHasPrivilege === false) {
                    
                        $newRolePrivilege = new RolePrivilege;
                        $newRolePrivilege->roleId = $roleId;
                        $newRolePrivilege->privilegeId = $privilegeId;
                        $newRolePrivilege->save();
                    
                        // Add to message stack
                        $msg[] = "Added the $privilegeDescription privilege to the $roleName role.";
                        
                    } elseif ($operation === 'delete' && $roleHasPrivilege === true) {
                    
                        // Remove this privilege for this role
                        $removeRolePrivilegeQuery = Doctrine_Query::create()
                            ->delete('RolePrivilege rp')
                            ->where('rp.roleId = ' . $roleId)
                            ->andWhere('rp.privilegeId = ' . $privilegeId);
                        $removeRolePrivilegeQuery->execute();
                        
                        // Add to message stack
                        $msg[] = "Removed the $privilegeDescription privilege from the $roleName role.";
                    }

                }
                
                Doctrine_Manager::connection()->commit();
                
            } catch (Exception $e) {
                Doctrine_Manager::connection()->rollBack();
                $this->view->priorityMessenger('An error occurred while saving privileges', 'warning');
                $this->_redirect('/role/view-matrix');
                return;
            }
            
            // Send priority messenger if there are messeges to send
            if (!empty($msg)) {
                $msg = implode("<br/>", $msg);
                $this->view->priorityMessenger($msg, 'notice');
            }
        }
        
        // Now that the privileges have been saved, redirect back to the view-mode
        $this->_redirect('/role/view-matrix');
    }
    
    /**
     * parent::getToolbarButtons located in FZCAO, and extends its returned array with a button to 
     * edit the Privilege Matrix
     *
     * @return array Array of Fisma_Yui_Form_Button
     */
    public function getToolbarButtons()
    {
        $buttons = parent::getToolbarButtons();
        
        if ($this->_acl->hasPrivilegeForClass('update', 'Role')) {
            $buttons['editMatrix'] = new Fisma_Yui_Form_Button_Link(
                'editMatrix',
                array(
                    'value' => 'Edit Privilege Matrix',
                    'href' => '/role/view-matrix'
                )
            );
        }
        
        return $buttons;
    }
    
    protected function _isDeletable()
    {
        return false;
    }
}
