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
 * @version   $Id: FindingController.php 1090 2008-10-30 21:01:17Z mehaase $
 */

/**
 * @todo move this definition into the class as a constant
 */
define('TEMPLATE_NAME', "OpenFISMA_Injection_Template.xls");

/**
 * The finding controller is used for searching, displaying, and updating
 * findings.
 *
 * @package   Controller
 * @copyright (c) Endeavor Systems, Inc. 2008 (http://www.endeavorsystems.com)
 * @license   http://www.openfisma.org/mw/index.php?title=License
 */
class FindingController extends PoamBaseController
{
    /**
     Provide searching capability of findings
     Data is limited in legal systems.
     */
    protected function _search($criteria)
    {
        $fields = array(
            'id',
            'legacy_finding_id',
            'ip',
            'port',
            'status',
            'source_id',
            'system_id',
            'discover_ts',
            'count' => 'count(*)'
        );
        if ($criteria['status'] == 'REMEDIATION') {
            $criteria['status'] = array(
                'OPEN',
                'EN',
                'EP',
                'ES'
            );
        }
        $result = $this->_poam->search($this->_me->systems, $fields, $criteria,
                     $this->_paging['currentPage'], $this->_paging['perPage']);
        $total = array_pop($result);
        $this->_paging['totalItems'] = $total;
        $pager = & Pager::factory($this->_paging);
        $this->view->assign('findings', $result);
        $this->view->assign('links', $pager->getLinks());
        $this->render('search');
    }
    /**
     Get finding detail infomation
     */
    public function viewAction()
    {
        $req = $this->getRequest();
        $id = $req->getParam('id', 0);
        assert($id);
        $this->view->assign('id', $id);
        if (Config_Fisma::isAllow('finding', 'read')) {
            $sys = new System();
            $poam = new Poam();
            $detail = $poam->find($id)->current();
            $this->view->finding = $poam->getDetail($id);
            $this->view->finding['system_name'] = 
                    $this->_systemList[$this->view->finding['system_id']];
            $this->render();
        } else {
            /// @todo Add a new Excption page to indicate Access denial
            $this->render();
        }
    }
    /**
     Edit finding infomation
     */
    public function editAction()
    {
        $req = $this->getRequest();
        $id = $req->getParam('id');
        assert($id);
        $finding = new Finding();
        $do = $req->getParam('do');
        if ($do == 'update') {
            $status = $req->getParam('status');
            $db = Zend_Registry::get('db');
            $result = $db->query("UPDATE FINDINGS SET finding_status = '$status'
                                  WHERE finding_id = $id");
            if ($result) {
                $this->view->assign('msg', "Finding updated successfully");
            } else {
                $this->view->assign('msg', "Failed to update the finding");
            }
        }
        $this->view->assign('act', 'edit');
        $this->_forward('view', 'Finding');
    }
    /**
     *  Spreadsheet upload
     *
     *  The spreadsheet should be a CSV file in fact. It parse the valid data
     *  and leave the
     *  remaining to the user.
     */
    public function injectionAction()
    {
        $this->_helper->actionStack('header', 'Panel');
        if (Config_Fisma::isAllow('finding', 'create')) {
            $csvFile = isset($_FILES['csv']) ? $_FILES['csv'] : array();
            if (!empty($csvFile)) {
                if ($csvFile['size'] < 1) {
                    $errMsg = 'Error: Empty file.';
                } else {
                    if ($csvFile['size'] > 1048576) {
                        $errMsg = 'Error: File is too big.';
                    }
                    if (preg_match('/\x00|\xFF/',
                        file_get_contents($csvFile['tmp_name']))) {
                        $errMsg = 'Error: Binary file.';
                    }
                    if ($csvFile['error']) {
                        $errMsg = 'Encountered an unknown error while
                                   processing the file';
                    }
                }
            }
            if (!empty($errMsg)) {
                $this->message($errMsg, self::M_WARNING);
                $this->render();
                return;
            }
            if (!empty($csvFile)) {
                $fileName = $csvFile['name'];
                $tempFile = $csvFile['tmp_name'];
                $fileSize = $csvFile['size'];
                $failedArray = $succeedArray = array();
                $handle = fopen($tempFile, 'r');
                $data = fgetcsv($handle, 1000, ",", '"'); //skip the first line
                $data = fgetcsv($handle, 1000, ",", '"'); //skip the second line
                $row = 0;
                while ($data = fgetcsv($handle, 1000, ",", '"')) {
                    if (implode('', $data) != '') {
                        $row++;
                        $ret = $this->insertCsvRow($data);
                        if (empty($ret)) {
                            $failedArray[] = $data;
                        } else {
                            $poamIds[] = $ret;                        
                            $succeedArray[] = $data;
                        }
                    }
                }
                fclose($handle);
                $summaryMsg = "You have uploaded a CSV file which contains
                               $row line(s) of data.<br />";
                if (count($failedArray) > 0) {
                    $tempFile = 'temp/csv_' . date('YmdHis') . '_' .
                                 rand(10, 99) . '.csv';
                    $fp = fopen($tempFile, 'w');
                    foreach ($failedArray as $fail) {
                        fputcsv($fp, $fail);
                    }
                    fclose($fp);
                    $summaryMsg.= count($failedArray) . " line(s) cannot be 
parsed successfully. This is likely due to an unexpected datatype or the use of
a datafield which is not currently in the database. Please ensure your csv file
matches the data rows contained <a href='/$tempFile'>here</a> in the spreadsheet
template. Please update your CSV file and try again.<br />";
                }
                if (count($succeedArray) > 0) {
                    $summaryMsg.= count($succeedArray) . " line(s) parsed and
                         injected successfully. <br />";
                }
                if (count($succeedArray) == $row) {
                    $summaryMsg.= " Congratulations! All of the lines contained
                        in the CSV were parsed and injected successfully.";
                }
                
                $this->view->assign('error_msg', $summaryMsg);
            }
            $this->render();
        }
    }
    /**
     *  Create a finding manually
     */
    public function createAction()
    {
        if ("new" == $this->_request->getParam('is')) {
            $poam = $this->_request->getPost('poam');
            try {
                if (!empty($poam['asset_id'])) {
                    $asset = new Asset();
                    $ret = $asset->find($poam['asset_id']);
                    $poam['system_id'] = $ret->current()->system_id;
                }
                // Validate that the user has selected a finding source
                if ($poam['source_id'] == 0) {
                    throw new FismaException(
                        "You must select a finding source"
                    );
                }
                // If the blscr_id is zero, that means the user didn't select
                // a security control, so set the control to null.
                if ($poam['blscr_id'] == 0) {
                    unset($poam['blscr_id']);
                }
                $poam['status'] = 'NEW';
                $discoverTs = new Zend_Date($poam['discover_ts']);
                $poam['discover_ts'] = $discoverTs->toString("Y-m-d");
                $poam['create_ts'] = self::$now->toString("Y-m-d H:i:s");
                $poam['created_by'] = $this->_me->id;
                $poamId = $this->_poam->insert($poam);
                $logContent = "a new finding was created";
                $this->_poam->writeLogs($poamId, $this->_me->id,
                     self::$now->toString('Y-m-d H:i:s'), 'CREATION',
                        $logContent);

                $this->_notification
                     ->add(Notification::FINDING_CREATED,
                           $this->_me->account,
                           "PoamID: $poamId",
                           $poam['system_id']);

                $message = "Finding created successfully";
                $model = self::M_NOTICE;
            }
            catch(Zend_Exception $e) {
                if ($e instanceof FismaException) {
                    $message = $e->getMessage();
                } else {
                    $message = "Failed to create the finding";
                }
                $model = self::M_WARNING;
            }
            $this->message($message, $model);
        }
        $blscr = new Blscr();
        $list = array_keys($blscr->getList('class'));
        $blscrList = array_combine($list, $list);
        $this->view->blscr_list = $blscrList;
        $this->view->assign('system', $this->_systemList);
        $this->view->assign('source', $this->_sourceList);
        $this->render();
    }
    /**
     *  Delete findings
     */
    public function deleteAction()
    {
        $req = $this->getRequest();
        $post = $req->getPost();
        $errno = 0;
        $successno = 0;
        $poam = new poam();
        foreach ($post as $key => $id) {
            if (substr($key, 0, 3) == 'id_') {
                $poamId[] = $id;
                $res = $poam->update(array(
                    'status' => 'DELETED'
                ), 'id = ' . $id);
                if ($res) {
                    $successno++;
                } else {
                    $errno++;
                }
            }
        }
        $msg = 'Delete ' . $successno . ' Findings Successfully,'
                . $errno . ' Failed!';
        // @todo The delete action isn't support right now, but when it is,
        // the notification needs to be limited to the system from which the
        // finding is being deleted.
        //$this->_notification->add(Notification::FINDING_DELETED,
        //    $this->_me->account, $poamId);
        $this->message($msg, self::M_NOTICE);
        $this->_forward('searchbox', 'finding', null, array(
            's' => 'search'
        ));
    }
    /** 
     *  Insert a row of data into database.
     */
    protected function insertCsvRow($row)
    {
        $asset = new Asset();
        $poam = new poam();
        if (!is_array($row) || (count($row) < 7)) {
            return false;
        }
        if (strlen($row[3]) > 63 || (!is_numeric($row[4]) && !empty($row[4]))) {
            return false;
        }
        if (in_array('', array(
            $row[0],
            $row[1],
            $row[2],
            $row[5],
            $row[6]
        ))) {
            return false;
        }
        $row[2] = date('Y-m-d', strtotime($row[2]));
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $row[2])) {
            return false;
        }
        $db = Zend_Registry::get('db');
        $query = $db->select()->from('systems', 'id')
                    ->where('nickname = ?', $row[0]);
        $result = $db->fetchRow($query);
        $row[0] = !empty($result) ? $result['id'] : false;

