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
    protected $_userId;

    protected $_modelName = 'Task';

    public function init()
    {
        parent::init();
        $this->_userId = CurrentUser::getInstance()->id;

        $this->_helper->fismaContextSwitch()
                      ->setActionContext('status-data', 'json')
                      ->setActionContext('status-task', 'json')
                      ->initContext();
    }

    public function testAction()
    {
        $values = 'test';
        $client = new Fisma_Gearman_Client;
        $client->doBackground('test', $values);
        $script = "Fisma.Task.start;";
        //$this->_redirect('/gearman/list');
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
        if ($this->getRequest()->getParam('id')) {
            $this->_helper->layout()->setLayout('ajax');
            $this->_helper->viewRenderer->setNoRender(true);
/*            $query = Doctrine_Query::create()
                    ->from('Task t')
                    ->where('userid = ?', $userid)
            //->andWhere('t.status', 'running')
                    ->setHydrationMode(Doctrine::HYDRATE_ARRAY);
            $userTasks = $query->execute();
*/
            $query = Doctrine_Query::create()
                    ->select('userid, status, count(*)')
                    ->from('Task')
                    ->andWhere('userid = ?', $this->_userId)
                    ->whereIn('status', array('pending', 'running', 'finished'))
                    ->orderBy('status');

            $this->view->results = $query->fetchArray();
        }
    }

    public function statusTaskAction()
    {
            $this->_helper->layout()->setLayout('ajax');
            $this->_helper->viewRenderer->setNoRender(true);
            $query = Doctrine_Query::create()
                    ->select('id, worker, status, progress, success')
                    ->from('Task')
                    ->andWhere('userid = ?', $this->_userId);

            $taskInfo = $query->fetchArray();
            $this->view->task = $taskInfo;
    }

    public function statusDataAction()
    {
        $this->_helper->layout()->setLayout('ajax');
        $this->_helper->viewRenderer->setNoRender(true);
        $userId = CurrentUser::getInstance()->id;

        /*
        $query = Doctrine_Query::create()
                ->from('Task t')
                ->where('userid = ?', $userid)
                //->andWhere('t.status', 'running')
                ->setHydrationMode(Doctrine::HYDRATE_ARRAY);
        $userTasks = $query->execute();
        */

        /*
        $query = Doctrine_Query::create()
                select("IFNULL(pending,0) AS pending, IFNULL(running,0) AS running, IFNULL(finished,0) AS finished, IFNULL(failed,0) AS failed")
                FROM (SELECT COUNT(status) AS pending FROM Task where status='pending') AS t1,
                (SELECT COUNT(status) AS running FROM Task WHERE status='running') AS t2,
                (SELECT COUNT(status) AS finished FROM Task WHERE status='finished') AS t3,
                (SELECT count(status) AS failed FROM Task WHERE status='failed') AS t4"
        )
        */

        $query = Doctrine_Query::create()
                ->select('status, count(*)')
                ->from('Task')
                ->whereIn('status', array('failed', 'pending', 'running', 'finished'))
                ->andWhere('userid = ?', $this->_userId)
                ->groupBy('status');
        $statusCount = $query->fetchArray();


        foreach ($statusCount as $status) {
           $count[$status['status']] = $status['count'];
        }

        foreach (array('failed', 'pending', 'running', 'finished') as $value) {
            if (!isset($count[$value])) {
                $count[$value] = 0;
            }
        }

        if ($id = $this->_request->getParam('id')) {
            $tasksRunningQuery = Doctrine_Query::create()
                    ->select()
                    ->from('Task')
                    ->where('userId = ?', $this->_userId)
                    ->andWhere('id = ?', $id)
                    ->orderBy('id')
                    ->limit(1);
            $running = $tasksRunningQuery->fetchArray();
        } else {
            $tasksRunningQuery = Doctrine_Query::create()
                    ->select()
                    ->from('Task')
                    ->where('userId = ?', $this->_userId)
                    ->andWhere('status = ?', 'running')
                    ->orderBy('id')
                    ->limit(1);
            $running = $tasksRunningQuery->fetchArray();
        }
        $array['running'] = $running[0];
        $array['count'] = $count;
        $this->view->tasks = $array;

        /*
        $tasksFinishedQuery = Doctrine_Query::create()
                ->select()
                ->from('Task')
                ->where('userId = ?', $this->_userId)
                ->andWhere('status = ?', 'finished');
        $status['finished'] = $tasksFinishedQuery->fetchArray();

        $this->view->status = $status;
        */
    }

    /**
     * @return void
     */
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

    /**
     * @return void
     */
    public function indexAction()
    {
    }

    /**
     * @return void
     */
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
                $gearmanClient->doBackground('scan', $values);
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
