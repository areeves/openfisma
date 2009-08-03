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
 * @author    Nathan Harris <nathan.harris@endeavorsystems.com>
 * @copyright (c) Endeavor Systems, Inc. 2008 (http://www.endeavorsystems.com)
 * @license   http://www.openfisma.org/mw/index.php?title=License
 * @version   $Id: FindingController.php 2082 2009-07-29 21:27:37Z nathanrharris $
 * @package   Controller
 */

/**
 * The incident controller is used for searching, displaying, and updating
 * incidents.
 *
 * @package   Controller
 * @copyright (c) Endeavor Systems, Inc. 2008 (http://www.endeavorsystems.com)
 * @license   http://www.openfisma.org/mw/index.php?title=License
 */
class IncidentController extends BaseController
{
    
    /**
     * The main name of the model.
     * 
     * This model is the main subject which the controller operates on.
     */
    protected $_modelName = 'Incident';

    /**
     * my OrgSystems
     *
     * @var array
     */
    private $_myOrgSystems = null;
    
    /**
     * my OrgSystem ids
     *
     * @var array
     */
    private $_myOrgSystemIds = null;
    
    /**
     * initialize the basic information, my orgSystems
     *
     */
    public function init()
    {
        parent::init();
        $orgSystems = $this->_me->getOrganizations()->toArray();
        $this->_myOrgSystems = $orgSystems;
        
        $orgSystemIds = array(0);
        foreach ($orgSystems as $orgSystem) {
            $orgSystemIds[] = $orgSystem['id'];
        }
        $this->_myOrgSystemIds = $orgSystemIds;
    }
    
    
    /**
     * Returns the standard form for creating finding
     *
     * @return Zend_Form
     */
    public function getForm()
    {
        $form = Fisma_Form_Manager::loadForm('incident');

        $form->setElementDecorators(array(new Fisma_Form_CreateFindingDecorator()));

        return $form;
    }
}
