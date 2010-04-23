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
 * The network controller handles searching, displaying, creating, and updating
 * network objects.
 *
 * @author     Ryan Yang <ryan@users.sourceforge.net>
 * @copyright  (c) Endeavor Systems, Inc. 2009 {@link http://www.endeavorsystems.com}
 * @license    http://www.openfisma.org/content/license GPLv3
 * @package    Controller
 * @version    $Id$
 */
class NetworkController extends BaseController
{
    
    /**
     * The main name of the model.
     * 
     * This model is the main subject which the controller operates on.
     * 
     * @var string
     */
    protected $_modelName = 'Network';

    /**
     * Initialize internal members.
     * 
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->_helper->contextSwitch()
                      ->addActionContext('search', 'json')
                      ->setAutoJsonSerialization(false)
                      ->initContext();
    }
    
    /**
     * Override parent
     *
     * @return void
     */
    public function preDispatch()
    {
        $this->view->createNetworkPrivilege = Fisma_Acl::hasPrivilegeForClass('create', 'Network');
        parent::preDispatch();
    }
    
    /**
     * Delete a network
     * 
     * @return void
     */
    public function deleteAction()
    {        
        $id = $this->_request->getParam('id');
        $network = Doctrine::getTable('Network')->find($id);
        
        if (!$network) {
            $msg   = "Invalid Network ID";
            $type = 'warning';
        } else {
            Fisma_Acl::requirePrivilegeForObject('delete', $network);
            
            $assets = $network->Assets->toArray();
            if (!empty($assets)) {
                $msg = 'This network can not be deleted because it is'
                     . ' already associated with one or more assets';
                $type = 'warning';
            } else {
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
     * Override parent to add network object update and delete privilege for view
     *
     * @return void
     */
    public function viewAction()
    {
        $id = $this->getRequest()->getParam('id');
        $network = Doctrine::getTable('Network')->find($id);

        if (!$network) {
            throw new Fisma_Exception("Invalid network ID ($id)");
        }

        $this->view->updateNetworkObjPrivilege = Fisma_Acl::hasPrivilegeForObject('update', $network);
        $this->view->deleteNetworkObjPrivilege = Fisma_Acl::hasPrivilegeForObject('delete', $network);

        parent::viewAction();
    }

    /** 
     * Override BaseController version to do JSON output in the view script.
     * 
     * @return string The encoded table data in json format
     */
    public function searchAction()
    {
        Fisma_Acl::requirePrivilegeForClass('read', 'Network');
        $sortBy = $this->_request->getParam('sortby', 'id');
        $order  = $this->_request->getParam('order');
        $keywords  = html_entity_decode($this->_request->getParam('keywords')); 

        //filter the sortby to prevent sqlinjection
        $networkTable = Doctrine::getTable('Network');
        if (!in_array(strtolower($sortBy), $networkTable->getColumnNames())) {
            return $this->_helper->json('Invalid "sortBy" parameter');
        }

        $order = strtoupper($order);
        if ($order != 'DESC') {
            $order = 'ASC'; //ignore other values
        }
        
        $query  = Doctrine_Query::create()
                    ->from('Network')
                    ->orderBy("$sortBy $order")
                    ->limit($this->_paging['count'])
                    ->offset($this->_paging['startIndex']);

        if (!empty($keywords)) {
            // lucene search 
            $index = new Fisma_Index('Network');
            $ids = $index->findIds($keywords);
            if (empty($ids)) {
                $ids = array(-1);
            }
            $query->whereIn('id', $ids);
        }
        $rows = $query->execute();
        $rows = $this->handleCollection($rows);
        
        $tableData = array('table' => array(
                         'recordsReturned' => count($rows),
                         'totalRecords'    => $query->count(),
                         'startIndex'      => $this->_paging['startIndex'],
                         'sort'            => $sortBy,
                         'dir'             => $order,
                         'pageSize'        => $this->_paging['count'],
                         'records'         => $rows
                     ));
        $this->view->tableData = $tableData;
    }
}
