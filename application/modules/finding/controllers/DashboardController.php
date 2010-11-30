<?php
/**
 * Copyright (c) 2010 Endeavor Systems, Inc.
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
 * Dashboard for findings
 * 
 * @author     Mark E. Haase
 * @copyright  (c) Endeavor Systems, Inc. 2009 {@link http://www.endeavorsystems.com}
 * @license    http://www.openfisma.org/content/license GPLv3
 * @package    Controllers
 * @version    $Id$
 */
class Finding_DashboardController extends Fisma_Zend_Controller_Action_Security
{
    /**
     * Set up headers/footers
     */
    public function preDispatch()
    {
        parent::preDispatch();

        $this->_acl->requireArea('finding');
    }

    public function indexAction()
    {
        $this->view->controlDeficienciesChart = new Fisma_ChartJQP(
                            array(
                                'width'         => 800,
                                'height'        => 300,
                                'chartTitle'    => 'Current Security Control Deficiencies',
                                'chartType'     => 'bar',
                                'align'         => 'center',
                                'externalSource'=> '/security-control-chart/control-deficiencies/format/json'
                            )
        );
    }
}
