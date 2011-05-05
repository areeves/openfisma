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
 * Gearman Test Controller *
 * @author     Christian Smith <christian.smith@endeavorsystems.com>
 * @copyright  (c) Endeavor Systems, Inc. 2011 {@link http://www.endeavorsystems.com}
 * @license    http://www.openfisma.org/content/license GPLv3
 * @package    Controller
 */

class GearmanController extends Fisma_Zend_Controller_Action_Object
{

    protected $_modelName = 'Gearman';

    public function testAction()
    {
        $values = 'test';
        $client = new Fisma_Gearman_Client;
        $client->doBackground('test', $values);
        $this->_redirect('/gearman/list');
    }

    public function tasksAction()
    {
        $client = new Fisma_Gearman_Client();
        $items = array("item1", "item2", "item3", "item4", "item5");

        foreach ($items as $item)
        {
            $client->addTaskBackground('test', $item);
        }
        $client->runTasks();
        $this->_redirect('/gearman/list');
    }

    public function statusAction()
    {
        $this->_redirect('/gearman/list');
    }

    public function jobstatusAction()
    {
       $jobId = $this->_request->getParam('jobId');
       if (isset($jobId)) {
           $this->view->status = $client->jobStatus($jobId);
       }
       else {
           $this->_redirect('/gearman/status/');
       }
    }

    public function virusAction()
    {
        $uploadForm = Fisma_Zend_Form_Manager::loadForm('virusscan');
        $uploadForm = Fisma_Zend_Form_Manager::prepareForm($uploadForm);
        $uploadForm->setAttrib('enctype', 'multipart/form-data');
        $uploadForm->selectFile->setDestination(Fisma::getPath('data') . '/uploads/virusscan');
        $this->view->assign('uploadForm', $uploadForm);
        $postValues = $this->_request->getPost();
        if ($postValues) {
            if ($uploadForm->isValid($postValues) && $fileReceived = $uploadForm->selectFile->receive()) {
                $filePath = $uploadForm->selectFile->getTransferAdapter()->getFileName('selectFile');
                $values = $uploadForm->getValues();
                $values['filepath'] = $filePath;
                $gearmanClient = new Fisma_Gearman_Client;
                $gearmanClient->doBackground('antivirus', $values);
                $this->_redirect('/gearman/list');
            }
        }
    }

    public function indexAction()
    {
    }

    public function scanAction()
    {
        $this->_acl->requirePrivilegeForClass('create', 'Vulnerability');

        // Load the vulnerability plugin form
        $uploadForm = Fisma_Zend_Form_Manager::loadForm('vulnerability_upload');
        $uploadForm = Fisma_Zend_Form_Manager::prepareForm($uploadForm);
        $uploadForm->setAttrib('id', 'injectionForm');

        // Populate the drop menu options
        $networks = Doctrine::getTable('Network')->findAll()->toArray();
        $networkList = array();
        foreach ($networks as $network) {
            $networkList[$network['id']] = $network['nickname'] . ' - ' . $network['name'];
        }
        asort($networkList, SORT_STRING);
        $uploadForm->network->addMultiOption('', '');
        $uploadForm->network->addMultiOptions($networkList);

        // Configure the file select
        $uploadForm->setAttrib('enctype', 'multipart/form-data');
        $uploadForm->selectFile->setDestination(Fisma::getPath('data') . '/uploads/scanreports');

        // Setup the view
        $this->view->assign('uploadForm', $uploadForm);

        // Handle the file upload, if necessary
        $fileReceived = false;
        $postValues = $this->_request->getPost();
        if ($postValues) {
            if ($uploadForm->isValid($postValues) && $fileReceived = $uploadForm->selectFile->receive()) {
                $filePath = $uploadForm->selectFile->getTransferAdapter()->getFileName('selectFile');
                $values = $uploadForm->getValues();
                $values['filepath'] = $filePath;
                $gearmanClient = new Fisma_Gearman_Client;
                $gearmajClient->doBackground('scan', $values);
                $this->_redirect('/gearman/list');
            } else {
                $errorString = Fisma_Zend_Form_Manager::getErrors($uploadForm);

                if (!$fileReceived) {
                    $errorString .= "File not received<br>";
                }

                // Error message
                $this->view->priorityMessenger("Scan upload failed:<br>$errorString", 'warning');
            }
            // This is a hack to make the submit button work with YUI:
            /** @yui */ $uploadForm->upload->setValue('Upload');
            $this->render(); // Not sure why this view doesn't auto-render?? It doesn't render when the POST is set.
        }
    }
}
