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
                      ->addActionContext('totalstatus', 'xml')
                      ->addActionContext('totalstatus', 'json')
                      ->addActionContext('totaltype', 'xml')
                      ->addActionContext('totaltype', 'json')
                      ->addActionContext('findingforecast', 'json')
                      ->addActionContext('findingnomitstrat', 'json')
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
        
        $chartTotalStatus = new Fisma_Chart(380, 275, 'chartTotalStatus', '/dashboard/totalstatus/format/json');
        $this->view->chartTotalStatus = $chartTotalStatus->export();
        
        $chartTotalType = new Fisma_Chart(380, 275, 'chartTotalType', '/dashboard/totaltype/format/json');
        $this->view->chartTotalType = $chartTotalType->export();
        
        $chartFindForecast = new Fisma_Chart(380, 275, 'chartFindForecast', '/dashboard/findingforecast/format/json');
        $chartFindForecast->addWidget('dayRangesStatChart', 'Day Ranges:', 'text', '30, 60, 90, 120');
        $this->view->chartFindForecast = $chartFindForecast->export();
        
        $chartNoMit = new Fisma_Chart(380, 275);
        $chartNoMit
                ->setUniqueid('chartNoMit')
                ->setExternalSource('/dashboard/findingnomitstrat/format/json')
                ->addWidget('dayRangesMitChart', 'Day Ranges:', 'text', '30, 60, 90, 120');
        $this->view->chartNoMit = $chartNoMit->export();
    }

    /**
     * Calculate "finding forcast" data for a chart based on finding.currentecd in the database
     * 
     * @return void
     */
    public function findingnomitstratAction()
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
            ->setData($chartData)
            ->setAxisLabelsX($chartDataText)
            ->setLayerLabels(
                array(
                    'High',
                    'Moderate',
                    'Low'
                )
            );
        
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
            $highCount[] = $q->count();
            
            // Get the count of Moderate findings
            $q = Doctrine_Query::create()
                ->select()
                ->from('Finding f')
                ->where(
                    'f.countermeasureseffectiveness = "MODERATE" AND ' .
                    '(f.currentecd BETWEEN "' . $fromDayStr . '" AND "' . $toDayStr . '")'
                )                 
                ->setHydrationMode(Doctrine::HYDRATE_ARRAY);
            $modCount[] = $q->count();
            
            // Get the count of Low findings
            $q = Doctrine_Query::create()
                ->select()
                ->from('Finding f')
                ->where(
                    'f.countermeasureseffectiveness = "LOW" AND ' .
                    '(f.currentecd BETWEEN "' . $fromDayStr . '" AND "' . $toDayStr . '")'
                )                 
                ->setHydrationMode(Doctrine::HYDRATE_ARRAY);
            $lowCount[] = $q->count();
            
            $chartDataText[] = $fromDay->toString('MMM dd') . ' - ' . $toDay->toString('MMM dd');
            
            $lastToDay = $toDay;
        }
        
        $chartData = array($highCount, $modCount, $lowCount);
        $chartLayerText = array('High', 'Moderate', 'Low');
        
        $thisChart = new Fisma_Chart();
        $thisChart
                ->setTitle('Finding Forecast')
                ->setChartType('stackedbar')
                ->setColors(
                    array(
                        "#FF0000",
                        "#FF6600",
                        "#FFC000"
                    )
                )
                ->setConcatXLabel(false)
                ->setData($chartData)
                ->setAxisLabelsX($chartDataText)
                ->setLayerLabels(chartLayerText);
                
        // export as array, the context switch will translate it to a JSON responce
        $this->view->chart = $thisChart->export('array');
    }
    
    /**
     * Calculate the statistics by status
     * 
     * @return void
     */
    public function totalstatusAction()
    {        
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
            ->setConcatXLabel(true)
            ->setData(array_values($arrTotal))
            ->setAxisLabelsX(array_keys($arrTotal))
            ->setLinks('/finding/remediation/list/queryType/advanced/denormalizedStatus/textExactMatch/#ColumnLabel#');
            
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