        $query->reset();
        $query = $db->select()->from('networks', 'id')
                    ->where('nickname = ?', $row[1]);
        $result = $db->fetchRow($query);
        $row[1] = !empty($result) ? $result['id'] : false;

        $query->reset();
        $query = $db->select()->from('sources', 'id')
                    ->where('nickname = ?', $row[5]);
        $result = $db->fetchRow($query);
        $row[5] = !empty($result) ? $result['id'] : false;

        if (!$row[0] || !$row[1] || !$row[5]) {
            return false;
        }
        $assetName = ':' . $row[3] . ':' . $row[4];
        $query = $asset->select()->from($asset, 'id')
                       ->where('system_id = ?', $row[0])
                       ->where('network_id = ?', $row[1])
                       ->where('address_ip = ?', $row[3])
                       ->where('address_port = ?', $row[4]);
        $result = $asset->fetchRow($query);
        if (!empty($result)) {
            $data = $result->toArray();
            $assetId = $data['id'];
        } else {
            $assetData = array(
                'name' => $assetName,
                'create_ts' => $row[2],
                'source' => 'SCAN',
                'system_id' => $row[0],
                'network_id' => $row[1],
                'address_ip' => $row[3],
                'address_port' => $row[4]
            );
            $assetId = $asset->insert($assetData);
        }
        $poamData = array(
            'asset_id' => $assetId,
            'source_id' => $row[5],
            'system_id' => $row[0],
            'status' => 'NEW',
            'create_ts' => self::$now->toString('Y-m-d h:i:s') ,
            'discover_ts' => $row[2],
            'finding_data' => $row[6]
        );
        $ret = $poam->insert($poamData);
        $this->_notification
             ->add(Notification::FINDING_INJECT,
                   $this->_me->account,
                   "PoamId: $ret",
                   $poamData['system_id']);
        return $ret;
    }
    /** 
     * Downloading a excel file which is used as a template 
     * for uploading findings.
     * systems, networks and sources are extracted from the
     * database dynamically.
     */
    public function templateAction()
    {
        $contextSwitch = $this->_helper->getHelper('contextSwitch');
        $contextSwitch->addContext('xls', array(
            'suffix' => 'xls',
            'headers' => array(
                'Content-type' => 'application/vnd.ms-excel',
                'Content-Disposition' => 'filename=' . TEMPLATE_NAME
            )
        ));
        $contextSwitch->addActionContext('template', 'xls');
        /* The spreadsheet won't open in Excel if any of these tables are 
         * empty. So we explicitly check for that condition, and if it 
         * exists then we show the user an error message explaining why 
         * the spreadsheet isn't available.
         */
        try {
            $src = new System();
            $this->view->systems = $src->getList('nickname',
                $this->_me->systems);
            if (count($this->view->systems) == 0) {
                throw new FismaException(
                    "The spreadsheet template can not be " .
                    "prepared because there are no systems defined.");
            }
            $src = new Network();
            $this->view->networks = $src->getList('nickname');
            if (count($this->view->networks) == 0) {
                 throw new FismaException("The spreadsheet template can not be
                     prepared because there are no networks defined.");
            }
            $src = new Source();
            $this->view->sources = $src->getList('nickname');
            if (count($this->view->networks) == 0) {
                 throw new FismaException("The spreadsheet template can
                     not be prepared because there are no finding sources
                     defined.");
            }
            // Context switch is called only after the above code executes 
            // successfully. Otherwise if there is an error,
            // the error handler will be confused by context switch and will 
            // look for error.xls.tpl instead of error.tpl
            $contextSwitch->initContext('xls');
            $this->render();
        } catch(FismaException $fe) {
            Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
            $this->message($fe->getMessage(), self::M_WARNING);
            $this->_forward('injection', 'Finding');
        }
    }

    /** 
     *  pluginAction() - Import scan results via a plug-in
     */
    public function pluginAction()
    {
        // Load the finding plugin form
        $uploadForm = Form_Manager::loadForm('finding_upload');
        $uploadForm = Form_Manager::prepareForm($uploadForm);

        // Populate the drop menu options
        $uploadForm->plugin->addMultiOption('', '');
        $plugin = new Plugin();
        $pluginList = $plugin->getList('name');
        $uploadForm->plugin->addMultiOptions($pluginList);
        
        $uploadForm->findingSource->addMultiOption('', '');
        $uploadForm->findingSource->addMultiOptions($this->_sourceList);

        $uploadForm->system->addMultiOption('', '');
        $uploadForm->system->addMultiOptions($this->_systemList);

        $uploadForm->network->addMultiOption('', '');
        $uploadForm->network->addMultiOptions($this->_networkList);
        
        // Configure the file select
        $uploadForm->setAttrib('enctype', 'multipart/form-data');
        $uploadForm->selectFile->setDestination(APPLICATION_ROOT . '/data/uploads/scanreports')
                               ->addValidator('Count', false, 1) // ensure only 1 file
                               ->addValidator('Size', false, 102400) // limit to 100K
                               ->addValidator('Extension', false, 'xml');

        // Setup the view
        $this->_helper->actionStack('header', 'Panel');
        $this->view->assign('uploadForm', $uploadForm);

        // Handle the file upload, if necessary
        $fileReceived = false;
        $postValues = $this->_request->getPost();

        if (isset($_POST['submit'])) {
            if ($uploadForm->isValid($postValues) && $fileReceived = $uploadForm->selectFile->receive()) {
                // Get information about the plugin, and then create a new instance of the plugin.
                $filePath = $uploadForm->selectFile->getTransferAdapter()->getFileName('selectFile');
                $pluginTable = new Plugin();
                $pluginInfo = $plugin->find($postValues['plugin'])->getRow(0);
                $pluginClass = $pluginInfo->class;
                $pluginName = $pluginInfo->name;
                $plugin = new $pluginClass($filePath,
                                           $postValues['network'],
                                           $postValues['system'],
                                           $postValues['findingSource']);

                // Execute the plugin with the received file
                try {
                    $findingsCreated = $plugin->parse();
                    $this->message("Your scan report was successfully uploaded."
                                   . " $findingsCreated findings were created.",
                                   self::M_NOTICE);
                } catch (Exception_InvalidFileFormat $e) {
                    $this->message("The uploaded file is not a valid format for {$pluginName}: {$e->getMessage()}",
                                   self::M_WARNING);
                }
            } else {
                /**
                 * @todo this error display code needs to go into the decorator,
                 * but before that can be done, the function it calls needs to be
                 * put in a more convenient place
                 */
                $errorString = '';
                foreach ($uploadForm->getMessages() as $field => $fieldErrors) {
                    if (count($fieldErrors>0)) {
                        foreach ($fieldErrors as $error) {
                            $label = $uploadForm->getElement($field)->getLabel();
                            $errorString .= "$label: $error<br>";
                        }
                    }
                }

                if (!$fileReceived) {
                    $errorString .= "File upload failed<br>";
                }

                // Error message
                $this->message("Scan upload failed:<br>$errorString", self::M_WARNING);
            }
        }

        $this->render();
    }
}
