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
 * The dashboard controller displays the user dashboard when the user first logs
 * in. This controller also produces graphical charts in conjunction with the SWF Charts
 * package.
 *
 * @author     Jim Chen <xhorse@users.sourceforge.net>
 * @copyright  (c) Endeavor Systems, Inc. 2009 {@link http://www.endeavorsystems.com}
 * @license    http://www.openfisma.org/content/license GPLv3
 * @package    Controller
 * @version    $Id$
 */
class DashboardController extends Fisma_Zend_Controller_Action_Security
{
    /**
     * My OrgSystem ids
     *
     * Not initialized until preDispatch
     *
     * @var array
     */
    private $_myOrgSystemIds = null;
    
    /**
     * Invoked before each Actions
     * 
     * @return void
     */
    function preDispatch()
    {
        parent::preDispatch();

        $this->_acl->requireArea('dashboard');

        $orgSystems = $this->_me->getOrganizationsByPrivilege('finding', 'read')->toArray();
        $orgSystemIds = array(0);
        foreach ($orgSystems as $orgSystem) {
            $orgSystemIds[] = $orgSystem['id'];
        }
        $this->_myOrgSystemIds = $orgSystemIds;

        $this->_helper->fismaContextSwitch()
                      ->addActionContext('chartoverdue', 'json')
                      ->addActionContext('chartfindingstatus', 'json')
                      ->addActionContext('totaltype', 'json')
                      ->addActionContext('findingforecast', 'json')
                      ->addActionContext('chartfindnomitstrat', 'json')
                      ->addActionContext('chartfinding', 'json')
                      ->initContext();
    }

    /**
     * The user dashboard displays important system-wide metrics, charts, and graphs
     * 
     * @return void
     */
    public function indexAction()
    {
        $user = new User();
        $user = $user->getTable()->find($this->_me->id);
        // Check to see if we got passed a "dismiss" parameter to dismiss notifications
        $dismiss = $this->_request->getParam('dismiss');
        if (isset($dismiss) && 'notifications' == $dismiss) {
            $user->Notifications->delete();
            $user->mostRecentNotifyTs = Fisma::now();
            $user->save();
        }

        // Calculate the dashboard statistics
        $totalFindingsQuery = Doctrine_Query::create()
                            ->select('COUNT(*) as count')
                            ->from('Finding f')
                            ->whereIn('f.responsibleorganizationid', $this->_myOrgSystemIds);
        $result = $totalFindingsQuery->fetchOne();
        $alert['TOTAL']  = $result['count'];
        
        $newFindingsQuery = Doctrine_Query::create()
                            ->select('COUNT(*) as count')
                            ->from('Finding f')
                            ->where('f.status = ?', 'NEW')
                            ->andWhereIn('f.responsibleorganizationid', $this->_myOrgSystemIds);
        $result = $newFindingsQuery->fetchOne();
        $alert['NEW']  = $result['count'];
        
        $draftFindingsQuery = Doctrine_Query::create()
                            ->select('COUNT(*) as count')
                            ->from('Finding f')
                            ->where('f.status = ?', 'DRAFT')
                            ->andWhereIn('f.responsibleorganizationid', $this->_myOrgSystemIds);
        $result = $draftFindingsQuery->fetchOne();
        $alert['DRAFT']  = $result['count'];

        $enFindingsQuery = Doctrine_Query::create()
                            ->select('COUNT(*) as count')
                            ->from('Finding f')
                            ->where('f.status = ? AND DATEDIFF(NOW(), f.nextDueDate) <= 0', 'EN')
                            ->andWhereIn('f.responsibleorganizationid', $this->_myOrgSystemIds);
        $result = $enFindingsQuery->fetchOne();
        $alert['EN']  = $result['count'];

        $eoFindingsQuery = Doctrine_Query::create()
                            ->select('COUNT(*) as count')
                            ->from('Finding f')
                            ->where('f.status = ? AND DATEDIFF(NOW(), f.nextDueDate) > 0', 'EN')
                            ->andWhereIn('f.responsibleorganizationid', $this->_myOrgSystemIds);
        $result = $eoFindingsQuery->fetchOne();
        $alert['EO']  = $result['count'];

        if ($this->_acl->hasPrivilegeForClass('approve', 'Finding')) {
            $pendingFindingsQuery = Doctrine_Query::create()
                                    ->select('COUNT(*) as count')
                                    ->from('Finding f')
                                    ->where('f.status = ?', 'PEND')
                                    ->andWhereIn('f.responsibleorganizationid', $this->_myOrgSystemIds);
            $result = $pendingFindingsQuery->fetchOne();
            $alert['PEND'] = $result['count'];
        }

        $this->view->alert = $alert;

        // URLs for "Alerts" panel
        $baseUrl = '/finding/remediation/list/queryType/advanced';

        $this->view->newFindingUrl = $baseUrl . '/denormalizedStatus/textExactMatch/NEW';
        $this->view->draftFindingUrl = $baseUrl . '/denormalizedStatus/textExactMatch/DRAFT';
        $this->view->pendingFindingUrl = '/finding/index/approve';
        
        $today = Zend_Date::now()->toString('yyyy-MM-dd');        
        $this->view->evidenceNeededOntimeUrl = $baseUrl 
                                             . '/denormalizedStatus/textExactMatch/EN'
                                             . '/nextDueDate/dateAfter/'
                                             . $today;
        $this->view->evidenceNeededOverdueUrl = $baseUrl 
                                             . '/denormalizedStatus/textExactMatch/EN'
                                             . '/nextDueDate/dateBefore/'
                                             . $today;
                                             
        // URLs for chart click event handlers
        $this->view->barChartBaseUrl = $baseUrl . '/denormalizedStatus/textExactMatch/';
        $this->view->pieChartBaseUrl = $baseUrl . '/type/enumIs/';
        
        // Look up the last login information. If it's their first time logging in, then the view
        // script will show a different message.
        $lastLoginInfo = new Zend_Session_Namespace('last_login_info');
        
        if (isset($lastLoginInfo->lastLoginTs)) {
            $lastLoginDate = new Zend_Date($lastLoginInfo->lastLoginTs, Zend_Date::ISO_8601);
            $this->view->lastLoginTs = $lastLoginDate->toString(Fisma_Date::FORMAT_WEEKDAY_MONTH_NAME_SHORT_DAY_TIME);
            $this->view->lastLoginIp = $lastLoginInfo->lastLoginIp;
            $this->view->failureCount = $lastLoginInfo->failureCount;
        } else {
            $this->view->applicationName = Fisma::configuration()->getConfig('system_name');
        }
        
        if ($user->Notifications->count() > 0) {
            $this->view->notifications = $user->Notifications;
            $this->view->dismissUrl = "/dashboard/index/dismiss/notifications";
        }
        
        $chartTotalStatus = new Fisma_Chart(380, 275, 'chartTotalStatus', '/dashboard/chartfinding/format/json');
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
        
        $chartTotalType = new Fisma_Chart(380, 275, 'chartTotalType', '/dashboard/totaltype/format/json');
        $this->view->chartTotalType = $chartTotalType->export();
        
        $chartFindForecast = new Fisma_Chart(380, 275, 'chartFindForecast', '/dashboard/findingforecast/format/json');
        $chartFindForecast->addWidget('dayRangesStatChart', 'Day Ranges:', 'text', '30, 60, 90, 120');
        $this->view->chartFindForecast = $chartFindForecast->export();
        
        $chartOverdueFinding = new Fisma_Chart(380, 275, 'chartOverdueFinding', '/dashboard/chartoverdue/format/json/');
        $this->view->chartOverdueFinding = $chartOverdueFinding->export();
        
        $chartNoMit = new Fisma_Chart(380, 275);
        $chartNoMit
                ->setUniqueid('chartNoMit')
                ->setExternalSource('/dashboard/chartfindnomitstrat/format/json')
                ->addWidget('dayRangesMitChart', 'Day Ranges:', 'text', '30, 60, 90, 120');
        $this->view->chartNoMit = $chartNoMit->export();
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
        $q = Doctrine_Query::create()
            ->addSelect('SUM(IF(DATEDIFF(NOW(), f.nextduedate) BETWEEN 0 AND 29, 1, 0)) a')
            ->addSelect('SUM(IF(DATEDIFF(NOW(), f.nextduedate) BETWEEN 30 AND 59, 1, 0)) b')
            ->addSelect('SUM(IF(DATEDIFF(NOW(), f.nextduedate) BETWEEN 60 AND 89, 1, 0)) c')
            ->addSelect('SUM(IF(DATEDIFF(NOW(), f.nextduedate) BETWEEN 90 AND 119, 1, 0)) d')
            ->addSelect('SUM(IF(DATEDIFF(NOW(), f.nextduedate) >= 120, 1, 0)) e')
            ->addSelect('IFNULL(COUNT(f.id), 0) f')
            ->addSelect('SUM(IF(DATEDIFF(NOW(), f.nextduedate) >= 120, 1, 0)) h')
            ->from('Finding f')
            ->where('DATEDIFF(NOW(), f.nextduedate) > 0')
            ->setHydrationMode(Doctrine::HYDRATE_SCALAR);
        
        $rslt = $q->execute();
        $rslt = $rslt[0];   // we are only expecting 1 result row
        
        $thisChart = new Fisma_Chart();
        $thisChart
            ->setTitle('Overdue findings')
            ->setChartType('bar')
            ->setConcatXLabel(true);
        
        $thisFromDate = new Zend_Date();
        $thisFromDate = $thisFromDate->addDay(1)->toString('YYY-MM-dd');
        $thisToDate = new Zend_Date();
        $thisToDate = $thisToDate->addDay(29)->toString('YYY-MM-dd');
        $thisChart->addColumn(
            '1-29 days',
            $rslt['f_a'],
            '/finding/remediation/list/queryType/advanced/nextDueDate/dateBetween/'.$thisFromDate.'/'.$thisToDate
        );
        
        $thisFromDate = new Zend_Date();
        $thisFromDate = $thisFromDate->addDay(30)->toString('YYY-MM-dd');
        $thisToDate = new Zend_Date();
        $thisToDate = $thisToDate->addDay(59)->toString('YYY-MM-dd');
        $thisChart->addColumn(
            '30-59 days',
            $rslt['f_b'],
            '/finding/remediation/list/queryType/advanced/nextDueDate/dateBetween/'.$thisFromDate.'/'.$thisToDate
        );
        
        $thisFromDate = new Zend_Date();
        $thisFromDate = $thisFromDate->addDay(60)->toString('YYY-MM-dd');
        $thisToDate = new Zend_Date();
        $thisToDate = $thisToDate->addDay(89)->toString('YYY-MM-dd');
        $thisChart->addColumn(
            '60-89 days',
            $rslt['f_c'],
            '/finding/remediation/list/queryType/advanced/nextDueDate/dateBetween/'.$thisFromDate.'/'.$thisToDate
        );
        
        $thisFromDate = new Zend_Date();
        $thisFromDate = $thisFromDate->addDay(90)->toString('YYY-MM-dd');
        $thisToDate = new Zend_Date();
        $thisToDate = $thisToDate->addDay(119)->toString('YYY-MM-dd');
        $thisChart->addColumn(
            '90-119 days',
            $rslt['f_d'],
            '/finding/remediation/list/queryType/advanced/nextDueDate/dateBetween/'.$thisFromDate.'/'.$thisToDate
        );
        
        $thisFromDate = new Zend_Date();
        $thisFromDate = $thisFromDate->addDay(120)->toString('YYY-MM-dd');
        $thisChart->addColumn(
            '120+ days',
            $rslt['f_e'],
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
                ->setTitle('Finding Status Distribution')
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
                ->setTitle('Finding Status Distribution')
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
                ->setTitle('Finding Status Distribution')
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
                ->setTitle('Finding Status Distribution')
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
            ->setTitle('Finding Status Distribution')
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
            ->setTitle('Findings With No Mitigation Strategy')
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
            ->setTitle('Finding Forecast')
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

    /**
     * Calculate the statistics by type
     * 
     * @return void
     */
    public function totaltypeAction()
    {
        $summary = array(
            'NONE' => 0,
            'CAP' => 0,
            'FP' => 0,
            'AR' => 0
        );
        
        $q = Doctrine_Query::create()
            ->select('f.type')
            ->addSelect('COUNT(f.type) as typeCount')
            ->from('Finding f')
            ->whereIn('f.responsibleOrganizationId ', $this->_myOrgSystemIds)
            ->groupBy('f.type');
        $results =$q->execute()->toArray();
        $types = array_keys($summary);
        foreach ($results as $result) {
            if (in_array($result['type'], $types)) {
                $summary[$result['type']] = (integer) $result['typeCount'];
            }
        }
        
        $thisChart = new Fisma_Chart();
        $thisChart
            ->setTitle('Mitigation Strategy Distribution')
            ->setChartType('pie')
            ->setData(array_values($summary))
            ->setAxisLabelsX(array_keys($summary))
            ->setLinks(
                array(
                    '/finding/remediation/list/queryType/advanced/type/enumIs/NONE',
                    '/finding/remediation/list/queryType/advanced/type/enumIs/CAP',
                    '/finding/remediation/list/queryType/advanced/type/enumIs/FP',
                    '/finding/remediation/list/queryType/advanced/type/enumIs/AR'
                )
            );
        
        // export as array, the context switch will translate it to a JSON responce
        $this->view->chart = $thisChart->export('array');
    }
}
