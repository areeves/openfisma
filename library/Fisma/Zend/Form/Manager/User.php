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
 * Fisma_Zend_Form_Manager_User 
 * 
 * @uses Fisma_Zend_Form_Manager_Abstract
 * @package Fisma_Zend_Form_Manager 
 * @copyright (c) Endeavor Systems, Inc. 2009 {@link http://www.endeavorsystems.com}
 * @author Josh Boyd <joshua.boyd@endeavorsystems.com> 
 * @license http://www.openfisma.org/content/license GPLv3
 */
class Fisma_Zend_Form_Manager_User extends Fisma_Zend_Form_Manager_Abstract
{
    /**
     * prepareForm 
     * 
     * @return void
     */
    public function prepareForm()
    {
        $passwordRequirements = new Fisma_Zend_Controller_Action_Helper_PasswordRequirements();
        $form = $this->getForm();

        if ('create' == $this->_request->getActionName()) {
            $form->getElement('password')->setRequired(true);
        }
        $roles  = Doctrine_Query::create()
                    ->select('*')
                    ->from('Role')
                    ->execute();
        foreach ($roles as $role) {
            $form->getElement('role')->addMultiOptions(array($role->id => $role->name));
        }

        $authType = Fisma::configuration()->getConfig('auth_type');
        if ($authType === 'database') {
            $form->removeElement('checkAccount');
            $this->_view->requirements =  $passwordRequirements->direct();
        } elseif ($authType === 'remote_user') {
            $form->removeElement('password');
            $form->removeElement('confirmPassword');
            $form->removeElement('generate_password');
            $form->removeElement('checkAccount');
            $form->removeElement('mustResetPassword');
        } else {
            $form->removeElement('password');
            $form->removeElement('confirmPassword');
            $form->removeElement('generate_password');
        }
        
        // Show lock explanation if account is locked. Hide explanation otherwise.
        $userId = $this->_request->getParam('id');
        $user = Doctrine::getTable('User')->find($userId);

        if ($user && $user->locked) {
            $reason = $user->getLockReason();
            $form->getElement('lockReason')->setValue($reason);

            $lockTs = new Zend_Date($user->lockTs, Zend_Date::ISO_8601);
            $form->getElement('lockTs')->setValue($lockTs->get(Fisma_Date::FORMAT_DATETIME));
        } else {
            $form->removeElement('lockReason');
            $form->removeElement('lockTs');
        }

        $this->setForm($form);
    }
}
