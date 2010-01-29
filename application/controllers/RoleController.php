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
 * @version    $Id$
 */
class RoleController extends BaseController
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
     * View a role
     * 
     * @return void
     */
    public function viewAction()
    {
        $id = $this->_request->getParam('id');
        $this->view->assign('rightLink', "/panel/Role/sub/right/id/$id");
        parent::viewAction();
    }
    
    /**
     * Delete a role
     * 
     * @return void
     */
    public function deleteAction()
    {        
        $req = $this->getRequest();
        $id = $req->getParam('id');
        $role = Doctrine::getTable('Role')->find($id);
        if (!$role) {
            $msg   = "Invalid Role ID";
            $type = 'warning';
        } else {
            Fisma_Acl::requirePrivilegeForObject('delete', $role);
            
            $users = $role->Users->toArray();
            if (!empty($users)) {
                $msg = 'This role cannot be deleted because it is in use by one or more users';
                $type = 'warning';
            } else {
                Doctrine::getTable('RolePrivilege')
                ->findByRoleId($id)
                ->delete();
                parent::deleteAction();
                // parent method will take care 
                // of the message and forword the page
                return;
            }
        }
        $this->view->priorityMessenger($msg, $type);
        $this->_forward('list');
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
        Fisma_Acl::requirePrivilegeForObject('assignPrivileges', $role);
                
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
            foreach ($functionIds as $fid) {
                $rolePrivilege = new RolePrivilege();
                $rolePrivilege->roleId = $roleId;
                $rolePrivilege->privilegeId = $fid;
                if (!$rolePrivilege->trySave()) {
                    $errno++;
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
            $this->_redirect('panel/role/sub/right/id/' . $roleId);
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
}
