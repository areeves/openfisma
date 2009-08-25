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
 * @author    Ryan Yang <ryan@users.sourceforge.net>
 * @copyright (c) Endeavor Systems, Inc. 2008 (http://www.endeavorsystems.com)
 * @license   http://www.openfisma.org/mw/index.php?title=License
 * @version   $Id$
 * @package   Controller
 */

/**
 * Handles CRUD for organization objects.
 *
 * @package   Controller
 * @copyright (c) Endeavor Systems, Inc. 2008 (http://www.endeavorsystems.com)
 * @license   http://www.openfisma.org/mw/index.php?title=License
 */
class OrganizationController extends SecurityController
{
    private $_paging = array(
        'startIndex' => 0,
        'count' => 20,
    );
    
    /**
     * Invoked before each Action
     */
    public function preDispatch()
    {
        parent::preDispatch();
        $req = $this->getRequest();
        $this->_paging['startIndex'] = $req->getParam('startIndex', 0);
    }
    
    public function init()
    {
        parent::init();
        $this->_helper->contextSwitch()
                      ->addActionContext('tree-data', 'json')
                      ->initContext();
    }
    
    /**
     * Returns the standard form for creating, reading, and
     * updating organizations.
     * 
     * @param Object $currOrg current recode of organization
     * @return Zend_Form
     */
    private function _getOrganizationForm($currOrg = null)
    {
        $form = Fisma_Form_Manager::loadForm('organization');
        
        // build base query
        $q = Doctrine_Query::create()
                ->select('o.*')
                ->from('Organization o')
                ->where('o.orgType != "system"');

        if ($currOrg == null) {
            $currOrg = new Organization();
        } else {
            $orgArray = $currOrg->toArray();
            // filter the organizations which belongs to the current organization and itself
            $q->andWhere('o.lft < ? OR o.rgt > ?', array($orgArray['lft'], $orgArray['rgt']));
            // if the organization is specifted, than set the parent node.
            if ($currOrg->getNode()->getParent()) {
                $form->getElement('parent')->setValue($currOrg->getNode()->getParent()->id);
            }
        }

        // if the organization is root, then you haven't chance to change its parent
        if ($currOrg->getNode()->isRoot()) {
            // remove the column
            $form->removeElement('parent');
        } else {
            $organizationTreeObject = Doctrine::getTable('Organization')->getTree();
            $organizationTreeObject->setBaseQuery($q);
            $organizationTree = $organizationTreeObject->fetchTree();
            if (!empty($organizationTree)) {
                foreach ($organizationTree as $organization) {
                    $value = $organization['id'];
                    $text = str_repeat('--', $organization['level']) . $organization['name'];
                    $form->getElement('parent')->addMultiOptions(array($value => $text));
                }
            } else {
                // condition: no organization in DB
                $form->getElement('parent')->addMultiOptions(array(0 => 'NONE'));
            }
        }
        
        // get all kinds of orgType
        $orgTypeArray = $currOrg->getTable()->getEnumValues('orgType');
        // except 'system' type
        unset($orgTypeArray[array_search('system', $orgTypeArray)]);
        $form->getElement('orgType')->addMultiOptions(array_combine($orgTypeArray, $orgTypeArray));
        
        return Fisma_Form_Manager::prepareForm($form);
    }

    /**
     *  Render the form for searching the organizations.
     */
    public function searchbox()
    {
        Fisma_Acl::requirePrivilege('organization', 'read');
        $keywords = trim($this->_request->getParam('keywords'));
        $this->view->assign('keywords', $keywords);
        $this->render('searchbox');
    }

    /**
     * show the list page, not for data
     */     
    public function listAction()
    {
        Fisma_Acl::requirePrivilege('organization', 'read'); 
        $value = trim($this->_request->getParam('keywords'));
        empty($value) ? $link = '' : $link = '/keywords/' . $value;
        $this->searchbox();
        $this->view->assign('pageInfo', $this->_paging);
        $this->view->assign('link', $link);
        $this->render('list');
    }

    /**
     * list the organizations from the search, 
     * if search none, it list all organizations
     * 
     */
    public function searchAction()
    {
        Fisma_Acl::requirePrivilege('organization', 'read');
        $value = trim($this->_request->getParam('keywords'));

        $this->_helper->layout->setLayout('ajax');
        $this->_helper->viewRenderer->setNoRender();
        $sortBy = $this->_request->getParam('sortby', 'name');
        $order = $this->_request->getParam('order');
        
        $organization = Doctrine::getTable('Organization');
        if (!in_array(strtolower($sortBy), $organization->getColumnNames())) {
            throw new Fisma_Exception('Invalid "sortBy" parameter');
        }
        
        
        $order = strtoupper($order);
        if ($order != 'DESC') {
            $order = 'ASC'; //ignore other values
        }
        
        $q = Doctrine_Query::create()
             ->select('*')
             ->from('Organization o')
             ->where('o.orgType IS NULL')
             ->orWhere('o.orgType != ?', 'system')
             ->orderBy("o.$sortBy $order")
             ->limit($this->_paging['count'])
             ->offset($this->_paging['startIndex']);

        if (!empty($value)) {
            $organizationIds = Fisma_Lucene::search($value, 'organization');
            if (empty($organizationIds)) {
                $organizationIds = array(-1);
            }
            $q->whereIn('o.id', $organizationIds);
        }
        $totalRecords = $q->count();
        $organizations = $q->execute();
        
        $tableData = array('table' => array(
            'recordsReturned' => count($organizations->toArray()),
            'totalRecords' => $totalRecords,
            'startIndex' => $this->_paging['startIndex'],
            'sort' => $sortBy,
            'dir' => $order,
            'pageSize' => $this->_paging['count'],
            'records' => $organizations->toArray()
        ));
        
        echo json_encode($tableData);
    }
    
    /**
     * Display a single organization record with all details.
     */
    public function viewAction()
    {
        Fisma_Acl::requirePrivilege('organization', 'read'); 
        $this->searchbox();
        $id = $this->_request->getParam('id');
        $v = $this->_request->getParam('v', 'view');
        
        $organization = Doctrine::getTable('Organization')->find($id);
        
        $form = $this->_getOrganizationForm($organization);
        
        if (!$organization) {
            throw new Fisma_Exception('Invalid organization ID');
        } else {
            $organization = $organization->toArray();
        }

        if ($v == 'edit') {
            $this->view->assign('viewLink',
                                "/panel/organization/sub/view/id/$id");
            $form->setAction("/panel/organization/sub/update/id/$id");
        } else {
            // In view mode, disable all of the form controls
            $this->view->assign('editLink',
                                "/panel/organization/sub/view/id/$id/v/edit");
            $form->setReadOnly(true);
        }
        $this->view->assign('deleteLink',"/panel/organization/sub/delete/id/$id");
        $form->setDefaults($organization);
        $this->view->form = $form;
        $this->view->assign('id', $id);
        $this->render($v);
    }
    
    /**
     * Display the form for creating a new organization.
     */
    public function createAction()
    {
        Fisma_Acl::requirePrivilege('organization', 'create'); 
        $form = $this->_getOrganizationForm();
        $orgValues = $this->_request->getPost();
        
        if ($orgValues) {
            if ($form->isValid($orgValues)) {
                $orgValues = $form->getValues();
                $organization = new Organization();
                $organization->merge($orgValues);
                
                // save the data, if failure then return false
                if (!$organization->trySave()) {
                    $msg = "Failure in creation";
                    $model = self::M_WARNING;
                } else {
                    $organization->getTable()->getRecordListener()->get('BaseListener')->setOption('disabled', true);
                    // the organization hasn't parent, so it is a root
                    if ((int)$orgValues['parent'] == 0) {
                        $treeObject = Doctrine::getTable('Organization')->getTree();
                        $treeObject->createRoot($organization);
                    // the organization which has parent
                    } else {
                        // insert as a child to a specify parent organization
                        $organization->getNode()->insertAsLastChildOf($organization->getTable()->find($orgValues['parent']));
                    }
                    $msg = "The organization is created";
                    $model = self::M_NOTICE;
                }
                $this->message($msg, $model);
                $this->_forward('view', null, null, array('id' => $organization->id));
                return;
            } else {
                $errorString = Fisma_Form_Manager::getErrors($form);
                // Error message
                $this->message("Unable to create organization:<br>$errorString", self::M_WARNING);
            }
        }
        
        //Display searchbox template
        $this->searchbox();

        $this->view->title = "Create ";
        $this->view->form = $form;
        $this->render('create');
    }

    /**
     * Delete a specified organization.
     * 
     */
    public function deleteAction()
    {
        Fisma_Acl::requirePrivilege('organization', 'delete');
        $id = $this->_request->getParam('id');
        $organization = Doctrine::getTable('Organization')->find($id);
        if ($organization) {
            if ($organization->delete()) {
                $msg = "Organization deleted successfully";
                $model = self::M_NOTICE;
            } else {
                $msg = "Failed to delete the Organization";
                $model = self::M_WARNING;
            }
            $this->message($msg, $model);
        }
        $this->_forward('list');
    }

    /**
     * Update organization information after submitting an edit form.
     *
     * @todo cleanup this function
     */
    public function updateAction()
    {
        Fisma_Acl::requirePrivilege('organization', 'update'); 
        $id = $this->_request->getParam('id', 0);
        $organization = new Organization();
        $organization = $organization->getTable()->find($id);
        
        if (!$organization) {
            throw new Exception_General("Invalid organization ID");
        }
        
        $form = $this->_getOrganizationForm($organization);
        $orgValues = $this->_request->getPost();
        
        if ($form->isValid($orgValues)) {
            $isModify = false;
            $orgValues = $form->getValues();
            $organization->merge($orgValues);

            if ($organization->isModified()) {
                $organization->save();
                $isModify = true;
            }
            // if the organization is not the root and 
            // its parent id is not equal the value submited
            if (!$organization->getNode()->isRoot() && 
                    (int)$orgValues['parent'] != $organization->getNode()->getParent()->id) {
                // then move this organization to an other parent node
                $organization->getNode()
                ->moveAsLastChildOf(Doctrine::getTable('Organization')->find($orgValues['parent']));
                $isModify = true;
            }
            
            if ($isModify) {
                $msg = "The organization is saved";
                $model = self::M_NOTICE;
            } else {
                $msg = "Nothing changes";
                $model = self::M_WARNING;
            }
            $this->message($msg, $model);
            $this->_forward('view', null, null, array('id' => $organization->id));
        } else {
            $errorString = Fisma_Form_Manager::getErrors($form);
            // Error message
            $this->message("Unable to update organization<br>$errorString", self::M_WARNING);
            // On error, redirect back to the edit action.
            $this->_forward('view', null, null, array('id' => $id, 'v' => 'edit'));
        }
    }
    
    /**
     * Display organizations and systems in tree mode for quick restructuring of the
     * organizational hiearchy.
     */
    public function treeAction() 
    {
        $this->searchbox();
        $this->render('tree');        
    }
    
    /**
     * Returns a JSON object that describes the organization tree, including systems
     */
    public function treeDataAction() 
    {
        Fisma_Acl::requirePrivilege('organization', 'read', '*');
        
        // Doctrine supports the idea of using a base query when populating a tree. In our case, the base
        // query selects all Organizations which the user has access to.
        if ('root' == Zend_Auth::getInstance()->getIdentity()->username) {
            $userOrgQuery = Doctrine_Query::create()
                            ->select('o.name, o.nickname, o.orgType, s.type')
                            ->from('Organization o')
                            ->leftJoin('o.System s');
        } else {
            $userOrgQuery = Doctrine_Query::create()
                            ->select('o.name, o.nickname, o.orgType, s.type')
                            ->from('Organization o')
                            ->innerJoin('o.Users u')
                            ->leftJoin('o.System s')
                            ->where('u.id = ?', $this->_me->id);
        }
        $orgTree = Doctrine::getTable('Organization')->getTree();
        $orgTree->setBaseQuery($userOrgQuery);
        $organizations = $orgTree->fetchTree();
        $orgTree->resetBaseQuery();
        
        $organizations = $this->toHierarchy($organizations);
        
        $this->view->treeData = $organizations;        
    }

    /**
     * Transform the flat array returned from Doctrine's nested set into a nested array
     * 
     * Doctrine should provide this functionality in a future
     * 
     * @todo review the need for this function in the future
     */
    public function toHierarchy($collection) 
    { 
        // Trees mapped 
        $trees = array(); 
        $l = 0; 
        if (count($collection) > 0) { 
            // Node Stack. Used to help building the hierarchy 
            $rootLevel = $collection[0]->level;

            $stack = array(); 
            foreach ($collection as $node) { 
                $item = ($node instanceof Doctrine_Record) ? $node->toArray() : $node;
                $item['level'] -= $rootLevel;
                $item['label'] = $item['nickname'] . ' - ' . $item['name'];
                $item['orgType'] = $node->getType();
                $item['orgTypeLabel'] = $node->getOrgTypeLabel();                
                $item['children'] = array();
                // Number of stack items 
                $l = count($stack); 
                // Check if we're dealing with different levels 
                while ($l > 0 && $stack[$l - 1]['level'] >= $item['level']) { 
                    array_pop($stack); 
                    $l--; 
                } 
                // Stack is empty (we are inspecting the root) 
                if ($l == 0) { 
                    // Assigning the root node 
                    $i = count($trees); 
                    $trees[$i] = $item; 
                    $stack[] = & $trees[$i]; 
                } else { 
                    // Add node to parent 
                    $i = count($stack[$l - 1]['children']); 
                    $stack[$l - 1]['children'][$i] = $item; 
                    $stack[] = & $stack[$l - 1]['children'][$i]; 
                } 
            } 
        } 
        return $trees; 
    }    
}
