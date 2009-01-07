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
                'DRAFT',
                'MSA',
                'EN',
                'EP'
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
        $this->_acl->requirePrivilege('finding', 'read');
        $req = $this->getRequest();
        $id = $req->getParam('id', 0);
        assert($id);
        $this->view->assign('id', $id);
        $sys = new System();
        $poam = new Poam();
        $detail = $poam->find($id)->current();
        $this->view->finding = $poam->getDetail($id);
        $this->view->finding['system_name'] = 
                $this->_systemList[$this->view->finding['system_id']];
    }
    /**
     Edit finding infomation
     */
    public function editAction()
    {
        $this->_acl->requirePrivilege('finding', 'update');
        
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
     * Allow the user to upload an XML Excel spreadsheet file containing finding data for multiple findings
     *
     * @todo This is very long. This should be refactored into a separate class. Perhaps even an injection plugin?
     */
    public function injectionAction()
    {
        $this->_acl->requirePrivilege('finding', 'inject');

        // If the form isn't submitted, then there is no work to do
        if (!isset($_POST['submit'])) {
            return;
        }
        
        // If the form is submitted, then the file object should contain an array
        $file = $_FILES['excelFile'];
        if (!is_array($file)) {
            $this->message("The file upload failed.", self::M_WARNING);
            return;
        }

        // Parse the file using SimpleXML. The finding data is located on the first worksheet.
        $spreadsheet = simplexml_load_file($file['tmp_name']);
        if ($spreadsheet === false) {
            $this->message("The file is not a valid Excel spreadsheet. Make sure that the file is saved as XML.",
                           self::M_WARNING);
            return;
        }
        // Have to do some namespace manipulation to make the spreadsheet searchable by xpath.
        $namespaces = $spreadsheet->getNamespaces(true);
        $spreadsheet->registerXPathNamespace('s', $namespaces['']);
        $findingData = $spreadsheet->xpath('/s:Workbook/s:Worksheet[1]/s:Table/s:Row');
        if ($findingData === false) {
            $this->message("The file format is not recognized. Your version of Excel might be incompatible.",
                           self::M_WARNING);
            return;
        }
        // $findingData is an array of rows in the first worksheet. The first two rows on this worksheet contain
        // column headers, so skip them.
        array_shift($findingData);
        array_shift($findingData);
        
        // Now process each row
        $error = '';
        $rowNumber = 3;
        /**
         * @todo Perform these commits in a single transaction.
         */
        foreach ($findingData as $row) {
            $rowArray = (array)$row;
            $rowData = $rowArray['Cell'];

            /** @todo Remove magic number 10, this is the number of columns in the template */
            // Verify that all columns are filled in
            if (count($row) != 10) {
                $error = "Row $rowNumber: Not all columns are filled in.";
                continue;
            }

            // Assign names to the row data
            $systemNickname  = $rowData[0]->Data;
            $dateDiscovered  = $rowData[1]->Data;
            $networkNickname = $rowData[2]->Data;
            $ipAddress       = $rowData[3]->Data;
            $ipPort          = $rowData[4]->Data;
            $findingSource   = $rowData[5]->Data;
            $description     = $rowData[6]->Data;
            $recommendation  = $rowData[7]->Data;
            $securityControl = $rowData[8]->Data;
            $risk            = $rowData[9]->Data;

            // Validate row data
            $systemTable = new System();
            /**
             * @todo Multiple SQL injection attacks
             */
            $system = $systemTable->fetchRow("nickname = '$systemNickname'");
            if (isset($system)) {
                $systemId = $system->id;
            } else {
                $error = "Row $rowNumber: Invalid System";
                continue;
            }
            
            $networkTable = new Network();
            $network = $networkTable->fetchRow("nickname = '$networkNickname'");
            if (isset($network)) {
                $networkId = $network->id;
            } else {
                $error = "Row $rowNumber: Invalid Network";
                continue;
            }

            if (empty($ipAddress)) {
                $error = "Row $rowNumber: Blank IP Address";
                continue;
            }

            if (empty($ipPort)) {
                $error = "Row $rowNumber: Blank IP Port";
                continue;
            }

            $sourceTable = new Source();
            $source = $sourceTable->fetchRow("nickname = '$findingSource'");
            if (isset($source)) {
                $sourceId = $source->id;
            } else {
                $error = "Row $rowNumber: Invalid Finding Source";
                continue;
            }

            if (empty($description)) {
                $error = "Row $rowNumber: Blank Finding Description";
                continue;
            }

            if (empty($recommendation)) {
                $error = "Row $rowNumber: Blank Finding Recommendation";
                continue;
            }
            
            $now = new Zend_Date();
            // Check to see if the asset exists
            $assetTable = new Asset();
            $asset = $assetTable->fetchRow("network_id = '$networkId' AND
                                            address_ip = '$ipAddress' AND
                                            address_port = '$ipPort'");
            if (!isset($asset)) {
                // The asset does not exist, so create it.
                $asset = array('name' => "$ipAddress:$ipPort",
                               'create_ts' => $now->toString('Y-m-d H:i:s'),
                               'source' => 'MANUAL',
                               'system_id' => $systemId,
                               'network_id' => $networkId,
                               'address_ip' => $ipAddress,
                               'address_port' => $ipPort);
                $assetId = $assetTable->insert($asset);
            } else {
                $assetId = $asset->id;
            }
            
            // Now insert the new finding
            $poamTable = new Poam();
            $finding = array('asset_id' => $assetId,
                             'source_id' => $sourceId,
                             'system_id' => $systemId,
                             'blscr_id' => $securityControl,
                             'create_ts' => $now->toString('Y-m-d H:i:s'),
                             'discover_ts' => $dateDiscovered,
                             'finding_data' => $description,
                             'action_suggested' => $recommendation);
            $poamTable->insert($finding);
            
            $rowNumber++;
        }

        if ($error != '') {
            $this->message("The findings could not be inserted because one or more rows had errors:<br>$error",
                           self::M_WARNING);
            $this->render();
            // If this were a real transaction, we would roll back right here.
        } else {
            // Otherwise, we'd commit right here.
            $rowsCommitted = $rowNumber - 3;
            $this->message("$rowsCommitted findings were created.", self::M_NOTICE);
            $this->render();
        }
    }
    
    /**
     *  Create a finding manually
     */
    public function createAction()
    {
        $this->_acl->requirePrivilege('finding', 'create');
        
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
                    throw new Exception_General(
                        "You must select a finding source"
                    );
                }
                // If the blscr_id is zero, that means the user didn't select
                // a security control, so set the control to null.
                if ($poam['blscr_id'] == '0') {
                    unset($poam['blscr_id']);
                }
                $poam['status'] = 'NEW';
                $discoverTs = new Zend_Date($poam['discover_ts'], 'Y-m-d');
                $poam['discover_ts'] = $discoverTs->toString("Y-m-d");
                $poam['create_ts'] = self::$now->toString("Y-m-d H:i:s");
                $poam['created_by'] = $this->_me->id;
                $poamId = $this->_poam->insert($poam);

                $message = "Finding created successfully";
                $model = self::M_NOTICE;
            }
            catch(Zend_Exception $e) {
                if ($e instanceof Exception_General) {
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
        $this->_acl->requirePrivilege('finding', 'delete');
        
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
     * Downloading a excel file which is used as a template 
     * for uploading findings.
     * systems, networks and sources are extracted from the
     * database dynamically.
     */
    public function templateAction()
    {
        $this->_acl->requirePrivilege('finding', 'inject');
        
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
                throw new Exception_General(
                    "The spreadsheet template can not be " .
                    "prepared because there are no systems defined.");
            }
            $src = new Network();
            $this->view->networks = $src->getList('nickname');
            if (count($this->view->networks) == 0) {
                 throw new Exception_General("The spreadsheet template can not be
                     prepared because there are no networks defined.");
            }
            $src = new Source();
            $this->view->sources = $src->getList('nickname');
            if (count($this->view->networks) == 0) {
                 throw new Exception_General("The spreadsheet template can
                     not be prepared because there are no finding sources
                     defined.");
            }
            $blscr = new Blscr();
            $blscrs = array_keys($blscr->getList('class'));
            $this->view->blscrs = $blscrs;
            if (count($this->view->blscrs) == 0) {
                 throw new Exception_General("The spreadsheet template can not be prepared because there are no security
                                              controls defined.");
            }
            $this->view->risk = array('HIGH', 'MODERATE', 'LOW');
            //var_dump($this->view->blscrs);die;

            // Context switch is called only after the above code executes 
            // successfully. Otherwise if there is an error,
            // the error handler will be confused by context switch and will 
            // look for error.xls.tpl instead of error.tpl
            $contextSwitch->initContext('xls');
        } catch(Exception_General $fe) {
            Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
            $this->message($fe->getMessage(), self::M_WARNING);
            $this->_forward('injection', 'Finding');
        }
    }

    /** 
     * pluginAction() - Import scan results via a plug-in
     */
    public function pluginAction()
    {       
        $this->_acl->requirePrivilege('finding', 'inject');

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
        $uploadForm->selectFile->setDestination(APPLICATION_ROOT . '/data/uploads/scanreports');

        // Setup the view
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
                    $plugin->parse();
                    $this->message("Your scan report was successfully uploaded.<br>"
                                   . "{$plugin->created} findings were created.<br>"
                                   . "{$plugin->reviewed} findings need review.<br>"
                                   . "{$plugin->deleted} findings were suppressed.",
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
                    if (count($fieldErrors)>0) {
                        foreach ($fieldErrors as $error) {
                            $label = $uploadForm->getElement($field)->getLabel();
                            $errorString .= "$label: $error<br>";
                        }
                    }
                }

                if (!$fileReceived) {
                    $errorString .= "File not received<br>";
                }

                // Error message
                $this->message("Scan upload failed:<br>$errorString", self::M_WARNING);
            }
            $this->render(); // Not sure why this view doesn't auto-render?? It doesn't render when the POST is set.
        }
    }

    /** 
     * approveAction() - Allows a user to approve or delete pending findings
     *
     * @todo Use Zend_Pager
     */
    public function approveAction() {
        $this->_acl->requirePrivilege('finding', 'approve');
        
        $db = Zend_Registry::get('db');
        $findings = $db->fetchAll("SELECT new_poam.id,
                                          new_poam.finding_data,
                                          new_poam.duplicate_poam_id,
                                          new_poam_system.nickname,
                                          old_poam.status old_status,
                                          old_poam.type old_type,
                                          old_poam_system.nickname old_nickname
                                     FROM poams new_poam
                               INNER JOIN systems new_poam_system ON new_poam.system_id = new_poam_system.id
                                LEFT JOIN poams old_poam ON new_poam.duplicate_poam_id = old_poam.id
                                LEFT JOIN systems old_poam_system ON old_poam.system_id = old_poam_system.id
                                    WHERE new_poam.status = 'PEND'
                                 ORDER BY new_poam.system_id,
                                          new_poam.id");
        
        $this->view->assign('findings', $findings);
    }
    
    /**
     * processApprovalAction() - Process the form submitted from the approveAction()
     *
     * @todo Add audit logging
     */
    public function processApprovalAction() {
        $this->_acl->requirePrivilege('finding', 'approve');
        
        $db = Zend_Registry::get('db');
        $post = $this->getRequest()->getPost();
        if (isset($post['findings'])) {
            $inString = mysql_real_escape_string(implode(',', $post['findings']));
            if (isset($_POST['approve_selected'])) {
                $poam = new Poam();
                $now = new Zend_Db_Expr('now()');
                $poam->update(array('status' => 'NEW', 'create_ts' => $now), "id IN ($inString)");
            } elseif (isset($_POST['delete_selected'])) {
                $poam = new Poam();
                $poam->delete("id IN ($inString)");
            }
        }
        $this->_forward('approve', 'Finding');
    }
}
