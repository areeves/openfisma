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
        
        $this->_helper->fismaContextSwitch()
                      ->addActionContext('chartoverdue', 'json')
                      ->addActionContext('chartfindingstatus', 'json')
                      ->addActionContext('totaltype', 'json')
                      ->addActionContext('findingforecast', 'json')
                      ->addActionContext('chartfindnomitstrat', 'json')
                      ->addActionContext('chartfinding', 'json')
                      ->initContext();
    }

    public function indexAction()
    {
        // Top-left chart - Finding Forecast
        $chartFindForecast = new Fisma_Chart(380, 275, 'chartFindForecast', '/finding/dashboard/findingforecast/format/json');
        $chartFindForecast->addWidget('dayRangesStatChart', 'Day Ranges:', 'text', '30, 60, 90, 120');
        $this->view->chartFindForecast = $chartFindForecast->export();
        
        // Top-right chart - Findings Past Due
        $chartOverdueFinding = new Fisma_Chart(380, 275, 'chartOverdueFinding', '/finding/dashboard/chartoverdue/format/json');
        $chartOverdueFinding->addWidget('dayRanges', 'Day Ranges:', 'text', '1, 30, 60, 90, 120');
        $this->view->chartOverdueFinding = $chartOverdueFinding->export();
        
        // Mid-left chart - Findings by Worklow Process
        $chartTotalStatus = new Fisma_Chart(380, 275, 'chartTotalStatus', '/finding/dashboard/chartfinding/format/json');
        $chartTotalStatus
                ->addWidget(
                    'findingType',
                    'Finding Type:',
                    'combo',
                    'All Divided',
                    array(
                        'All Combined',
                        'All Divided',
                        'High',
                        'Moderate',
                        'Low'
                    )
                )
                ->addWidget(
                    'displayBy',
                    'Display By:',
                    'combo',
                    'Status Distribution',
                    array(
                        'Status Distribution',
                        'Organization Owner'
                    )
                );
        
        $this->view->chartTotalStatus = $chartTotalStatus->export();
        
        // Mid-right chart - Findings Without Corrective Actions
        $chartNoMit = new Fisma_Chart(380, 275);
        $chartNoMit
                ->setUniqueid('chartNoMit')
                ->setExternalSource('/finding/dashboard/chartfindnomitstrat/format/json')
                ->addWidget('dayRangesMitChart', 'Day Ranges:', 'text', '1, 30, 60, 90, 120');
        $this->view->chartNoMit = $chartNoMit->export();
        
        $chartTotalType = new Fisma_Chart(380, 275, 'chartTotalType', '/finding/dashboard/totaltype/format/json');
        $this->view->chartTotalType = $chartTotalType->export();
        
        // Bottom chart - Current Security Control Deficiencies
        $controlDeficienciesChart = new Fisma_Chart();
        $controlDeficienciesChart
                ->setUniqueid('chartFindingStatusDistribution')
                ->setWidth(800)
                ->setHeight(275)
                ->setChartType('bar')
                ->setExternalSource('/security-control-chart/control-deficiencies/format/json')
                ->setAlign('center');
                
        $this->view->controlDeficienciesChart = $controlDeficienciesChart->export();
    }
    
    public function chartfindingAction()
    {
        $displayBy = urldecode($this->_request->getParam('displayBy'));
        
        switch ($displayBy) {
            case "Status Distribution":
                $rtnChart = $this->_chartfindingstatus();
                break;
            case "Organization Owner":
                $rtnChart = $this->_chartfindingorg();
                break;
        }
        
        // export as array, the context switch will translate it to a JSON responce
        $this->view->chart = $rtnChart->export('array');
    }

    public function chartoverdueAction()
    {
        $dayRanges = str_replace(' ', '', urldecode($this->_request->getParam('dayRanges')));
        $dayRanges = explode(',', $dayRanges);
        
        $thisChart = new Fisma_Chart();
        $thisChart
            ->setChartType('bar')
            ->setConcatXLabel(true);
        
        // Get counts in between the day ranges given
        for ($x = 1; $x < count($dayRanges); $x++) {
            
            $fromDayDiff = $dayRanges[$x-1];
            $toDayDiff = $dayRanges[$x];
            
            $q = Doctrine_Query::create();
            $q
                ->addSelect(
                    'SUM(IF(DATEDIFF(NOW(), f.nextduedate) BETWEEN ' . 
                    $fromDayDiff .
                    ' AND ' . 
                    $toDayDiff . 
                    ', 1, 0)) a'
                )
                ->from('Finding f')
                ->where('DATEDIFF(NOW(), f.nextduedate) > 0')
                ->setHydrationMode(Doctrine::HYDRATE_SCALAR);
            
            $rslt = $q->execute();
            $rslt = $rslt[0];   // we are only expecting 1 result row
            
            $thisFromDate = new Zend_Date();
            $thisFromDate = $thisFromDate->addDay($fromDayDiff)->toString('YYY-MM-dd');
            $thisToDate = new Zend_Date();
            $thisToDate = $thisToDate->addDay($toDayDiff)->toString('YYY-MM-dd');
            $thisChart->addColumn(
                $fromDayDiff . '-' . $toDayDiff . ' days',
                $rslt['f_a'],
                '/finding/remediation/list/queryType/advanced/nextDueDate/dateBetween/'.$thisFromDate.'/'.$thisToDate
            );
            
        }
        
        // Get the count from the last day range on
        $fromDayDiff = $dayRanges[count($dayRanges)-1];
        
        $q = Doctrine_Query::create();
        $q
            ->addSelect('SUM(IF(DATEDIFF(NOW(), f.nextduedate) >= ' . $fromDayDiff . ', 1, 0)) a')
            ->from('Finding f')
            ->where('DATEDIFF(NOW(), f.nextduedate) > 0')
            ->setHydrationMode(Doctrine::HYDRATE_SCALAR);
            
        $rslt = $q->execute();
        $rslt = $rslt[0];   // we are only expecting 1 result row
        
        $thisFromDate = new Zend_Date();
        $thisFromDate = $thisFromDate->addDay($fromDayDiff)->toString('YYY-MM-dd');
        $thisChart->addColumn(
            $fromDayDiff . '+ days',
            $rslt['f_a'],
            '/finding/remediation/list/queryType/advanced/nextDueDate/dateAfter/'.$thisFromDate
        );
            
        $this->view->chart = $thisChart->export('array');
    }

    /**
     * Calculate the finding statistics by Org
     * 
     * @return Fisma_Chart
     */
    private function _chartfindingorg()
    {
        $findingType = urldecode($this->_request->getParam('findingType'));
        
        if ($findingType === 'All Combined') {
            
            $thisChart = new Fisma_Chart();
            $thisChart
                ->setChartType('bar')
                ->setConcatXLabel(false);
            
            $q = Doctrine_Query::create()
                ->select('count(*), nickname')
                ->from('organization o')
                ->leftJoin('o.Findings f')
                ->groupBy('o.id')
                ->orderBy('o.nickname')
                ->setHydrationMode(Doctrine::HYDRATE_ARRAY);
            $orgCounts = $q->execute();
            
            foreach ($orgCounts as $thisOrg) {
                
                $thisChart->addColumn(
                    $thisOrg['nickname'],
                    $thisOrg['count'],
                    '/finding/remediation/list/queryType/advanced/organization/textExactMatch/' . $thisOrg['nickname']
                );
                
            }
            
            return $thisChart;
            
        } elseif ($findingType === 'All Divided') {
            
            $thisChart = new Fisma_Chart();
            $thisChart
                ->inheritanceControle('minimal')
                ->setChartType('stackedbar')
                ->setConcatXLabel(true)
                ->setColors(
                    array(
                        "#FF0000",
                        "#FF6600",
                        "#FFC000"
                    )
                )
                ->setLayerLabels(
                    array(
                        'HIGH',
                        'MODERATE',
                        'LOW'
                    )
                );
            
            $q = Doctrine_Query::create()
                ->select('count(f.threatlevel), nickname, f.threatlevel')
                ->from('organization o')
                ->leftJoin('o.Findings f')
                ->groupBy('o.id, f.threatlevel')
                ->orderBy('o.nickname, f.threatlevel')
                ->setHydrationMode(Doctrine::HYDRATE_ARRAY);
            
            $orgCounts = $q->execute();
            
            foreach ($orgCounts as $thisOrg) {
                
                // initalize counts to 0
                $thisHigh = 0;
                $thisMod = 0;
                $thisLow = 0;
                
                foreach ($thisOrg['Findings'] as $thisLevel) {
                    switch ($thisLevel['threatLevel']) {
                        case 'LOW':
                            $thisHigh = $thisLevel['count'];
                            break;
                        case 'MODERATE':
                            $thisMod = $thisLevel['count'];
                            break;
                        case 'HIGH':
                            $thisLow = $thisLevel['count'];
                            break;
                    }
                }
                
                $thisChart->addColumn(
                    $thisOrg['nickname'],
                    array(
                        $thisLow,
                        $thisMod,
                        $thisHigh
                    ),
                    array(
                        '/finding/remediation/list/queryType/advanced/' . 
                        'organization/textExactMatch/' . $thisOrg['nickname'] . 
                        '/threatLevel/enumIs/HIGH',
                        '/finding/remediation/list/queryType/advanced/' . 
                        'organization/textExactMatch/' . $thisOrg['nickname'] . 
                        '/threatLevel/enumIs/MODERATE',
                        '/finding/remediation/list/queryType/advanced/' . 
                        'organization/textExactMatch/' . $thisOrg['nickname'] . 
                        '/threatLevel/enumIs/LOW'
                    )
                );
                
            }
            
            return $thisChart;
            
        } else {
            // findingType is High, Mod, or Low
            
            $thisChart = new Fisma_Chart();
            $thisChart
                ->setChartType('bar')
                ->setConcatXLabel(false);
            
            // Decide color of every bar based on High/Mod/Low
            switch (strtoupper($findingType)) {
            case 'HIGH':
                $thisChart->setColors(array('#FF0000'));    // red
                break;
            case 'MODERATE':
                $thisChart->setColors(array('#FF6600'));    // orange
                break;
            case 'LOW':
                $thisChart->setColors(array('#FFC000'));    // yellow
                break;
            }
            
            $q = Doctrine_Query::create()
                ->select('count(f.threatlevel), nickname, f.threatlevel')
                ->from('organization o')
                ->leftJoin('o.Findings f')
                ->groupBy('o.id')
                ->orderBy('o.nickname, f.threatlevel')
                ->where('f.threatlevel = ?', strtoupper($findingType))
                ->setHydrationMode(Doctrine::HYDRATE_ARRAY);
            
            $orgThisThreatCounts = $q->execute();
            
            foreach ($orgThisThreatCounts as $thisThreatCount) {
                $thisChart->addColumn(
                    $thisThreatCount['nickname'],
                    $thisThreatCount['count'],
                    '/finding/remediation/list/queryType/advanced' .
                    '/organization/textExactMatch/' . $thisThreatCount['nickname'] . 
                    '/threatLevel/enumIs/' . strtoupper($findingType)
                );
            }
            
            return $thisChart;
        }
    }
    
    /**
     * Calculate the finding statistics by status
     * 
     * @return Fisma_Chart
     */
    private function _chartfindingstatus()
    {
        $findingType = urldecode($this->_request->getParam('findingType'));
        
        if ($findingType === 'All Combined') {
            
            $q = Doctrine_Query::create()
                 ->select('f.status, e.nickname')
                 ->addSelect('COUNT(f.status) AS statusCount, COUNT(e.nickname) AS subStatusCount')
                 ->from('Finding f')
                 ->leftJoin('f.CurrentEvaluation e')
                 ->whereIn('f.responsibleOrganizationId ', $this->_myOrgSystemIds)
                 ->groupBy('f.status, e.nickname')
                 ->setHydrationMode(Doctrine::HYDRATE_ARRAY);
            $results = $q->execute();
            
            // initialize 3 basic status
            $arrTotal = array('NEW' => 0, 'DRAFT' => 0);
            // initialize current evaluation status
            $q = Doctrine_Query::create()
                 ->select()
                 ->from('Evaluation e')
                 // keep the the 'action' approvalGroup is first fetched
                 ->orderBy('e.approvalGroup ASC')
                 ->setHydrationMode(Doctrine::HYDRATE_ARRAY);
            $evaluations = $q->execute();

            foreach ($evaluations as $evaluation) {
                if ($evaluation['approvalGroup'] == 'evidence') {
                    $arrTotal['EN'] = 0;
                }
                $arrTotal[$evaluation['nickname']] = 0;
            }

            foreach ($results as $result) {
                if (in_array($result['status'], array_keys($arrTotal))) {
                    $arrTotal[$result['status']] = (integer) $result['statusCount'];
                } elseif (!empty($result['CurrentEvaluation']['nickname'])) {
                    $arrTotal[$result['CurrentEvaluation']['nickname']] = (integer) $result['subStatusCount'];
                }
            }
            
            $thisChart = new Fisma_Chart();
            $thisChart
                ->setChartType('bar')
                ->setConcatXLabel(false)
                ->setData(array_values($arrTotal))
                ->setAxisLabelsX(array_keys($arrTotal))
                ->setLinks(
                    '/finding/remediation/list/queryType/advanced/denormalizedStatus/textExactMatch/#ColumnLabel#'
                );
                
            return $thisChart;
            
        }
        
        // If we have not returned by this line, then the findingType is either High/Moderate/Low/All-Divided
        
        $thisChart = new Fisma_Chart();
        $thisChart
            ->setConcatXLabel(true);
        
        if ($findingType === 'High'|| $findingType === 'Moderate' || $findingType === 'Low') {
            
            // Display a simple bar chart of just High/Mod/Low findings
            $thisChart
                ->setChartType('bar')
                ->setConcatXLabel(false);
            
            // Decise color of every bar based on High/Mod/Low
            switch (strtoupper($findingType)) {
            case 'HIGH':
                $thisChart->setColors(array('#FF0000'));
                break;
            case 'MODERATE':
                $thisChart->setColors(array('#FF6600'));
                break;
            case 'LOW':
                $thisChart->setColors(array('#FFC000'));
                break;
            }
            
        } elseif ($findingType === 'All Divided') {
            
            // Display a stacked-bar chart with High/Mod/Low findings in each column
            $thisChart
                ->setChartType('stackedbar')
                ->setColors(
                    array(
                        "#FF0000",
                        "#FF6600",
                        "#FFC000"
                    )
                )
                ->setLayerLabels(
                    array(
                        'High',
                        'Moderate',
                        'Low'
                    )
                );
            
        }
        
        // Query database
        $q = Doctrine_Query::create()
            ->select('count(*), threatlevel, denormalizedstatus')
            ->from('Finding f')
            ->groupBy('f.denormalizedstatus, f.threatlevel')
            ->orderBy('f.denormalizedstatus, f.threatlevel')
            ->setHydrationMode(Doctrine::HYDRATE_ARRAY);
        $rslts = $q->execute();
        
        // sort results into $sortedRslts[FindingStatusName][High/Mod/Low] = TheCount
        $sortedRslts = array();
        foreach ($rslts as $thisRslt) {
            
            if (empty($sortedRslts[$thisRslt['denormalizedStatus']])) {
                $sortedRslts[$thisRslt['denormalizedStatus']] = array();
            }
            
            $sortedRslts[$thisRslt['denormalizedStatus']][$thisRslt['threatLevel']] = $thisRslt['count'];
        }
        
        // Go in order adding columns to chart; New,Draft,MS ISSO, MS IV&V, EN, EV ISSO, EV IV&V
        for ($x = 0; $x < 7; $x++) {
            
            // Which status are we adding this time?
            switch ($x) {
            case 0:
                $thisStatus = 'NEW';
                break;
            case 1:
                $thisStatus = 'DRAFT';
                break;
            case 2:
                $thisStatus = 'MS ISSO';
                break;
            case 3:
                $thisStatus = 'MS IV&V';
                break;
            case 4:
                $thisStatus = 'EN';
                break;
            case 5:
                $thisStatus = 'EV ISSO';
                break;
            case 6:
                $thisStatus = 'EV IV&V';
                break;
            }
            
            // Is it Or All-Migh&Mod&Low in a stacked bar chart? Or just High, Mod, or Low in a regular chart?
            if ($findingType === 'All Divided') {
                $addColumnData = array(
                        $sortedRslts[$thisStatus]['HIGH'],
                        $sortedRslts[$thisStatus]['MODERATE'],
                        $sortedRslts[$thisStatus]['LOW']
                    );
                $addLink = array(
                        '/finding/remediation/list/queryType/advanced' .
                            '/denormalizedStatus/textExactMatch/' . strtoupper($thisStatus) . 
                            '/threatLevel/enumIs/HIGH',
                        '/finding/remediation/list/queryType/advanced' .
                            '/denormalizedStatus/textExactMatch/' . strtoupper($thisStatus) . 
                            '/threatLevel/enumIs/MODERATE',
                        '/finding/remediation/list/queryType/advanced' .
                            '/denormalizedStatus/textExactMatch/' . strtoupper($thisStatus) . 
                            '/threatLevel/enumIs/LOW'
                    );
            } else {
                $addColumnData = $sortedRslts[$thisStatus][strtoupper($findingType)];
                $addLink = '/finding/remediation/list/queryType/advanced' .
                            '/denormalizedStatus/textExactMatch/' . strtoupper($thisStatus);
            }
            
            $thisChart->addColumn(
                $thisStatus,
                $addColumnData,
                $addLink
            );
        }
        
        return $thisChart;
    }

    /**
     * Calculate "finding forcast" data for a chart based on finding.currentecd in the database
     * 
     * @return void
     */
    public function chartfindnomitstratAction()
    {
        
        $dayRange = $this->_request->getParam('dayRangesMitChart');
        $dayRange = str_replace(' ', '', $dayRange);
        $dayRange = explode(',', $dayRange);
        
        $highCount = array();
        $modCount = array();
        $lowCount = array();
        $chartDataText = array();
        
        for ($x = 1; $x < count($dayRange); $x++) {
            
            $fromDay = $dayRange[$x-1];
            $toDay = $dayRange[$x];
            
            // Get the count of High findings
            $q = Doctrine_Query::create()
                ->select()
                ->from('Finding f')
                ->where(
                    'f.threatlevel = "LOW" AND ' . 
                    '(f.status="NEW" OR f.status="DRAFT") AND ' . 
                    '(DATEDIFF(NOW(), f.createdts) BETWEEN "' . $fromDay . '" AND "' . $toDay . '")'
                )                
                ->setHydrationMode(Doctrine::HYDRATE_ARRAY);
            $highCount[] = $q->count();
            
            // Get the count of Moderate findings
            $q = Doctrine_Query::create()
                ->select()
                ->from('Finding f')
                ->where(
                    'f.threatlevel = "MODERATE" AND ' . 
                    '(f.status="NEW" OR f.status="DRAFT") AND ' . 
                    '(DATEDIFF(NOW(), f.createdts) BETWEEN "' . $fromDay . '" AND "' . $toDay . '")'
                )                 
                ->setHydrationMode(Doctrine::HYDRATE_ARRAY);
            $modCount[] = $q->count();
            
            // Get the count of Low findings
            $q = Doctrine_Query::create()
                ->select()
                ->from('Finding f')
                ->where(
                    'f.threatlevel = "HIGH" AND ' . 
                    '(f.status="NEW" OR f.status="DRAFT") AND ' . 
                    '(DATEDIFF(NOW(), f.createdts) BETWEEN "' . $fromDay . '" AND "' . $toDay . '")'
                )                 
                ->setHydrationMode(Doctrine::HYDRATE_ARRAY);
            $lowCount[] = $q->count();
            
            $chartDataText[] = $fromDay . '-' . $toDay . ' days';
            
        }
        
        $chartData = array($highCount, $modCount, $lowCount);
        
        $noMitChart = new Fisma_Chart();
        $noMitChart
            ->setChartType('stackedbar')
            ->setColors(
                array(
                    "#FF0000",
                    "#FF6600",
                    "#FFC000"
                )
            )
            ->setConcatXLabel(false)
            ->setLayerLabels(array('High', 'Moderate', 'Low'))
            ->setData($chartData)
            ->setAxisLabelsX($chartDataText);
            
        // export as array, the context switch will translate it to a JSON responce
        $this->view->chart = $noMitChart->export('array');
    }

    /**
     * Calculate "finding forcast" data for a chart based on finding.currentecd in the database
     * 
     * @return void
     */
    public function findingforecastAction()
    {
        
        $dayRange = $this->_request->getParam('dayRangesStatChart');
        $dayRange = str_replace(' ', '', $dayRange);
        $dayRange = explode(',', $dayRange);
        
        $highCount = array();
        $modCount = array();
        $lowCount = array();
        $chartDataText = array();
        
        $thisChart = new Fisma_Chart();
        $thisChart
            ->setChartType('stackedbar')
            ->setConcatXLabel(false)
            ->setLayerLabels(
                array(
                    'High',
                    'Moderate',
                    'Low'
                )
            )
            ->setColors(
                array(
                    "#FF0000",
                    "#FF6600",
                    "#FFC000"
                )
            );
        
        for ($x = 0; $x < count($dayRange); $x++) {
            
            if ($x === 0) {
                $fromDay = new Zend_Date();
            } else {
                $fromDay = $lastToDay;
            }
            $fromDayStr = $fromDay->toString('YYY-MM-dd');
            
            $toDay = new Zend_Date();
            $toDay = $toDay->addDay($dayRange[$x]);
            $toDayStr = $toDay->toString('YYY-MM-dd');
            
            // Get the count of High findings
            $q = Doctrine_Query::create()
                ->select()
                ->from('Finding f')
                ->where(
                    'f.countermeasureseffectiveness = "HIGH" AND ' .
                    '(f.currentecd BETWEEN "' . $fromDayStr . '" AND "' . $toDayStr . '")'
                )                 
                ->setHydrationMode(Doctrine::HYDRATE_ARRAY);
            $highCount = $q->count();
            
            // Get the count of Moderate findings
            $q = Doctrine_Query::create()
                ->select()
                ->from('Finding f')
                ->where(
                    'f.countermeasureseffectiveness = "MODERATE" AND ' .
                    '(f.currentecd BETWEEN "' . $fromDayStr . '" AND "' . $toDayStr . '")'
                )                 
                ->setHydrationMode(Doctrine::HYDRATE_ARRAY);
            $modCount = $q->count();
            
            // Get the count of Low findings
            $q = Doctrine_Query::create()
                ->select()
                ->from('Finding f')
                ->where(
                    'f.countermeasureseffectiveness = "LOW" AND ' .
                    '(f.currentecd BETWEEN "' . $fromDayStr . '" AND "' . $toDayStr . '")'
                )                 
                ->setHydrationMode(Doctrine::HYDRATE_ARRAY);
            $lowCount = $q->count();
            
            $thisChart->addColumn(
                $fromDay->toString('MMM dd') . ' - ' . $toDay->toString('MMM dd'),
                array(
                    $highCount,
                    $modCount,
                    $lowCount
                ),
                array(
                    '/finding/remediation/list/queryType/advanced' . 
                    '/currentEcd/dateBetween/' . $fromDay->toString('YYYY-MM-dd').'/'.$toDay->toString('YYYY-MM-dd') .
                    '/threatLevel/enumIs/HIGH',
                    '/finding/remediation/list/queryType/advanced' . 
                    '/currentEcd/dateBetween/' . $fromDay->toString('YYYY-MM-dd').'/'.$toDay->toString('YYYY-MM-dd') .
                    '/threatLevel/enumIs/MODERATE',
                    '/finding/remediation/list/queryType/advanced' . 
                    '/currentEcd/dateBetween/' . $fromDay->toString('YYYY-MM-dd').'/'.$toDay->toString('YYYY-MM-dd') .
                    '/threatLevel/enumIs/LOW'
                )
            );
            
            $lastToDay = $toDay;
        }
        
        // export as array, the context switch will translate it to a JSON responce
        $this->view->chart = $thisChart->export('array');
    }

}
