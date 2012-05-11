<?php
/**
 * Copyright (c) 2009 Endeavor Systems, Inc.
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
 * The asset controller deals with creating, updating, and managing assets on the system.
 *
 * @author     Jim Chen <xhorse@users.sourceforge.net>
 * @copyright  (c) Endeavor Systems, Inc. 2009 {@link http://www.endeavorsystems.com}
 * @license    http://www.openfisma.org/content/license GPLv3
 * @package    Controller
 *
 * @see        Zend_View_Helper_Abstract
 */
class AssetController extends Fisma_Zend_Controller_Action_Object
{
    /**
     * The main name of the model.
     *
     * This model is the main subject which the controller operates on.
     *
     * @var string
     */
    protected $_modelName = 'Asset';

    /**
     * Invokes a contract with Fisma_Zend_Controller_Action_Object regarding privileges.
     *
     * @var string
     * @link http://jira.openfisma.org/browse/OFJ-24
     */
    protected $_organizations = '*';

    /**
     * Hooks for manipulating the values before setting to a form
     *
     * @param Doctrine_Record $subject The specified subject model
     * @param Zend_Form $form The specified form
     * @return Zend_Form The manipulated form
     */
    protected function setForm($subject, $form)
    {
        $product = $subject->Product;

        if ($this->getRequest()->getParam('sub') != 'edit')
            $form->getElement('product')->setAttrib('readonly', true);

        $form->getElement('productId')->setValue($subject->productId);
        $form->getElement('product')->setValue($subject->Product->name);

        return parent::setForm($subject, $form);
    }

    /**
     * Hooks for manipulating and saving the values retrieved by Forms
     *
     * @param Zend_Form $form The specified form
     * @param Doctrine_Record|null $subject The specified subject model
     * @return Fisma_Doctrine_Record The saved record
     * @throws Fisma_Zend_Exception if the subject is not instance of Doctrine_Record
     */
    protected function saveValue($form, $subject=null)
    {
        if (is_null($subject)) {
            $subject = new $this->_modelName();
        } elseif (!$subject instanceof Doctrine_Record) {
            throw new Fisma_Zend_Exception('Invalid parameter: Expected a Doctrine_Record');
        }

        $values = $form->getValues();

        $subject->merge($values);
        $subject->save();

        return $subject;
    }

    /**
     * Customize the toolbar buttons
     *
     * @param Fisma_Doctrine_Record $record The object for which this toolbar applies, or null if not applicable
     * @return array Array of Fisma_Yui_Form_Button
     */
    public function getToolbarButtons(Fisma_Doctrine_Record $record = null)
    {
        $buttons = parent::getToolbarButtons($record);

        if ($this->_acl->hasPrivilegeForClass('create', 'Asset')) {
            $buttons[] = new Fisma_Yui_Form_Button_Link(
                'importAssetsButton',
                array(
                    'value' => 'Import Assets',
                    'href' => $this->getBaseUrl() . '/import'
                )
            );
        }

        return $buttons;
    }

    /**
     * Import assets from an uploaded XML file using an import plugin
     *
     * @GETAllowed
     */
    public function importAction()
    {
        $this->_acl->requirePrivilegeForClass('create', 'Asset');

        $uploadForm = $this->getForm('asset_upload');

        // Configure the file select
        $uploadForm->setAttrib('enctype', 'multipart/form-data');

        $this->view->assign('uploadForm', $uploadForm);

        // Handle the file upload
        if ($postValues = $this->_request->getPost()) {
            $msgs = array();
            $err = FALSE;
            $filesReceived = ($uploadForm->selectFile->receive()) ? TRUE : FALSE;

            if (!$uploadForm->isValid($postValues)) {
                $errorString = Fisma_Zend_Form_Manager::getErrors($uploadForm);

                // Customize error message, see the attachments on OFJ-1693
                if ($errorString && stristr($errorString, 'selectFile') && stristr($errorString, 'few')) {
                    $msgs[] = array('warning' => 'No file selected. Please select at least one file to upload.');
                } else {
                    $msgs[] = array('warning' => $errorString);
                }
                $err = TRUE;
            } elseif (!$filesReceived) {
                $msgs[] = array('warning' => "File not received.");
                $err = TRUE;
            } else {
                $values = $uploadForm->getValues();
                $filePath = $uploadForm->selectFile->getTransferAdapter()->getFileName('selectFile');

                // get original file name
                $originalName = pathinfo(basename($filePath), PATHINFO_FILENAME);
                $values['filepath'] = $filePath;

                $upload = new Upload();

                $import = Fisma_Inject_Factory::create('Asset', $values);
                $import->parse(null);

                $msgs[] = $import->getMessages();

                // Add the file to storage
                $upload->instantiate(array(
                    'tmp_name' => $filePath,
                    'name' => $originalName,
                    'type' => $uploadForm->selectFile->getMimeType()
                ));

                // Need to save again after instantiate.
                $upload->save();
            }

            if ($err) {
                if (!empty($upload)) {
                    unlink($filePath);
                    $upload->delete();
                }

                if (!$msgs) {
                    $msgs[] = array('notice' => 'An unrecoverable error has occured.');
                }
            }

            $this->view->priorityMessenger($msgs);
        }
    }

    protected function _isDeletable()
    {
        return false;
    }

    /**
     * viewAction
     *
     * @return void
     *
     * @GETAllowed
     */
    public function viewAction()
    {
        $this->_viewObject();
        $asset = $this->view->asset = $this->view->subject;
        $this->view->buttons = $this->getToolbarButtons($asset);
    }
}
