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
 * Create XML files for flash charts
 * 
 * @author     Mark E. Haase
 * @copyright  (c) Endeavor Systems, Inc. 2010 {@link http://www.endeavorsystems.com}
 * @license    http://www.openfisma.org/content/license GPLv3
 * @package    Controller
 * @version    $Id$
 */
class IncidentChartController extends Fisma_Zend_Controller_Action_Security
{
    /**
     * Set contexts for this controller's actions
     */
    public function init()
    {
        parent::init();
        
        $this->_helper->fismaContextSwitch()
                      ->setActionContext('history', 'xml')
                      ->setActionContext('history', 'json')
                      ->setActionContext('category', 'xml')
                      ->setActionContext('category', 'json')
                      ->setActionContext('bureau', 'xml')
                      ->setActionContext('bureau', 'json')
                      
                      ->initContext();
    }
    
    /**
     * Verify that this module is enabled
     */
    public function preDispatch()
    {
        parent::preDispatch();
        
        $module = Doctrine::getTable('Module')->findOneByName('Incident Reporting');
        
        if (!$module->enabled) {
            throw new Fisma_Zend_Exception('This module is not enabled.');
        }

        $this->_acl->requireArea('incident');
    }
    
    /**
     * A bar chart which shows how many incidents were reported/resolved/rejected on a month-by-month basis 
     * in recent history
     */
    public function historyAction()
    {
        /**
         * $period is the number of months of history to limit the results to. It's limited to 12 due to the way
         * the query is structured (indexed by month number, which would wrap around with a 12+ month period)
         */
        $period = $this->getRequest()->getParam('period');
        
        if (!is_int((int)$period) || $period > 12) {
            $message = "Incident status chart period parameter must be an integer less than or equal to 12.";
            throw new Fisma_Zend_Exception($message);
        }
        
        // Calculate the cutoff date based on the period        
        $cutoffDate = Zend_Date::now()->sub($period, Zend_Date::MONTH)->get(Fisma_Date::FORMAT_DATE);

        // Get chart data. This is done in two queries because one groups by reportTs and the other groups by closedTs
        $reportedIncidentsQuery = Doctrine_Query::create()
                                  ->addSelect('COUNT(i.id) AS reported')
                                  ->addSelect('MONTH(i.reportTs) AS monthNumber')
                                  ->from('Incident i INDEXBY monthNumber')
                                  ->where("i.reportTs > '$cutoffDate'")
                                  ->groupBy('monthNumber')
                                  ->setHydrationMode(Doctrine::HYDRATE_ARRAY);

        $reportedIncidents = $reportedIncidentsQuery->execute();

        $closedIncidentsQuery = Doctrine_Query::create()
                                ->addSelect("SUM(IF(i.resolution = 'resolved', 1, 0)) AS resolved")
                                ->addSelect("SUM(IF(i.resolution = 'rejected', 1, 0)) AS rejected")
                                ->addSelect('MONTH(i.closedTs) AS monthNumber')
                                ->from('Incident i INDEXBY monthNumber')
                                ->where("i.closedTs > '$cutoffDate'")
                                ->groupBy('monthNumber')
                                ->setHydrationMode(Doctrine::HYDRATE_ARRAY);

        $closedIncidents = $closedIncidentsQuery->execute();

        // Merge results and fill in placeholders for months that have no data
        $mergedData = array();
        $firstMonth = Zend_Date::now()->sub($period, Zend_Date::MONTH);

        $chartData = array('reported' => array(), 'resolved' => array(), 'rejected' => array());

        for ($monthOffset = 1; $monthOffset <= $period; $monthOffset++) {
            $currentMonth = clone $firstMonth;
            $currentMonth->add($monthOffset, Zend_Date::MONTH);
            
            // Fill in default values in case one or both queries had no matching records for this month
            $thisReported = 0;
            $thisResolved = 0;
            $thisRejected = 0;
            $thisMonthName = $currentMonth->get(Zend_Date::MONTH_NAME_SHORT); // short name for month
            $thisYear = $currentMonth->get(Zend_Date::YEAR);
            
            // Merge reported counts with rejected/resolved counts for each month
            
            // Current month as number with no leading zero
            $currentMonthNumber = $currentMonth->get(Zend_Date::MONTH_SHORT);
            
            if (isset($reportedIncidents[$currentMonthNumber])) {
                $thisReported = $reportedIncidents[$currentMonthNumber]['reported'];
            }

            if (isset($closedIncidents[$currentMonthNumber])) {
                $thisResolved = $closedIncidents[$currentMonthNumber]['resolved'];
                $thisRejected = $closedIncidents[$currentMonthNumber]['rejected'];
            }

            $chartData['reported'][] = $thisReported;
            $chartData['resolved'][] = $thisResolved;
            $chartData['rejected'][] = $thisRejected;
            
            $chartDataText[] = $thisMonthName;
        }
        
        $chartData = array_values($chartData);  // whipe out keys
        
        $this->view->chart = array(
            'chartData'         => $chartData,
            'chartDataText'     => $chartDataText,
            'chartLayerText'    => array(
                'Reported Incidents',
                'Resolved Incidents',
                'Rejected Incidents'
            )
        );
    }
    
    /**
     * A pie chart which shows how many incidents of each category are open
     */
    public function categoryAction()
    {
        $categoryQuery = Doctrine_Query::create()
                         ->select('category.name, category.category, COUNT(category.id) AS count')
                         ->from('IrCategory category INDEXBY category')
                         ->innerJoin('category.SubCategories subcategory')
                         ->innerJoin('subcategory.Incident i')
                         ->where('i.status = \'open\'')
                         ->groupBy('category.id')
                         ->setHydrationMode(Doctrine::HYDRATE_ARRAY);
        
        $catQueryRslt = $categoryQuery->execute();
        
        $chartData = array();
        $chartDataText = array();
        
        reset($catQueryRslt);
        while ($thisElement = current($catQueryRslt)) {
            
            $chartData[] = $thisElement['count'];
            $chartDataText[] = key($catQueryRslt) . ' - ' . $thisElement['name'];
            
            next($catQueryRslt);
        }

        
        $this->view->chart = array('chartData' => $chartData, 'chartDataText' => $chartDataText);
    }
    
    /**
     * A bar chart which shows the number of incidents per bureau in the last 90 days
     */
    public function bureauAction()
    {
        $cutoffDate = Zend_Date::now()->subDay(90)->toString(Fisma_Date::FORMAT_DATETIME);

        $bureauQuery = Doctrine_Query::create()
                       ->from('Incident i')
                       ->select('i.id, COUNT(*) AS count, bureau.nickname')
                       ->leftJoin('i.Organization o')
                       ->leftJoin('Organization bureau')
                       ->where('i.reportTs > ?', $cutoffDate)
                       ->andWhere('bureau.orgType = ?', array('bureau'))
                       ->andWhere('o.lft BETWEEN bureau.lft and bureau.rgt')
                       ->orderBy('bureau.nickname')
                       ->groupBy('bureau.id')
                       ->setHydrationMode(Doctrine::HYDRATE_SCALAR);
        
        $this->view->bureaus = $bureauQuery->execute();
    }
}
