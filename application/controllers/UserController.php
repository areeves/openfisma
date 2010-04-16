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
 * CRUD for Account manipulation
 *
 * @author     Ryan Yang <ryan@users.sourceforge.net>
 * @copyright  (c) Endeavor Systems, Inc. 2009 {@link http://www.endeavorsystems.com}
 * @license    http://www.openfisma.org/content/license GPLv3
 * @package    Controller
 * @version    $Id$
 */
class UserController extends BaseController
{
    /**
     * The main name of the model.
     * 
     * This model is the main subject which the controller operates on.
     * 
     * @var string
     */
    protected $_modelName = 'User';

    /**
     * Initialize internal members.
     * 
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->_user = new User();
        $this->_helper->contextSwitch()
                      ->setAutoJsonSerialization(false)
                      ->addActionContext('check-account', 'json')
                      ->initContext();
    }
    
    /**
     * Get the specified form of the subject model
     * 
     * @param string $formName The specified form name to fill
     * @return Zend_Form The assembled form
     */
    public function getForm($formName = null) 
    {
        $form = Fisma_Form_Manager::loadForm('account');
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

        if ('database' == Fisma::configuration()->getConfig('auth_type')) {
            $form->removeElement('checkAccount');
            $this->view->requirements =  $this->_getPasswordRequirements();
        } else {
            $form->removeElement('password');
            $form->removeElement('confirmPassword');
            $form->removeElement('generate_password');
        }
        
        // Show lock explanation if account is locked. Hide explanation otherwise.
        $userId = $this->getRequest()->getParam('id');
        $user = Doctrine::getTable('User')->find($userId);

        if ($user && $user->locked) {
            $reason = $user->getLockReason();
            $form->getElement('lockReason')->setValue($reason);

            $lockTs = new Zend_Date($user->lockTs, Zend_Date::ISO_8601);
            $form->getElement('lockTs')->setValue($lockTs->get('YYYY-MM-DD HH:mm:ss '));
        } else {
            $form->removeElement('lockReason');
            $form->removeElement('lockTs');
        }
        
        $form = Fisma_Form_Manager::prepareForm($form);
        return $form;
    }

    /**
     * Returns the standard form for reading, and updating
     * the current user's profile.
     *
     * @return Zend_Form The loaded user profile from
     * @todo This function is not named correctly
     */
    private function _getProfileForm()
    {
        $form = Fisma_Form_Manager::loadForm('account');
        $form->removeElement('username');
        $form->removeElement('password');
        $form->removeElement('confirmPassword');
        $form->removeElement('checkAccount');
        $form->removeElement('generate_password');
        $form->removeElement('role');
        $form->removeElement('locked');
        return $form;
    }

    /** 
     * Set the Roles, organization relation before save the model
     *
     * @param Zend_Form $form The specified form to save
     * @param Doctrine_Record|null $subject The specified subject related to the form
     * @return void
     * @throws Fisma_Exception if the related subject is not instance of Doctrine_Record
     */
    protected function saveValue($form, $subject=null)
    {
        $conn = Doctrine_Manager::connection();

        try {
            $conn->beginTransaction();

            $rolesOrganizations = $this->_request->getPost('organizations', array());

            if (is_null($subject)) {
                $subject = new $this->_modelName();
            } elseif (!($subject instanceof Doctrine_Record)) {
                throw new Fisma_Exception('Invalid parameter, expected a Doctrine_Model');
            }
            $values = $form->getValues();

            // If any roles were added to the user without any organizations, make sure they're added
            // to the appropriate array for saving the User Roles.
            foreach (array_diff_key(array_flip($values['role']), $rolesOrganizations) as $k => $v) {
                $rolesOrganizations[$k] = array();
            }

            unset($values['role']);

            if (empty($values['password'])) {
                unset($values['password']);
            }
            
            if ($values['locked'] && !$subject->locked) {
                $subject->lockAccount(User::LOCK_TYPE_MANUAL);
                unset($values['locked']);
                unset($values['lockTs']);
            } elseif (!$values['locked'] && $subject->locked) {
                $subject->unlockAccount();
                unset($values['locked']);
                unset($values['lockTs']);
            }
            
            $subject->merge($values);

            /*
             * We need to save the model once before linking related records, because Doctrine has a weird behavior 
             * where an invalid record will result in failed foreign key constraints. If this record is invalid, 
             * saving it here will avoid those errors.
             */
            $subject->save();

            foreach ($subject->UserRole as $role) {
                $role->unlink('Organizations');
            }
            $subject->save();
            $subject->unlink('Roles');
            $subject->save();

            foreach ($rolesOrganizations as $role => $organizations) {
                $userRole = new UserRole();
                $userRole->userId = (int) $subject->id;
                $userRole->roleId = (int) $role;
                $userRole->save();

                foreach ($organizations as $organization) {
                    $userRoleOrganization = new UserRoleOrganization();
                    $userRoleOrganization->organizationId = (int) $organization;
                    $userRoleOrganization->userRoleId = (int) $userRole->userRoleId;
                    $userRoleOrganization->save();
                    $userRoleOrganization->free();
                    unset($userRoleOrganization);
                }

                $userRole->free();
                unset($userRole);
            }
            $conn->commit();
            $this->view->id = $subject->id;
        } catch (Doctrine_Exception $e) {
            $conn->rollback();
            throw new Fisma_Exception('Unable to save user.');
        }
    }

    /**
     * Get the Roles and the organization from the model and assign them to the form
     *
     * @param Doctrine_Record|null $subject The specified subject
     * @param Zend_Form $form The specified form to set
     * @return Zend_Form The processed form
     */
    protected function setForm($subject, $form)
    {
        $roles = array();
        $assignedRoles = $subject->Roles->toArray();
        array_walk($assignedRoles, create_function('$v, $k, &$roles', '$roles[] = $v[\'id\'];'), &$roles);
        $form->setDefaults($subject->toArray());
        $form->getElement('role')->setValue($roles);

        return $form;
    }
    
    /**
     * Show audit logs for a given user
     */
    public function logAction()
    {
        $id = $this->getRequest()->getParam('id');
        
        $user = Doctrine::getTable('User')->find($id);

        $this->view->createUserPrivilege = Fisma_Acl::hasPrivilegeForClass('create', 'User');
        $this->view->username = $user->username;
        $this->view->columns = array('Timestamp', 'User', 'Message');
        $this->view->rows = $user->getAuditLog()->fetch(Doctrine::HYDRATE_SCALAR);
        $this->view->viewLink = "/panel/user/sub/view/id/$id";
    }

    /**
     * Display the user's "Edit Profile" page and handle its updating
     * 
     * @return void
     */
    public function profileAction()
    {
        $form = $this->_getProfileForm();
        $user = Doctrine::getTable('User')->find($this->_me->id);
        if ($this->_request->isPost()) {
            $post = $this->_request->getPost();
            if ($form->isValid($post)) {
                $user->merge($form->getValues());
                try {
                    Doctrine_Manager::connection()->beginTransaction();
                    $modified = $user->getModified();
                    $user->save();
                    Doctrine_Manager::connection()->commit();
                    $message = "Profile updated successfully"; 
                    if (isset($modified['email'])) {
                        $mail = new Fisma_Mail();
                        if ($mail->validateEmail($user, $modified['email'])) {
                            $message .= ", and a validation email has been sent to your new e-mail address.";
                        } 
                    }
                    $model   = 'notice';
                } catch (Doctrine_Exception $e) {
                    Doctrine_Manager::connection()->rollback();
                    $message = $e->getMessage();
                    $model   = 'warning';
                }
            } else {
                $errorString = Fisma_Form_Manager::getErrors($form);
                $message     = "Unable to update profile:<br>" . $errorString;
                $model       = 'warning';
            }
            $this->view->priorityMessenger($message, $model);
        } else {
            $form->setDefaults($user->toArray());
        }
        $this->view->form    = Fisma_Form_Manager::prepareForm($form);
    }

    /**
     * List user accounts
     *
     * @return void
     */
    public function listAction()
    {
        $this->view->readUserPrivilege = Fisma_Acl::hasPrivilegeForClass('read', 'User');
        $this->view->createUserPrivilege = Fisma_Acl::hasPrivilegeForClass('create', 'User');
        parent::listAction();
    }

    /**
     * Change user's password
     * 
     * @return void
     */
    public function passwordAction()
    {
        // This action isn't allowed unless the system's authorization is based on the database
        if ('database' != Fisma::configuration()->getConfig('auth_type') && 'root' != User::currentUser()->username) {
            throw new Fisma_Exception('Password change is not allowed when the authentication type is not "database"');
        }
        
        // Load the change password file
        $form = Fisma_Form_Manager::loadForm('change_password');
        $form = Fisma_Form_Manager::prepareForm($form);

        $this->view->requirements =  $this->_getPasswordRequirements();
        $post   = $this->_request->getPost();

        if (isset($post['oldPassword'])) {

            if ($form->isValid($post)) {
                $user = User::currentUser();
                try {
                    $user->merge($post);
                    $user->save();
                    $message = "Password updated successfully."; 
                    $model   = 'notice';
                } catch (Doctrine_Exception $e) {
                    $message = $e->getMessage();
                    $model   = 'warning';
                }
            } else {
                $errorString = Fisma_Form_Manager::getErrors($form);
                $message     = "Unable to change password:<br>" . $errorString;
                $model       = 'warning';
            }
            $this->view->priorityMessenger($message, $model);
        }
        $this->view->form    =  $form;
    }

    /**
     * Set user's notification policy
     * 
     * @return void
     */
    public function notificationAction()
    {
        $user = Doctrine::getTable('User')->find($this->_me->id);

        if ($this->_request->isPost()) {
            //@todo check injection
            $user->notifyFrequency = $this->_request->getParam('notify_frequency');
            $user->notifyEmail     = $this->_request->getParam('notify_email');

            $postEvents = $this->_request->getPost('existEvents');
            try {
                Doctrine_Manager::connection()->beginTransaction();
                $modified = $user->getModified();

                $user->unlink('Events');
                $user->link('Events', $postEvents);
                $user->save();
                Doctrine_Manager::connection()->commit();

                $message = "Notification events modified successfully";
                $model   = 'notice';
            } catch (Doctrine_Exception $e) {
                Doctrine_Manager::connection()->rollback();
                $message = $e->getMessage();
                $model   = 'warning';
            }
            $this->view->priorityMessenger($message, $model);
        }

        $this->view->notifyFrequency = $user->notifyFrequency;
        $this->view->notifyEmail     = $user->notifyEmail;
        $this->view->availableEvents = $user->getAvailableEvents();
        $this->view->existEvents     = $user->getExistEvents();
    }

    /**
     * Get the password complex requirements
     *
     * @return array The password requirement messages in array
     */
    private function _getPasswordRequirements()
    {
        $requirements[] = "Length must be between "
        . Fisma::configuration()->getConfig('pass_min_length')
        . " and "
        . Fisma::configuration()->getConfig('pass_max_length')
        . " characters long.";
        if (Fisma::configuration()->getConfig('pass_uppercase') == 1) {
            $requirements[] = "Must contain at least 1 upper case character (A-Z)";
        }
        if (Fisma::configuration()->getConfig('pass_lowercase') == 1) {
            $requirements[] = "Must contain at least 1 lower case character (a-z)";
        }
        if (Fisma::configuration()->getConfig('pass_numerical') == 1) {
            $requirements[] = "Must contain at least 1 numeric digit (0-9)";
        }
        if (Fisma::configuration()->getConfig('pass_special') == 1) {
            $requirements[] = htmlentities("Must contain at least 1 special character (!@#$%^&*-=+~`_)");
        }
        return $requirements;
    }

    /**
     * Set cloumn preference
     * 
     * @return void
     */
    public function setColumnPreferenceAction()
    {
        $me = Doctrine::getTable('User')->find($this->_me->id);
        $me->searchColumnsPref = Fisma_Cookie::get($_COOKIE, 'search_columns_pref');
        $me->getTable()->getRecordListener()->setOption('disabled', true);
        $me->save();
        $this->_helper->layout->setLayout('ajax');
        $this->_helper->viewRenderer->setNoRender();
    }
    
    /**
     * Store user last accept rob and create a audit event
     * 
     * @return void
     */
    public function acceptRobAction()
    {
        $user = User::currentUser();
        $user->lastRob = Fisma::now();
        $user->save();
        
        $this->_forward('index', 'Panel');
    }

    /**
     * Override parent to add a link for audit logs
     *
     * @return void
     */
    public function viewAction()
    {
        $id = $this->getRequest()->getParam('id');
        $this->view->auditLogLink = "/panel/user/sub/log/id/$id";

        $user = Doctrine::getTable('User')->find($id);
        $this->view->createUserPrivilege = Fisma_Acl::hasPrivilegeForClass('create', 'User');
        $this->view->createUserObjPrivilege = Fisma_Acl::hasPrivilegeForObject('create', $user);
        $this->view->updateUserObjPrivilege = Fisma_Acl::hasPrivilegeForObject('update', $user);
        $this->view->deleteUserObjPrivilege = Fisma_Acl::hasPrivilegeForObject('delete', $user);

        $tabView = new Fisma_Yui_TabView('UserView');

        $q = Doctrine_Query::create()
            ->from('User u')
            ->leftJoin('u.Roles r')
            ->where('u.id = ?', $id);

        $user = $q->fetchArray();

        foreach ($user[0]['Roles'] as $role) {
            $tabView->addTab(
                $role['nickname'], 
                "/User/get-organization-subform/user/{$id}/role/{$role['id']}/readOnly/1", 
                $role['id'],
                'false'
            );
        }

        $this->view->tabView = $tabView;

        parent::viewAction();
    }

    /**
     * Retrieve the organization subform 
     * 
     * @return void
     */
    public function getOrganizationSubformAction()
    {
        $this->_helper->layout()->setLayout('ajax');

        $userId = $this->getRequest()->getParam('user');
        $roleId = $this->getRequest()->getParam('role');
        $readOnly = $this->getRequest()->getParam('readOnly');

        $user = Doctrine::getTable('User')->find($userId);

        $assignedOrgs = array();

        if (!empty($user)) {
            $userOrgs = $user->getOrganizationsByRole($roleId);

            foreach ($userOrgs as $userOrg) {
                $assignedOrgs[] = $userOrg->id;
            }
        }

        $subForm = new Zend_Form_SubForm();
        $subForm->removeDecorator('DtDdWrapper');
        $subForm->removeDecorator('HtmlTag');

        $organizations = new Fisma_Form_Element_CheckboxTree("organizations");
        $organizations->clearDecorators();
        $organizations->setLabel('Organizations & Information Systems');

        $organizationTreeObject = Doctrine::getTable('Organization')->getTree();
        $q = Doctrine_Query::create()
                ->select('o.id, o.name, o.level')
                ->from('Organization o');
        $organizationTreeObject->setBaseQuery($q);
        $organizationTree = $organizationTreeObject->fetchTree();

        if (!empty($organizationTree)) {
            foreach ($organizationTree as $organization) {
                $organizations->addCheckbox(
                    $organization['id'], 
                    $organization['name'], 
                    $organization['level'], 
                    $roleId
                );
            }
        }

        $organizations->setValue($assignedOrgs);

        $organizations->readOnly = (boolean) $readOnly;

        $subForm->addElement($organizations);

        $this->view->subForm = $subForm;
    }
    
    /**
     * Override parent to add a link for audit logs
     *
     * @return void
     */
    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $tabView = new Fisma_Yui_TabView('UserView');

        $q = Doctrine_Query::create()
            ->from('User u')
            ->leftJoin('u.Roles r')
            ->where('u.id = ?', $id);

        $user = $q->fetchArray();

        if (isset($user[0]['Roles'])) {
            foreach ($user[0]['Roles'] as $role) {
                $tabView->addTab(
                    $role['nickname'], 
                    "/User/get-organization-subform/user/{$id}/role/{$role['id']}/readOnly/0", 
                    $role['id'],
                    'true' 
                );
            }
        }

        $roles = Doctrine_Query::create()
            ->select('r.id, r.nickname')
            ->from('Role r')
            ->setHydrationMode(Doctrine::HYDRATE_ARRAY)
            ->execute();

        $this->view->auditLogLink = "/panel/user/sub/log/id/$id";
        $this->view->tabView = $tabView;
        $this->view->roles = Zend_Json::encode($roles);

        $user = Doctrine::getTable('User')->find($id);
        $this->view->createUserPrivilege = Fisma_Acl::hasPrivilegeForClass('create', 'User');
        $this->view->readUserObjPrivilege = Fisma_Acl::hasPrivilegeForObject('read', $user);

        parent::editAction();
        $this->view->form->removeDecorator('Fisma_Form_FismaDecorator');
    }

    /**
     * Override parent method
     * 
     * @return void
     */
    public function createAction()
    {
        $this->view->createUserPrivilege = Fisma_Acl::hasPrivilegeForClass('create', 'User');

        $tabView = new Fisma_Yui_TabView('UserView');

        $roles = Doctrine_Query::create()
            ->select('r.id, r.nickname')
            ->from('Role r')
            ->setHydrationMode(Doctrine::HYDRATE_ARRAY)
            ->execute();

        $this->view->roles = Zend_Json::encode($roles);
        $this->view->tabView = $tabView;
        parent::createAction();
        $this->view->form->removeDecorator('Fisma_Form_FismaDecorator');

        if (!empty($this->view->id)) {
            $this->_request->setParam('id', $this->view->id);
            $this->_forward('view');
        }
    }

    /**
     * Generate a password that meet the application's password complexity requirements.
     * 
     * @return void
     */
    public function generatePasswordAction()
    {
        $passLengthMin = Fisma::configuration()->getConfig('pass_min_length');
        $passLengthMax = Fisma::configuration()->getConfig('pass_max_length');
        $passNum = Fisma::configuration()->getConfig('pass_numerical');
        $passUpper = Fisma::configuration()->getConfig('pass_uppercase');
        $passLower = Fisma::configuration()->getConfig('pass_lowercase');
        $passSpecial = Fisma::configuration()->getConfig('pass_special');
        
        $flag = 0;
        $password = "";
        $length = rand($passLengthMin ? $passLengthMin : 1, $passLengthMax);
        if (true == $passUpper) {
            $possibleCharactors[] = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
            $flag++;
        }
        if (true == $passLower) {
            $possibleCharactors[] = "abcdefghijklmnopqrstuvwxyz";
            $flag++;
        }
        if (true == $passNum) {
            $possibleCharactors[] = "0123456789";
            $flag++;
        }
        if (true == $passSpecial) {
            $possibleCharactors[] = "!@#$%^&*()_+=-`~\|':;?><,.[]{}/";
            $flag++;
        }

        while (strlen($password) < $length) {
            if (0 == $flag) {
                $password .= rand(0, 9);
            } else {
                foreach ($possibleCharactors as $row) {
                    if (strlen($password) < $length) {
                        $password .= substr($row, (rand()%(strlen($row))), 1);
                    }
                }
            }
        }
        echo $password;
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender();
    }
    
    /**
     * Check if the specified LDAP distinguished name (Account) exists in the system's specified LDAP directory.
     * 
     * @return void
     * @todo code finish this function later
     */
    public function checkAccountAction()
    {
        Fisma_Acl::requirePrivilegeForClass('read', 'User');
        
        $data = LdapConfig::getConfig();
        $account = $this->_request->getParam('account');
        $msg = '';
        if (count($data) == 0) {
            $type = 'warning';
            $msg .= "No LDAP providers defined";
        }

        foreach ($data as $opt) {
            $srv = new Zend_Ldap($opt);
            try {
                $type = 'message';
                $dn = $srv->getCanonicalAccountName($account, Zend_Ldap::ACCTNAME_FORM_DN); 
                $msg = "$account exists, the dn is: $dn";
            } catch (Zend_Ldap_Exception $e) {
                $type = 'warning';
                // The expected error is LDAP_NO_SUCH_OBJECT, meaning that the
                // DN does not exist.
                if ($e->getErrorCode() ==
                    Zend_Ldap_Exception::LDAP_NO_SUCH_OBJECT) {
                    $msg = "$account does NOT exist";
                } else {
                    $msg .= 'Unknown error while checking Account: '
                          . $e->getMessage();
                }
            }
        }

        echo Zend_Json::encode(array('msg' => $msg, 'type' => $type));
        $this->_helper->viewRenderer->setNoRender();
    }
}
