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
 * Generate charts for the security control catalog
 * 
 * @author     Mark E. Haase
 * @copyright  (c) Endeavor Systems, Inc. 2009 {@link http://www.endeavorsystems.com}
 * @license    http://www.openfisma.org/content/license GPLv3
 * @package    Controllers
 * @version    $Id$
 */
class SecurityControlChartController extends Fisma_Zend_Controller_Action_Security
{
    /**
     * Set contexts for this controller's actions
     */
    public function init()
    {
        parent::init();
        
        $this->_helper->fismaContextSwitch()
                      ->setActionContext('control-deficiencies', 'xml')
                      ->setActionContext('control-deficiencies', 'json')
                      ->initContext();
    }

    /**
     * Renders a bar chart that shows the number of open findings against each security control code.
     */
    public function controlDeficienciesAction()
    {
        $rtnChart = new Fisma_Chart();
        $rtnChart
            ->setColors(array('#3366FF'))
            ->setChartType('bar')
            ->setConcatXLabel(false);
    
        $userOrganizations = $this->_me->getOrganizationsByPrivilege('organization', 'read')
            ->toKeyValueArray('id', 'id');
        
        $deficienciesQuery = Doctrine_Query::create()
            ->select('COUNT(*) AS count, sc.code')
            ->from('SecurityControl sc')
            ->innerJoin('sc.Findings f')
            ->innerJoin('f.ResponsibleOrganization o')
            ->andWhere('f.status <> ?', 'CLOSED')
            ->whereIn('o.id', $userOrganizations)
            ->groupBy('sc.code')
            ->orderBy('sc.code')
            ->setHydrationMode(Doctrine::HYDRATE_SCALAR);
        
        $defQueryRslt = $deficienciesQuery->execute();
        
        foreach ($defQueryRslt as $thisElement) {
            $rtnChart->addColumn($thisElement['sc_code'], $thisElement['sc_count']);
        }
        
        // pass a string instead of an array to Fisma_Chart to set all columns to link with this URL-rule
        $rtnChart->setLinks(
            '/finding/remediation/list/queryType/advanced/denormalizedStatus/textDoesNotContain/CLOSED' .
            '/securityControl/textExactMatch/#ColumnLabel#'
        );
            
        // the context switch will convert this array to a JSON resonce
        $this->view->chart = $rtnChart->export('array');
        
    }
}
