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
 * Handles CRUD for "system" objects.
 *
 * @package   Controller
 * @copyright (c) Endeavor Systems, Inc. 2008 (http://www.endeavorsystems.com)
 * @license   http://www.openfisma.org/mw/index.php?title=License
 */
class SystemController extends SecurityController
{
    private $_paging = array(
        'mode' => 'Sliding',
        'append' => false,
        'urlVar' => 'p',
        'path' => '',
        'currentPage' => 1,
        'perPage' => 20
    );
    private $_user = null;
    protected $_sanity = array(
        'data' => 'system',
        'filter' => array(
            '*' => array(
                'StringTrim',
                'StripTags'
            )
        ) ,
        'validator' => array(
            'name' => array('Alnum' => true),
            'nickname' => array('Alnum' => true),
            'primary_office' => 'Digits',
            'confidentiality' => 'NotEmpty',
            'integrity' => 'NotEmpty',
            'availability' => 'NotEmpty',
            'type' => 'NotEmpty',
            'desc' => array(
                'allowEmpty' => TRUE
            ) ,
            'criticality_justification' => array(
                'allowEmpty' => TRUE
            ) ,
            'sensitivity_justification' => array(
                'allowEmpty' => TRUE
            )
        ) ,
        'flag' => TRUE
    );

    /**
     * @todo english
     * Initialize this Class
     */
    public function init()
    {
        parent::init();
        $this->_system = new System();
    }

    /**
     * @todo english
     * Invoked before each Action
     */
    public function preDispatch()
    {

        $req = $this->getRequest();
        $this->_pagingBasePath = $req->getBaseUrl() .
            '/panel/system/sub/list';
        $this->_paging['currentPage'] = $req->getParam('p', 1);
    }

    /**
     * Returns the standard form for creating, reading, and updating systems.
     *
     * @return Zend_Form
     */
    public function getSystemForm()
    {
        $form = Form_Manager::loadForm('system');
        
        $db = $this->_system->getAdapter();
        $query = $db->select()->from(array('o'=>'organizations'), '*');
        $ret =  $db->fetchAll($query);
        if (!empty($ret)) {
            foreach ($ret as $row) {
                $form->getElement('organization_id')->addMultiOptions(array($row['id'] => $row['name']));
            }
        }
        
	$visibility_array = array("visible"=>"Visible", "hidden"=>"Hidden");
        $form->getElement('visibility')->addMultiOptions($visibility_array);
        
        $array = $this->_system->getEnumColumns('confidentiality');
        $form->getElement('confidentiality')->addMultiOptions(array_combine($array, $array));
        
        $array = $this->_system->getEnumColumns('integrity');
        $form->getElement('integrity')->addMultiOptions(array_combine($array, $array));
        
        $array = $this->_system->getEnumColumns('availability');
        $form->getElement('availability')->addMultiOptions(array_combine($array, $array));
        
        $type = $this->_system->getEnumColumns('type');
        $form->getElement('type')->addMultiOptions(array_combine($type, $type));
        
        return Form_Manager::prepareForm($form);
    }

    /**
     * list the systems from the search, if search none, it list all systems
     */     
    public function listAction()
    {
        $this->_acl->requirePrivilege('admin_systems', 'read');
        //Display searchbox template
        $this->searchbox();
        
        $value = trim($this->_request->getParam('qv'));
        $show_hidden = trim($this->_request->getParam('sh'));
        $db = $this->_system->getAdapter();
        $query = $db->select()->from(array('s'=>'systems'), 's.*')
                               ->join(array('o'=>'organizations'), 's.organization_id = o.id',
                                   array('organization'=>'o.name'))
                               ->order('s.name ASC')
                               ->limitPage($this->_paging['currentPage'], $this->_paging['perPage']);
        if (!empty($value)) {
            $cache = Config_Fisma::getCacheInstance();
            //@todo english  get search results in ids
            $systemIds = $cache->load($this->_me->id . '_system');
            if (!empty($systemIds)) {
                $ids = implode(',', $systemIds);
            } else {
                //@todo english  set ids as a not exist value in database if search results is none.
                $ids = -1;
            }
            $query->where('s.id IN (' . $ids . ')');
        }

	if (empty($show_hidden)) {
            $query->where('s.visibility="visible"');
	}
	else {
            $query->where('s.visibility="hidden"');
	}

        $systemList = $db->fetchAll($query);
        $this->view->assign('system_list', $systemList);
        $this->render('list');
    }

    /**
     *  Render the form for searching the systems.
     */
    public function searchbox()
    {
        $this->_acl->requirePrivilege('admin_systems', 'read');
        
        $qv = trim($this->_request->getParam('qv'));
        if (!empty($qv)) {
            //@todo english  if system index dosen't exist, then create it.
            if (!is_dir(Config_Fisma::getPath('data') . '/index/system/')) {
                $this->createIndex();
            }
            $ret = Config_Fisma::searchQuery($qv, 'system');
            $count = count($ret);
        } else {
            $count = $this->_system->count();
        }

        $this->_paging['totalItems'] = $count;
        $this->_paging['fileName'] = "{$this->_pagingBasePath}/p/%d";
        $pager = & Pager::factory($this->_paging);
        $this->view->assign('qv', $qv);
        $this->view->assign('total', $count);
        $this->view->assign('links', $pager->getLinks());
        $this->render('searchbox');
    }

    /**
     * Display the form for creating a new system.
     */
    public function createAction()
    {
        $this->_acl->requirePrivilege('admin_systems', 'create');
        
        $form = $this->getSystemForm('system');
        $system = $this->_request->getPost();
        if ($system) {
            if ($form->isValid($system)) {
                $system = $form->getValues();
                unset($system['save']);
                unset($system['reset']);

                $systemId = $this->_system->insert($system);
                if (! $systemId) {
                    //@REVIEW 3 lines
                    $msg = "Failure in creation";
                    $model = self::M_WARNING;
                } else {
                    $this->_notification
                         ->add(Notification::SYSTEM_CREATED,
                             $this->_me->account, $systemId);

                    //Create a system index
                    if (is_dir(Config_Fisma::getPath('data') . '/index/system/')) {
                        $organization = new Organization();
                        $ret = $organization->find($system['organization_id'])->current();
                        if (!empty($ret)) {
                            $system['organization'] = $ret->name . ' ' . $ret->nickname;
                            unset($system['organization_id']);
                            Config_Fisma::updateIndex('system', $systemId, $system);
                        }
                    }

                    $msg = "The system is created";
                    $model = self::M_NOTICE;
                }
                $this->message($msg, $model);
                $this->_forward('view', null, null, array('id' => $systemId));
                return;
            } else {
                $errorString = Form_Manager::getErrors($form);
                // Error message
                $this->message("Unable to create system:<br>$errorString", self::M_WARNING);
            }
        }
        //Display searchbox template
        $this->searchbox();

        $this->view->title = "Create ";
        $this->view->form = $form;
        $this->render('create');
    }

    /**
     *  Delete a specified system.
     */
    public function deleteAction()
    {
        $this->_acl->requirePrivilege('admin_systems', 'delete');
        
        $errno = 0;
        $req = $this->getRequest();
        $id = $req->getParam('id');
        $db = $this->_system->getAdapter();
        $qry = $db->select()->from('poams')
             ->where('system_id = ' . $id);
        $resultA = $db->fetchAll($qry);
        $qry->reset();
        $qry = $db->select()->from('assets')
            ->where('system_id = ' . $id);
        $resultB = $db->fetchAll($qry);
        if (!empty($resultA) || !empty($resultB)) {
            $msg = "This system cannot be deleted because it is already".
                   " associated with one or more POAMS or assets";
            $model = self::M_WARNING;
        } else {
            $res = $this->_system->delete('id = ' . $id);
            if (!$res) {
                $errno++;
            }
            if ($errno > 0) {
                $msg = "Failed to delete the system";
                $model = self::M_WARNING;
            } else {
                $this->_notification
                     ->add(Notification::SYSTEM_DELETED,
                        $this->_me->account, $id);

                //Delete this system index
                if (is_dir(Config_Fisma::getPath('data') . '/index/system/')) {
                    Config_Fisma::deleteIndex('system', $id);
                }

                $msg = "System deleted successfully";
                $model = self::M_NOTICE;
            }
        }
        $this->message($msg, $model);
        $this->_forward('list');
    }
    
    /**
     * Display a single system record with all details.
     */
    public function viewAction()
    {
        $this->_acl->requirePrivilege('admin_systems', 'read');
        //Display searchbox template
        $this->searchbox();
        
        $form = $this->getSystemForm();
        $id = $this->_request->getParam('id');
        $v = $this->_request->getParam('v', 'view');

        $res = $this->_system->find($id)->toArray();
        $system = $res[0];

        $organization = new Organization();
        $res = $organization->find($system['organization_id'])->toArray();
        if (!empty($res)) {
            $organizationName = $res[0]['name'];
        } else {
            $organizationName = 'NONE';
        }
        $system['organization'] = $organizationName;

        if ($v == 'edit') {
            $this->view->assign('viewLink',
                                "/panel/system/sub/view/id/$id");
            $form->setAction("/panel/system/sub/update/id/$id");
        } else {
            // In view mode, disable all of the form controls
            $this->view->assign('editLink',
                                "/panel/system/sub/view/id/$id/v/edit");
            $form->setReadOnly(true);            
        }
        $form->setDefaults($system);
        $this->view->form = $form;
        $this->view->assign('id', $id);
        $this->render($v);
    }

    /**
     * Updates system information after submitting an edit form.
     *
     * @todo cleanup this function
     */
    public function updateAction ()
    {
        $this->_acl->requirePrivilege('admin_systems', 'update');
        
        $form = $this->getSystemForm();
        $formValid = $form->isValid($_POST);
        $system = $form->getValues();

        $id = $this->_request->getParam('id');
        $ret = $this->_system->find($id)->current();
        if (!empty($ret)) {
            $query = $ret->name . ' ' . $ret->nickname;
        } else {
            throw new Exception_General("The system posted is not a valid system");
        }
        $data['system'] = $system['name'] . ' ' . $system['nickname'];

        if ($formValid) {
            unset($system['save']);
            unset($system['reset']);

            $res = $this->_system->update($system, 'id = ' . $id);
            if ($res) {
                //@REVIEW 3 lines
                $this->_notification
                     ->add(Notification::SYSTEM_MODIFIED,
                         $this->_me->account, $id);

                //Update findings index
                if (is_dir(Config_Fisma::getPath('data') . '/index/finding') && $query!= $data['system']) {
                    $index = new Zend_Search_Lucene(Config_Fisma::getPath('data') . '/index/finding');
                    $hits = $index->find('system:'.$query);
                    $ids = array();
                    foreach ($hits as $hit) {
                        $ids[] = $hit->id;
                        $x[] = $hit->rowId;
                    }
                    Config_Fisma::updateIndex('finding', $ids, $data);
                }

                //Update this system index
                if (is_dir(Config_Fisma::getPath('data') . '/index/system/')) {
                    $organization = new Organization();
                    $ret = $organization->find($system['organization_id'])->current();
                    if (!empty($ret)) {
                        $system['organization'] = $ret->name . ' ' . $ret->nickname;
                        unset($system['organization_id']);
                        Config_Fisma::updateIndex('system', $id, $system);
                    }
                }

                $msg = "The system is saved";
                $model = self::M_NOTICE;
            } else {
                $msg = "Nothing changes";
                $model = self::M_WARNING;
            }
            $this->message($msg, $model);
            $this->_forward('view', null, null, array('id' => $id));
        } else {
            $errorString = Form_Manager::getErrors($form);
            // Error message
            $this->message("Unable to update system:<br>$errorString", self::M_WARNING);
            // On error, redirect back to the edit action.
            $this->_forward('view', null, null, array('id' => $id, 'v' => 'edit'));
        }
    }

    /**
     * Create systems Lucene Index
     */
    protected function createIndex()
    {
        $index = new Zend_Search_Lucene(Config_Fisma::getPath('data') . '/index/system', true);
        $query = $this->_system->getAdapter()->select()->from(array('s'=>'systems'), 's.*')
                              ->join(array('o'=>'organizations'), 's.organization_id = o.id',
                                     array('org_name'=>'o.name', 'org_nickname'=>'o.nickname'));
        $list = $this->_system->getAdapter()->fetchAll($query);
        set_time_limit(0);
        if (!empty($list)) {
            foreach ($list as $row) {
                $doc = new Zend_Search_Lucene_Document();
                $doc->addField(Zend_Search_Lucene_Field::UnStored('key', md5($row['id'])));
                $doc->addField(Zend_Search_Lucene_Field::UnIndexed('rowId', $row['id']));
                $doc->addField(Zend_Search_Lucene_Field::UnStored('name', $row['name']));
                $doc->addField(Zend_Search_Lucene_Field::UnStored('nickname', $row['nickname']));
                $doc->addField(Zend_Search_Lucene_Field::UnStored('organization',
                            $row['org_name'] . ' ' . $row['org_nickname']));
                $doc->addField(Zend_Search_Lucene_Field::UnStored('desc', $row['desc']));
                $doc->addField(Zend_Search_Lucene_Field::UnStored('type', $row['type']));
                $doc->addField(Zend_Search_Lucene_Field::UnStored('confidentiality', $row['confidentiality']));
                $doc->addField(Zend_Search_Lucene_Field::UnStored('integrity', $row['integrity']));
                $doc->addField(Zend_Search_Lucene_Field::UnStored('availability', $row['availability']));
                $doc->addField(Zend_Search_Lucene_Field::UnStored('confidentiality_justification',
                            $row['confidentiality_justification']));
                $doc->addField(Zend_Search_Lucene_Field::UnStored('integrity_justification',
                            $row['integrity_justification']));
                $doc->addField(Zend_Search_Lucene_Field::UnStored('availability_justification',
                            $row['availability_justification']));
                $index->addDocument($doc);
            }
            $index->optimize();
        }
    }
}
