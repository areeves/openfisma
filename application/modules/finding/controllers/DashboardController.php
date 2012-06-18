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
 * @copyright  (c) Endeavor Systems, Inc. 2010 {@link http://www.endeavorsystems.com}
 * @license    http://www.openfisma.org/content/license GPLv3
 * @package    Controllers
 */
class Finding_DashboardController extends Fisma_Zend_Controller_Action_Security
{
    const COLOR_HIGH = "#FF0000";
    const COLOR_MODERATE = "#FF6600";
    const COLOR_LOW = "#FFC000";
    const COLOR_BLUE = " #3366FF";

    /**
     * Mapping from enum strings used in the DB to User-Friendly strings used in the UI
     *
     * @var array
     */
    protected $_threatTypes = array('threat_level' => 'Threat Level', 'residual_risk' => 'Residual Risk');

    /**
     * List of threat/risk levels
     *
     * @var array
     */
    protected $_threatLevels = array( 'Totals', 'High, Moderate, and Low', 'High', 'Moderate', 'Low');

    /**
     * Low, Moderate and High Colors
     *
     * @var array
     */
    protected $_highModLowColors = array(self::COLOR_HIGH, self::COLOR_MODERATE, self::COLOR_LOW);

    /**
     * Set ajaxContect on analystAction and chartsAction
     */
    public function init()
    {
        $this->_helper->ajaxContext()
            ->addActionContext('analyst', 'html')
            ->addActionContext('charts', 'html')
            ->initContext();
        parent::init();
    }

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
            ->addActionContext('total-type', 'json')
            ->addActionContext('findingforecast', 'json')
            ->addActionContext('chartfindnomitstrat', 'json')
            ->addActionContext('chartfindingbyorgdetail', 'json')
            ->initContext();



        $this->_visibleOrgs = $this->_me
            ->getOrganizationsByPrivilegeQuery('finding', 'read')
            ->select('o.id')
            ->execute()
            ->toKeyValueArray('id', 'id');
    }

    /**
     * The index page for Finding Dashboard
     *
     * @GETAllowed
     */
    public function indexAction()
    {
        $tabView = new Fisma_Yui_TabView('FindingDashboard');

        $tabView->addTab("Analyst View", "/finding/dashboard/analyst/format/html");
        $tabView->addTab("Executive View", "/finding/summary/index/format/html");
        $tabView->addTab("Charts", "/finding/dashboard/charts/format/html");

        $this->view->tabView = $tabView;
    }

    /**
     * Load the anlyst view in a tab
     *
     * @GETAllowed
     */
    public function analystAction()
    {
        $orgSystems = $this->_me->getOrganizationsByPrivilege('finding', 'read')->toArray();
        $myOrgSystemIds = array(0);
        foreach ($orgSystems as $orgSystem) {
            $myOrgSystemIds[] = $orgSystem['id'];
        }

        $totalFindingsQuery = Doctrine_Query::create()
            ->select('COUNT(id) as count')
            ->from('Finding f')
            ->where('f.deleted_at is NULL AND f.status <> ?', 'CLOSED')
            ->andWhereIn('f.responsibleOrganizationId', $myOrgSystemIds)
            ->orWhere('f.status <> ?', 'CLOSED')
            ->andWhere('f.pocId = ?', $this->_me->id);
        $result = $totalFindingsQuery->fetchOne();
        $this->view->total = $result['count'];
        if ($this->view->total < 1) {
            $this->view->message = "There are no unresolved findings under your responsibility.";
            return;
        }

        $findingsByTime = Doctrine_Query::create()
            ->select('f.id, f.nextDueDate')
            ->from('Finding f')
            ->where('f.deleted_at is NULL AND f.status <> ?', 'CLOSED')
            ->andWhereIn('f.responsibleorganizationid', $myOrgSystemIds)
            ->orWhere('f.status <> ?', 'CLOSED')
            ->andWhere('f.pocId = ?', $this->_me->id)
            ->execute();
        $this->view->byTime = array(
            array(
                'criteria' => 'On-Time',
                'count' => 0
            ),
            array(
                'criteria' => 'Overdue',
                'count' => 0
            )
        );
        foreach ($findingsByTime as $finding) {
            $nextDueDate = new Zend_Date($finding->nextDueDate, Fisma_Date::FORMAT_DATE);
            if (!is_null($finding->nextDueDate)) {
                if ($nextDueDate->compareDate(new Zend_Date()) >= 0) {
                    $this->view->byTime[0]['count']++;
                } else {
                    $this->view->byTime[1]['count']++;
                }
            }
        }

        $this->view->byThreat = Doctrine_Query::create()
            ->select('COUNT(id) as count, threatlevel as criteria')
            ->from('Finding f')
            ->groupBy('f.threatlevel')
            ->where('f.deleted_at is NULL AND f.status <> ?', 'CLOSED')
            ->andWhereIn('f.responsibleorganizationid', $myOrgSystemIds)
            ->orWhere('f.status <> ?', 'CLOSED')
            ->andWhere('f.pocId = ?', $this->_me->id)
            ->setHydrationMode(Doctrine::HYDRATE_ARRAY)
            ->execute();

        $this->view->byStatus = Doctrine_Query::create()
            ->select('COUNT(id) as count, denormalizedstatus as criteria')
            ->from('Finding f')
            ->groupBy('f.denormalizedstatus')
            ->where('f.deleted_at is NULL AND f.status <> ?', 'CLOSED')
            ->andWhereIn('f.responsibleorganizationid', $myOrgSystemIds)
            ->orWhere('f.status <> ?', 'CLOSED')
            ->andWhere('f.pocId = ?', $this->_me->id)
            ->setHydrationMode(Doctrine::HYDRATE_ARRAY)
            ->execute();
        $emptyFinding = new Finding();
        foreach ($this->view->byStatus as &$status) {
            $status['tooltip'] = "<b>" . $emptyFinding->getLongStatus($status['criteria']) . "</b>";
        }
        usort($this->view->byStatus, function($a, $b){
            $allStatuses = Finding::getAllStatuses();
            return array_search($a['criteria'], $allStatuses) - array_search($b['criteria'], $allStatuses);
        });
        unset($emptyFinding);

        $this->view->byType = Doctrine_Query::create()
            ->select('COUNT(id) as count, type as criteria')
            ->from('Finding f')
            ->groupBy('f.type')
            ->where('f.deleted_at is NULL AND f.status <> ?', 'CLOSED')
            ->andWhereIn('f.responsibleorganizationid', $myOrgSystemIds)
            ->orWhere('f.status <> ?', 'CLOSED')
            ->andWhere('f.pocId = ?', $this->_me->id)
            ->setHydrationMode(Doctrine::HYDRATE_ARRAY)
            ->execute();
        foreach ($this->view->byType as &$type) {
            switch ($type['criteria']) {
                case 'CAP':
                    $type['tooltip'] = "<b>CAP - Corrective Action Plan</b><br/>"
                                     . "<p>A corrective action plan is a mitigation strategy that aims to reduce the "
                                     . "overall risk of a finding by correcting the underlying deficiency.</p>";
                break;
                case 'FP':
                    $type['tooltip'] = "<b>FP - False Positive</b><br/>"
                                     . "<p>A false positive is not a true mitigation strategy, per se, but it is a plan"
                                     . " to document that the auditor's finding did not exist as documented on the day "
                                     . "that it was observed. Notice that if a finding was true at the time it was "
                                     . "reported by the auditor but has since become invalid, that is not considered a "
                                     . "false positive.</p>";
                break;
                case 'AR':
                    $type['tooltip'] = "<b>AR - Accepted Risk</b><br/><p>"
                                     . "An accept risk is a mitigation strategy that aims to reduce risk down to an acc"
                                     . "eptable level, then seek official sign-off from the authorizing official.</p>";
                break;
                case 'NONE':
                    $type['tooltip'] = "<b>NONE - No Mitigation Strategy Selected</b>";
                break;
            }
        }

        $this->view->bySource = Doctrine_Query::create()
            ->select('COUNT(f.id) as count, f.sourceid, s.nickname as criteria, ' .
                     'CONCAT("<b>", s.nickname, " - ", s.name, "</b><br/>", s.description) as tooltip')
            ->from('Finding f')
            ->innerJoin('f.Source s')
            ->groupBy('f.sourceid')
            ->where('f.deleted_at is NULL AND f.status <> ?', 'CLOSED')
            ->andWhereIn('f.responsibleorganizationid', $myOrgSystemIds)
            ->orWhere('f.status <> ?', 'CLOSED')
            ->andWhere('f.pocId = ?', $this->_me->id)
            ->setHydrationMode(Doctrine::HYDRATE_ARRAY)
            ->execute();

        $this->view->byPoc = Doctrine_Query::create()
            ->select('COUNT(f.id) as count, f.threatlevel, i.id as icon, o.nickname, f.pocid, u.id, u.displayName')
            ->from('Finding f')
            ->leftJoin('f.PointOfContact u')
            ->leftJoin('u.ReportingOrganization o')
            ->leftJoin('o.OrganizationType ot')
            ->leftJoin('ot.Icon i')
            ->groupBy('f.pocid, f.threatlevel')
            ->where('f.deleted_at is NULL AND f.status <> ?', 'CLOSED')
            ->andWhereIn('f.responsibleorganizationid', $myOrgSystemIds)
            ->orWhere('f.status <> ?', 'CLOSED')
            ->andWhere('f.pocId = ?', $this->_me->id)
            ->setHydrationMode(Doctrine::HYDRATE_ARRAY)
            ->execute();
        $criteria = array();
        foreach ($this->view->byPoc as $index => &$statistic) {
            if (empty($statistic['pocId'])) {
                $statistic['criteria'] = 'Unassigned';
                $statistic['pocId'] = 'empty';
            } else {
                $statistic['criteria'] = $this->view->userInfo(
                    $statistic['PointOfContact']['displayName'],
                    $statistic['PointOfContact']['id']
                );
            }

            $pocid = $statistic['pocId'];
            $threatlevel = $statistic['threatLevel'];
            if (!isset($criteria[$pocid])) {
                $criteria[$pocid] = $index;
                $this->view->byPoc[$index][$threatlevel] = $statistic['count'];
            } else {
                $currentIndex = $criteria[$pocid];
                $this->view->byPoc[$currentIndex][$threatlevel] = $statistic['count'];
                $this->view->byPoc[$currentIndex]['count'] += $statistic['count'];
                unset($this->view->byPoc[$index]);
            }
        }

        $this->view->bySystem = Doctrine_Query::create()
            ->select('COUNT(f.id) as count, o.nickname, f.threatlevel, o.id, o.lft, o.rgt, o.level, o.name as criteria, f.responsibleorganizationid, ot.iconId as icon')
            ->from('Organization o')
            ->leftJoin('o.OrganizationType ot')
            ->leftJoin('o.Findings f')
            ->groupBy('f.threatlevel, o.id')
            ->where('f.deleted_at is NULL AND f.status <> ?', 'CLOSED')
            ->andWhereIn('o.id', $myOrgSystemIds)
            ->orWhere('f.status <> ?', 'CLOSED')
            ->andWhere('f.pocId = ?', $this->_me->id)
            ->setHydrationMode(Doctrine::HYDRATE_ARRAY)
            ->execute();
        foreach ($this->view->bySystem as &$statistic) {
            $count = 0;
            foreach ($statistic['Findings'] as &$finding) {
                $threatLevel = $finding['threatLevel'];
                $statistic[$threatLevel] = $finding['count'];
                $count += $finding['count'];
            }
            $statistic['count'] = $count;

            $statistic['parent'] = Doctrine_Query::create()
                ->select('o.id, o.nickname, i.id as icon')
                ->from('Organization o')
                ->leftJoin('o.OrganizationType ot')
                ->leftJoin('ot.Icon i')
                ->where('o.lft < ?', $statistic['lft'])
                ->andWhere('o.lft < ?', $statistic['rgt'])
                ->andWhere('o.level = ?', $statistic['level'] - 1)
                ->setHydrationMode(Doctrine::HYDRATE_ARRAY)
                ->fetchOne();
            if (empty($statistic['icon'])) { // the OrganizationType "system" doesn't have an icon
                $statistic['icon'] = Doctrine_Query::create()
                    ->select('o.id, s.id, st.iconId as icon')
                    ->from('Organization o')
                    ->leftJoin('o.System s')
                    ->leftJoin('s.SystemType st')
                    ->where('o.id = ?', $statistic['id'])
                    ->setHydrationMode(Doctrine::HYDRATE_ARRAY)
                    ->fetchOne();
                $statistic['icon'] = $statistic['icon']['icon'];
            }
            if (empty($statistic['parent']['icon'])) { // the OrganizationType "system" doesn't have an icon
                $statistic['parent']['icon'] = Doctrine_Query::create()
                    ->select('o.id, s.id, st.iconId as icon')
                    ->from('Organization o')
                    ->leftJoin('o.System s')
                    ->leftJoin('s.SystemType st')
                    ->where('o.id = ?', $statistic['parent']['id'])
                    ->setHydrationMode(Doctrine::HYDRATE_ARRAY)
                    ->fetchOne();
                $statistic['parent']['icon'] = $statistic['parent']['icon']['icon'];
            }
        }
    }

    /**
     * Load the charts in a tab
     *
     * @GETAllowed
     */
    public function chartsAction()
    {
        // Top-left chart - Finding Forecast
        $chartFindForecast =
            new Fisma_Chart(380, 275, 'chartFindForecast',
                    '/finding/dashboard/findingforecast/format/json');
        $chartFindForecast
            ->setTitle('Finding Forecast')
            ->addWidget('dayRangesStatChart', 'Day Ranges:', 'text', '0, 15, 30, 60, 90')
            ->addWidget(
                'forcastThreatLvl',
                'Finding Type:',
                'combo',
                'High, Moderate, and Low',
                $this->_threatLevels
            )
            ->addWidget(
                'forecastThreatType',
                'Risk Type:',
                'combo',
                'Threat Level',
                array_values($this->_threatTypes)
            );

        $this->view->chartFindForecast = $chartFindForecast->export();

        // Top-right chart - Findings Past Due
        $chartOverdueFinding =
            new Fisma_Chart(380, 275, 'chartOverdueFinding', '/finding/dashboard/chartoverdue/format/json');
        $chartOverdueFinding
            ->setTitle('Findings Past Due')
            ->addWidget('dayRanges', 'Day Ranges:', 'text', '1, 30, 60, 90, 120')
            ->addWidget(
                'pastThreatLvl',
                'Finding Type:',
                'combo',
                'High, Moderate, and Low',
                $this->_threatLevels
            )
            ->addWidget(
                'pastThreatType',
                'Risk Type:',
                'combo',
                'Threat Level',
                array_values($this->_threatTypes)
            );
        $this->view->chartOverdueFinding = $chartOverdueFinding->export();

        // Mid-left chart - Findings by Worklow Process
        $chartTotalStatus
            = new Fisma_Chart(420, 275, 'chartTotalStatus', '/dashboard/chart-finding/format/json');
        $chartTotalStatus
            ->setTitle('Findings by Workflow Process')
            ->addWidget(
                'findingType',
                'Finding Type:',
                'combo',
                'High, Moderate, and Low',
                $this->_threatLevels
            )
            ->addWidget(
                'workflowThreatType',
                'Risk Type:',
                'combo',
                'Threat Level',
                array_values($this->_threatTypes)
            );

        $this->view->chartTotalStatus = $chartTotalStatus->export();

        // Mid-right chart - Findings Without Corrective Actions
        $chartNoMit = new Fisma_Chart(380, 275);
        $chartNoMit
            ->setTitle('Findings Without Corrective Actions')
            ->setUniqueid('chartNoMit')
            ->setExternalSource('/finding/dashboard/chartfindnomitstrat/format/json')
            ->addWidget('dayRangesMitChart', 'Day Ranges:', 'text', '1, 30, 60, 90, 120')
            ->addWidget(
                'noMitThreatLvl',
                'Finding Type:',
                'combo',
                'High, Moderate, and Low',
                $this->_threatLevels
            )
            ->addWidget(
                'withoutCorrectiveActionThreatType',
                'Risk Type:',
                'combo',
                'Threat Level',
                array_values($this->_threatTypes)
            );
        $this->view->chartNoMit = $chartNoMit->export();

        // Bottom-Upper chart - Open Findings By Organization
        $orgChartFilterList = $this->_getOrgChartFilterList();

        $defaultValues = array_keys($orgChartFilterList);

        $findingOrgChart = new Fisma_Chart(400, 275, 'findingOrgChart');
        $findingOrgChart
            ->setTitle('Open Findings By Organization')
            ->setExternalSource('/finding/dashboard/chartfindingbyorgdetail/format/json')
            ->addWidget(
                    'displayBy',
                    'Display By:',
                    'combo',
                    $defaultValues[0],
                    $orgChartFilterList,
                    true
                )
            ->addWidget(
                'threatLevel',
                'Finding Type:',
                'combo',
                'Totals',
                $this->_threatLevels
            )
            ->addWidget(
                'orgThreatType',
                'Risk Type:',
                'combo',
                'Threat Level',
                array_values($this->_threatTypes)
            );

        $this->view->findingOrgChart = $findingOrgChart->export();

        // Bottom-Bottom chart - Current Security Control Deficiencies
        $securityFamilies = $this->_getSecurityControleFamilies();
        foreach ($securityFamilies as &$familyName) {
            $familyName = 'Family: ' . $familyName;
        }
        array_unshift($securityFamilies, 'Family Summary');
        $controlDeficienciesChart = new Fisma_Chart();
        $controlDeficienciesChart
            ->setTitle('Current Security Control Deficiencies')
            ->setUniqueid('chartSecurityControlDeficiencies')
            ->setWidth(800)
            ->setHeight(275)
            ->setChartType('bar')
            ->setExternalSource('/security-control-chart/control-deficiencies/format/json')
            ->setAlign('center')
            ->addWidget(
                    'displaySecurityBy',
                    'Display By:',
                    'combo',
                    'Family Summary',
                    $securityFamilies
                    );

        $this->view->controlDeficienciesChart = $controlDeficienciesChart->export();
    }

    /**
     * Gets a list of all Security Controle Families that have
     * findings associated with them, and can be seen from the
     * current user (ACL).
     *
     * @return array
     */
    private function _getSecurityControleFamilies()
    {
        // Dont query if there are no organizations this user can see
        if (empty($this->_visibleOrgs)) {
            return array();
        }

        $families = Doctrine_Query::create()
            ->select('SUBSTRING_INDEX(sc.code, "-", 1) fam')
            ->from('SecurityControl sc')
            ->innerJoin('sc.Findings f')
            ->innerJoin('f.Organization o')
            ->andWhere('f.status <> ?', 'CLOSED')
            ->whereIn('o.id', $this->_visibleOrgs)
            ->groupBy('fam')
            ->orderBy('fam')
            ->setHydrationMode(Doctrine::HYDRATE_SCALAR)
            ->execute();

        $familyArray = array();
        foreach ($families as $famResult)
            $familyArray[] = $famResult['sc_fam'];

        return $familyArray;
    }

    /**
     * Calculate Organization statistics based on params.
     * Params expected by $this->getRequest()->getParam(...)
     * Expected params: displayBy
     * Returns exported Fisma_Chart
     *
     * @GETAllowed
     * @return array
     */
    public function chartfindingbyorgdetailAction()
    {
        $displayBy = urldecode($this->getRequest()->getParam('displayBy'));
        $displayBy = strtolower($displayBy);

        $threatLevel = urldecode($this->getRequest()->getParam('threatLevel'));
        $threatLevel = strtolower($threatLevel);

        $threatType = $this->getRequest()->getparam('orgThreatType');
        $threatField = $threatType === 'Threat Level' ? 'threatLevel' : 'residualRisk';

        $rtnChart = new Fisma_Chart();
        $rtnChart
            ->setThreatLegendVisibility(true)
            ->setThreatLegendTitle($threatType)
            ->setThreatLegendWidth(450)
            ->setAxisLabelY('Number of Findings')
            ->setChartType('stackedbar')
            ->setColors($this->_highModLowColors)
            ->setLayerLabels(
                    array(
                        'Null',
                        'High',
                        'Moderate',
                        'Low'
                        )
                    );

        // Dont query if there are no organizations this user can see
        if (empty($this->_visibleOrgs)) {
            $this->view->chart = $rtnChart->export('array');
            return;
        }

        $basicLink =
            '/finding/remediation/list?q=' .
            '/denormalizedStatus/enumIsNot/CLOSED' .
            '/organization/organizationSubtree/';

        if ($displayBy === 'system') {
            $systemCounts = Doctrine_Query::create()
                ->select('parent.id, parent.nickname, parent.name')
                ->addSelect("SUM(IF(finding.id IS NOT NULL AND finding.' . $threatField . ' IS NULL, 1, 0)) isnull")
                ->addSelect("SUM(IF(finding.' . $threatField . ' = 'LOW', 1, 0)) low")
                ->addSelect("SUM(IF(finding.' . $threatField . ' = 'MODERATE', 1, 0)) moderate")
                ->addSelect("SUM(IF(finding.' . $threatField . ' = 'HIGH', 1, 0)) high")
                ->from('Organization parent')
                ->leftJoin('parent.System system')
                ->leftJoin('Organization node')
                ->leftJoin("node.Findings finding WITH finding.status <> 'CLOSED'")
                ->leftJoin('node.System nodeSystem')
                ->where('node.lft BETWEEN parent.lft and parent.rgt')
                ->andWhere('nodeSystem.sdlcPhase <> ?', array('disposal'))
                ->andWhere('system.sdlcPhase <> ?', array('disposal'))
                ->andWhereIn('parent.id', $this->_visibleOrgs)
                ->groupBy('parent.nickname')
                ->orderBy('parent.nickname')
                ->having('SUM(IF(finding.id IS NOT NULL, 1, 0)) > 0')
                ->setHydrationMode(Doctrine::HYDRATE_SCALAR)
                ->execute();

            foreach ($systemCounts as $systemCountInfo) {
                $orgName = $systemCountInfo['parent_nickname'];
                $rtnChart->addColumn(
                    $orgName,
                    array(
                        $systemCountInfo['finding_isnull'],
                        $systemCountInfo['finding_high'],
                        $systemCountInfo['finding_moderate'],
                        $systemCountInfo['finding_low']
                    ),
                    array(
                        '',
                        $basicLink . '#ColumnLabel#/' . $threatField . '/enumIs/HIGH',
                        $basicLink . '#ColumnLabel#/' . $threatField . '/enumIs/MODERATE',
                        $basicLink . '#ColumnLabel#/' . $threatField . '/enumIs/LOW'
                    ),
                    $systemCountInfo['parent_name'] . '<hr/>#columnReport#'
                );
            }
        } else {

            // Get a list of requested organization-parent types (Agency-organizations, Bureau-organizations, gss, etc)
            $parents = $this->_getOrganizationsByOrgType($displayBy);

            // For each parent (foreach agency, or bBureau, etc)
            foreach ($parents as $thisParentOrg) {

                $childrenTotaled = $this->_getSumsOfOrgChildren($thisParentOrg['id'], $threatField);

                // Do not use association, high/mod/low is defined on the chart with Fisma_Chart->setLayerLabels()
                $childrenTotaled = array_values($childrenTotaled);

                $rtnChart->addColumn(
                        $thisParentOrg['nickname'],
                        $childrenTotaled,
                        array(
                            '',
                            $basicLink . '#ColumnLabel#/' . $threatField . '/enumIs/HIGH',
                            $basicLink . '#ColumnLabel#/' . $threatField . '/enumIs/MODERATE',
                            $basicLink . '#ColumnLabel#/' . $threatField . '/enumIs/LOW'
                            ),
                        $thisParentOrg['name'] . '<hr/>#columnReport#'
                        );

            }
        }

        switch ($threatLevel) {

            case 'high, moderate, and low':
                // Remove null-count layer/stack in this stacked bar chart
                $rtnChart->deleteLayer(0);
                break;

            case 'totals':
                $rtnChart
                    ->convertFromStackedToRegular()
                    ->setColors(array(self::COLOR_BLUE))
                    ->setThreatLegendVisibility(false)
                    ->setLinks(
                            '/finding/remediation/list?q=' .
                            '/denormalizedStatus/enumIsNot/CLOSED' .
                            '/organization/organizationSubtree/#ColumnLabel#'
                            );

                break;
            case 'high':
                // Remove null-count layer/stack in this stacked bar chart
                $rtnChart->deleteLayer(0);

                $rtnChart
                    ->deleteLayer(2)
                    ->deleteLayer(1)
                    ->setColors(array(self::COLOR_HIGH));
                break;
            case 'moderate':
                // Remove null-count layer/stack in this stacked bar chart
                $rtnChart->deleteLayer(0);

                $rtnChart
                    ->deleteLayer(2)
                    ->deleteLayer(0)
                    ->setColors(array(self::COLOR_MODERATE));
                break;
                case 'low';
                // Remove null-count layer/stack in this stacked bar chart
                $rtnChart->deleteLayer(0);

                $rtnChart
                    ->deleteLayer(1)
                    ->deleteLayer(0)
                    ->setColors(array(self::COLOR_LOW));
                    break;
        }

        // The context switch will turn this array into a json reply (the responce to the external source)
        $this->view->chart = $rtnChart->export('array');
    }

    /**
     * Computes the sums of HIGH/MODERATE/LOW/NULL of all children reported from _getAllChildrenOfOrg($orgId)
     *
     * @return array
     */
    private function _getSumsOfOrgChildren($orgId, $threatField)
    {

        // Get all children of the given organization id
        $childList = $this->_getAllChildrenOfOrg($orgId, $threatField);

        $totalNull = 0;
        $totalHigh = 0;
        $totalMod = 0;
        $totalLow = 0;

        // For each organization (that is a child of $orgId)
        foreach ($childList as $thisChildOrg) {

            // For each threat level total (of findings) of this organization (high.mod,low)
            foreach ($thisChildOrg['Findings'] as $thisThreatLvl) {

                switch ($thisThreatLvl[$threatField]) {
                    case 'HIGH':
                        $totalHigh += $thisThreatLvl['COUNT'];
                        break;
                    case 'MODERATE':
                        $totalMod += $thisThreatLvl['COUNT'];
                        break;
                    case 'LOW':
                        $totalLow += $thisThreatLvl['COUNT'];
                        break;
                    case NULL:
                        $totalNull += $thisThreatLvl['COUNT'];
                        break;
                    case '':
                        $totalNull += $thisThreatLvl['COUNT'];
                        break;
                }

            }

        }

        return array('NULL' => $totalNull, 'HIGH' => $totalHigh, 'MODERATE' => $totalMod, 'LOW' => $totalLow);
    }

    /**
     * Gets a list of organizations that are children of the given organization id, and
     * the count of their findings associated with them (seperate by threat level)
     * returns an array strict of
     * array(
     *   'id'       => this organization id
     *   'nickname' => Organization nickname
     *   'Findings' =>
     *      array(
     *          array(
     *              'threatLevel' => LOW/MODERATE/HIGH
     *              'COUNT' => Number of findings with this threatLevel and in this org
     *          )
     *      )
     *  )
     *
     * @return array
     */
    private function _getAllChildrenOfOrg($orgId, $threatField)
    {
        // Dont query if there are no organizations this user can see
        if (empty($this->_visibleOrgs)) {
            return array();
        }

        // get the left and right nodes (lft and rgt) of the target system from the system table
        $q = Doctrine_Query::create();
        $q
            ->addSelect('lft, rgt')
            ->from('Organization o')
            ->where('id = ?', $orgId)
            ->setHydrationMode(Doctrine::HYDRATE_ARRAY);
        $row = $q->execute();
        $row = $row[0];     // we are only expecting 1 row result
        $parLft = $row['lft'];
        $parRgt = $row['rgt'];

        $q = Doctrine_Query::create();
        $q
            ->addSelect('COUNT(f.id), o.id, o.nickname, f.' . $threatField)
            ->from('Organization o')
            ->leftJoin('o.Findings f')
            ->where('f.responsibleorganizationid=o.id')
            ->whereIn('f.responsibleOrganizationId', $this->_visibleOrgs)
            ->andWhere("? < o.lft", $parLft)
            ->andWhere('f.status <> "CLOSED"')
            ->andWhere("? > o.rgt", $parRgt)
            ->groupBy('o.nickname, f.' . $threatField)
            ->setHydrationMode(Doctrine::HYDRATE_ARRAY);
        $rtn = $q->execute();

        if (array_search($orgId, $this->_visibleOrgs) !== false) {

            $q = Doctrine_Query::create();
            $q
                ->addSelect('COUNT(f.id), o.id, o.nickname, f.' . $threatField)
                ->from('Organization o')
                ->leftJoin('o.Findings f')
                ->whereIn('f.responsibleorganizationid', $this->_visibleOrgs)
                ->where('o.id = ?', $orgId)
                ->andWhere('f.status <> "CLOSED"')
                ->groupBy('o.nickname, f.' . $threatField)
                ->setHydrationMode(Doctrine::HYDRATE_ARRAY);

            $rtn = array_merge($rtn, $q->execute());
        }

        return $rtn;
    }

    /**
     * Gets a list of organizations that are at the level given
     * This is usefull for obtaining Agency and Bureau IDs
     * Returns array('id','nickname','name') for each result in an array
     *
     * @return array
     */
    private function _getOrganizationsByOrgType($orgType)
    {
        $typeList = $this->_getOrgChartFilterList();
        $orgType = $typeList[(int)$orgType];

        $q = Doctrine_Query::create();
        $q->addSelect('o.id, o.nickname, o.name')
          ->from('Organization o')
          ->leftJoin('o.OrganizationType ot')
          ->leftJoin('o.System s')
          ->leftJoin('s.SystemType st')
          ->where('(ot.name = ? OR st.name = ?)', array($orgType, $orgType))
          ->whereIn('o.id ', $this->_visibleOrgs)
          ->setHydrationMode(Doctrine::HYDRATE_ARRAY)
          ->orderBy('o.nickname');

        return $q->execute();
    }

    /**
     * @GETAllowed
     */
    public function chartoverdueAction()
    {
        $dayRanges = str_replace(' ', '', urldecode($this->getRequest()->getParam('dayRanges')));
        $dayRanges = explode(',', $dayRanges);
        $dayRanges[] = 365 * 10;    // The last ##+ column

        $findingType = urldecode($this->getRequest()->getParam('pastThreatLvl'));

        $threatType = $this->getRequest()->getParam('pastThreatType', 'Threat Level');
        $threatField = $threatType === 'Threat Level' ? 'threatLevel' : 'residualRisk';

        $thisChart = new Fisma_Chart();
        $thisChart
            ->setChartType('stackedbar')
            ->setConcatColumnLabels(false)
            ->setAxisLabelX('Number of Days Past Due')
            ->setAxisLabelY('Number of Findings')
            ->setColumnLabelAngle(0)
            ->setThreatLegendVisibility(true)
            ->setThreatLegendTitle($threatType)
            ->setColors($this->_highModLowColors)
            ->setLayerLabels(
                    array(
                        'Null',
                        'High',
                        'Moderate',
                        'Low'
                        )
                    );

        // Dont query if there are no organizations this user can see
        if (empty($this->_visibleOrgs)) {
            $this->view->chart = $thisChart->export('array');
            return;
        }

        $nonStackedLinks = array();

        // Get counts in between the day ranges given
        for ($x = 0; $x < count($dayRanges) - 1; $x++) {

            $toDayDiff = $dayRanges[$x];
            $toDay = new Zend_Date();
            $toDay->subDay($toDayDiff);
            $toDayStr = $toDay->toString(Fisma_Date::FORMAT_DATE);

            $fromDayDiff = $dayRanges[$x+1] - 1;
            $fromDay = new Zend_Date();
            $fromDay->subDay($fromDayDiff);
            $fromDayStr = $fromDay->toString(Fisma_Date::FORMAT_DATE);

            $q = Doctrine_Query::create();
            $q
                ->addSelect($threatField . ' threat, COUNT(f.id)')
                ->from('Finding f')
                ->where('f.currentecd BETWEEN "' . $fromDayStr . '" AND "' . $toDayStr . '"')
                ->andWhere('f.status <> "CLOSED"')
                ->whereIn('f.responsibleOrganizationId ', FindingTable::getOrganizationIds())
                ->groupBy($threatField)
                ->setHydrationMode(Doctrine::HYDRATE_ARRAY);
            $rslts = $q->execute();

            // We will get three results, each for a count of High Mod, Low
            $thisNull = 0;
            $thisHigh = 0;
            $thisMod = 0;
            $thisLow = 0;
            foreach ($rslts as $thisRslt) {
                switch ($thisRslt['threat']) {
                    case "LOW":
                        $thisLow = $thisRslt['COUNT'];
                        break;
                    case "MODERATE":
                        $thisMod = $thisRslt['COUNT'];
                        break;
                    case "HIGH":
                        $thisHigh = $thisRslt['COUNT'];
                        break;
                    case NULL:
                        $thisNull += $thisRslt['COUNT'];
                        break;
                    case '':
                        $thisNull += $thisRslt['COUNT'];
                        break;
                }
            }

            if ($x === count($dayRanges) - 2) {
                $thisColLabel = $dayRanges[$x] . '+';
            } else {
                $thisColLabel = $toDayDiff . '-' . $fromDayDiff;
            }

            // The links to associate with entire columns when this is not a stacked bar chart
            $nonStackedLinks[] = '/finding/remediation/list?q=' .
                '/denormalizedStatus/enumIsNot/CLOSED' .
                '/currentEcd/dateBetween/' . $fromDayStr . '/' . $toDayStr;

            $linkPrefix = '/finding/remediation/list?q=/denormalizedStatus/enumIsNot/CLOSED'
                        . '/currentEcd/dateBetween/' . $fromDayStr . '/' . $toDayStr
                        . '/' . $threatField . '/enumIs/';
            $thisChart->addColumn(
                $thisColLabel,
                array(
                    $thisNull,
                    $thisHigh,
                    $thisMod,
                    $thisLow
                ),
                array('',
                    $linkPrefix . 'HIGH',
                    $linkPrefix . 'MODERATE',
                    $linkPrefix . 'LOW'
                )
            );
        }

        // What should we filter/show on the chart? Totals? Migh,Mod,Low? etc...

        switch (strtolower($findingType)) {
            case "totals":
                // Crunch numbers
                $thisChart
                    ->convertFromStackedToRegular()
                    ->setThreatLegendVisibility(false)
                    ->setColors(array(self::COLOR_BLUE))
                    ->setLinks($nonStackedLinks);
                break;
            case "high, moderate, and low":
                // Remove null-count layer
                $thisChart->deleteLayer(0);
                break;
            case "high":
                // Remove null-count layer
                $thisChart->deleteLayer(0);
                // Remove the Low and Moderate columns/layers
                $thisChart->deleteLayer(2);
                $thisChart->deleteLayer(1);
                $thisChart->setColors(array(self::COLOR_HIGH));
                break;
            case "moderate":
                // Remove null-count layer
                $thisChart->deleteLayer(0);
                // Remove the Low and High columns/layers
                $thisChart->deleteLayer(2);
                $thisChart->deleteLayer(0);
                $thisChart->setColors(array(self::COLOR_MODERATE));
                break;
            case "low":
                // Remove null-count layer
                $thisChart->deleteLayer(0);
                // Remove the Moderate and High columns/layers
                $thisChart->deleteLayer(1);
                $thisChart->deleteLayer(0);
                $thisChart->setColors(array(self::COLOR_LOW));
                break;
        }

        $this->view->chart = $thisChart->export('array');
    }

    /**
     * Calculate "finding forcast" data for a chart based on finding.currentecd in the database
     *
     * @GETAllowed
     * @return void
     */
    public function chartfindnomitstratAction()
    {
        $dayRange = $this->getRequest()->getParam('dayRangesMitChart');
        $dayRange = str_replace(' ', '', $dayRange);
        $dayRange = explode(',', $dayRange);

        $threatLvl = $this->getRequest()->getParam('noMitThreatLvl');
        $threatType = $this->getRequest()->getParam('withoutCorrectiveActionThreatType');
        $threatField = $threatType === 'Threat Level' ? 'threatLevel' : 'residualRisk';

        $noMitChart = new Fisma_Chart();
        $noMitChart
            ->setAxisLabelX('Number of Days Without Mitigation Strategy')
            ->setAxisLabelY('Number of Findings')
            ->setChartType('stackedbar')
            ->setThreatLegendVisibility(true)
            ->setThreatLegendTitle($threatType)
            ->setColumnLabelAngle(0)
            ->setColors(
                    array(
                        self::COLOR_HIGH,
                        self::COLOR_MODERATE,
                        self::COLOR_LOW
                        )
                    )
            ->setConcatColumnLabels(false)
            ->setLayerLabels(
                    array(
                        'Null',
                        'High',
                        'Moderate',
                        'Low'
                        )
                    );

        // Dont query if there are no organizations this user can see
        if (empty($this->_visibleOrgs)) {
            $this->view->chart = $noMitChart->export('array');
            return;
        }

        $nonStackedLinks = array();

        for ($x = 0; $x < count($dayRange) - 1; $x++) {

            $fromDayInt = $dayRange[$x+1] - 1;
            $fromDay = new Zend_Date();
            $fromDay = $fromDay->addDay(-$fromDayInt);
            $fromDayStr = $fromDay->toString(Fisma_Date::FORMAT_DATE);

            /**
             * Since the createdts column is timestamp type. It needs to add one extra day to the first label so that
             * the finding created yesterday can be searched.
             */
            if ( $x == 0 ) {
                $toDayInt = $dayRange[$x] - 1;
                $thisColumnLabel = $dayRange[$x]  . '-' . $fromDayInt;
            } else {
                $toDayInt = $dayRange[$x];
                $thisColumnLabel = $toDayInt . '-' . $fromDayInt;
            }

            $toDay = new Zend_Date();
            $toDay = $toDay->addDay(-$toDayInt);
            $toDayStr = $toDay->toString(Fisma_Date::FORMAT_DATE);

            if ($x !== count($dayRange) - 2) {
                $fromDay->addday(-1);
                $fromDayStr = $fromDay->toString(Fisma_Date::FORMAT_DATE);
            }

            // Get the count of findings
            $q = Doctrine_Query::create()
                ->select('count(f.id), f.' . $threatField)
                ->from('Finding f')
                ->where('f.status="NEW" OR f.status="DRAFT"')
                ->andWhere('f.status <> "CLOSED"')
                ->andWhere('f.createdts BETWEEN "' . $fromDayStr . '" AND "' . $toDayStr . '"')
                ->groupBy('f.' . $threatField)
                ->whereIn('f.responsibleOrganizationId ', FindingTable::getOrganizationIds())
                ->setHydrationMode(Doctrine::HYDRATE_ARRAY);
            $rslts = $q->execute();

            // Initalize to 0 (query may not return values for 0 counts)
            $thisNull = 0;
            $thisHigh = 0;
            $thisMod = 0;
            $thisLow = 0;

            foreach ($rslts as $thisLevel) {
                switch ($thisLevel[$threatField]) {
                    case 'LOW':
                        $thisLow = $thisLevel['count'];
                        break;
                    case 'MODERATE':
                        $thisMod = $thisLevel['count'];
                        break;
                    case 'HIGH':
                        $thisHigh = $thisLevel['count'];
                        break;
                    case NULL:
                        $thisNull += $thisRslt['COUNT'];
                        break;
                    default:
                        $thisNull += $thisRslt['COUNT'];
                        break;
                }
            }

            // Make URL to the search page with date params
            $basicSearchLink = '/finding/remediation/list?q=' .
                '/createdTs/dateBetween/' . $fromDayStr . '/' . $toDayStr;

            // Rake this url filter out CLOSED, EN, and anything on evaluation.nickname (MS ISSO, EV ISSO, etc)
            $basicSearchLink .= '/denormalizedStatus/enumIsNot/CLOSED';
            $basicSearchLink .= '/denormalizedStatus/enumIsNot/EN';
            foreach ($this->_getEvaluationNames() as $thisStatus) {
                $basicSearchLink .= '/denormalizedStatus/enumIsNot/' . $thisStatus;
            }

            // Remembers links for a non-stacked bar chart in the even the user is querying "totals"
            $nonStackedLinks[] = $basicSearchLink;

            $noMitChart->addColumn(
                    $thisColumnLabel,
                    array(
                        $thisNull,
                        $thisHigh,
                        $thisMod,
                        $thisLow
                        ),
                    array(
                        $basicSearchLink . '/' . $threatField . '/enumIs/NULL',
                        $basicSearchLink . '/' . $threatField . '/enumIs/HIGH',
                        $basicSearchLink . '/' . $threatField . '/enumIs/MODERATE',
                        $basicSearchLink . '/' . $threatField . '/enumIs/LOW'
                        )
                    );

        }

        // Show, hide and filter data on the chart as requested
        switch (strtolower($threatLvl)) {
            case "totals":
                // Remove the nullCount layer
                $noMitChart->deleteLayer(0);
                // Crunch numbers
                $noMitChart
                    ->convertFromStackedToRegular()
                    ->setThreatLegendVisibility(false)
                    ->setColors(array(self::COLOR_BLUE))
                    ->setLinks($nonStackedLinks);
                break;
            case "high, moderate, and low":
                // $noMitChart is already in this form
                // Remove null-counts (findings without threatLevels)
                $noMitChart->deleteLayer(0);
                break;
            case "high":
                // Remove null-counts (findings without threatLevels)
                $noMitChart->deleteLayer(0);
                // Remove the Low and Moderate columns/layers
                $noMitChart->deleteLayer(2);
                $noMitChart->deleteLayer(1);
                $noMitChart->setColors(array(self::COLOR_HIGH));
                break;
            case "moderate":
                // Remove null-counts (findings without threatLevels)
                $noMitChart->deleteLayer(0);
                // Remove the Low and High columns/layers
                $noMitChart->deleteLayer(2);
                $noMitChart->deleteLayer(0);
                $noMitChart->setColors(array(self::COLOR_MODERATE));
                break;
            case "low":
                // Remove null-counts (findings without threatLevels)
                $noMitChart->deleteLayer(0);
                // Remove the Moderate and High columns/layers
                $noMitChart->deleteLayer(1);
                $noMitChart->deleteLayer(0);
                $noMitChart->setColors(array(self::COLOR_LOW));
                break;
        }

        // Export as array, the context switch will translate it to a JSON responce
        $this->view->chart = $noMitChart->export('array');
    }

    /**
     * Gets all nicknames from the evaluation table
     *
     * @return array
     */
    private function _getEvaluationNames()
    {
        $q = Doctrine_Query::create()
            ->select('nickname')
            ->from('Evaluation e')
            ->setHydrationMode(Doctrine::HYDRATE_ARRAY);
        $results = $q->execute();

        $rtn = array();
        foreach ($results as $thisEval) {
            $rtn[] = $thisEval['nickname'];
        }

        return $rtn;
    }

    /**
     * Calculate "finding forcast" data for a chart based on finding.currentecd in the database
     *
     * @GETAllowed
     * @return void
     */
    public function findingforecastAction()
    {

        $dayRange = $this->getRequest()->getParam('dayRangesStatChart');
        $dayRange = str_replace(' ', '', $dayRange);
        $dayRange = explode(',', $dayRange);

        $threatLvl = $this->getRequest()->getParam('forcastThreatLvl');
        $threatType = $this->getRequest()->getParam('forecastThreatType');
        $threatField = $threatType === 'Threat Level' ? 'threatLevel' : 'residualRisk';

        $highCount = 0;
        $modCount = 0;
        $lowCount = 0;
        $nullCount = 0;
        $chartDataText = array();
        $totalChartLinks = array();

        $thisChart = new Fisma_Chart();
        $thisChart
            ->setChartType('stackedbar')
            ->setConcatColumnLabels(false)
            ->setColumnLabelAngle(0)
            ->setThreatLegendVisibility(true)
            ->setThreatLegendTitle($threatType)
            ->setAxisLabelX('Number of Days Until Overdue')
            ->setAxisLabelY('Number of Findings')
            ->setLayerLabels(
                    array(
                        'Null',
                        'High',
                        'Moderate',
                        'Low'
                        )
                    )
            ->setColors($this->_highModLowColors);

        // Dont query if there are no organizations this user can see
        if (empty($this->_visibleOrgs)) {
            $this->view->chart = $thisChart->export('array');
            return;
        }

        for ($x = 0; $x < count($dayRange) - 1; $x++) {

            $fromDay = new Zend_Date();
            $fromDay = $fromDay->addDay($dayRange[$x]);
            $fromDayStr = $fromDay->toString(Fisma_Date::FORMAT_DATE);

            $toDay = new Zend_Date();
            $toDay = $toDay->addDay($dayRange[$x+1]);

            if ($x === count($dayRange) - 2) {
                $thisColumnLabel = $dayRange[$x] . '-' . $dayRange[$x + 1];
            } else {
                $toDay->addDay(-1);
                $thisColumnLabel = $dayRange[$x] . '-' . ( $dayRange[$x + 1] - 1 );
            }

            $toDayStr = $toDay->toString(Fisma_Date::FORMAT_DATE);

            // Get the count of High,Mod,Low findings
            $q = Doctrine_Query::create()
                ->select('COUNT(f.id), f.' . $threatField)
                ->from('Finding f')
                ->where('f.currentecd BETWEEN "' . $fromDayStr . '" AND "' . $toDayStr . '"')
                ->andWhere('f.status <> "CLOSED"')
                ->whereIn('f.responsibleOrganizationId ', FindingTable::getOrganizationIds())
                ->groupBy('f.' . $threatField)
                ->setHydrationMode(Doctrine::HYDRATE_ARRAY);

            $results = $q->execute();
            $this->view->rtn = $results;

            $highCount = $modCount = $lowCount = 0;
            foreach ($results as $thisRslt) {
                switch ($thisRslt[$threatField]) {
                    case 'HIGH':
                        $highCount = $thisRslt['COUNT'];
                        break;
                    case 'MODERATE':
                        $modCount = $thisRslt['COUNT'];
                        break;
                    case 'LOW':
                        $lowCount = $thisRslt['COUNT'];
                        break;
                    case NULL:
                        $nullCount += $thisRslt['COUNT'];
                        break;
                    case '':
                        $nullCount += $thisRslt['COUNT'];
                        break;
                }
            }

            // Add column assuming this is a stacked-bar chart with High, Mod, and Low findings
            $linkPrefix = '/finding/remediation/list?q=/denormalizedStatus/enumIsNot/CLOSED'
                        . '/currentEcd/dateBetween/'
                        . $fromDay->toString(Fisma_Date::FORMAT_DATE).'/'.$toDay->toString(Fisma_Date::FORMAT_DATE)
                        . '/' . $threatField . '/enumIs/';
            $thisChart
                ->addColumn(
                        $thisColumnLabel,
                        array(
                            $nullCount,
                            $highCount,
                            $modCount,
                            $lowCount
                            ),
                        array('',
                            $linkPrefix . 'HIGH',
                            $linkPrefix . 'MODERATE',
                            $linkPrefix . 'LOW'
                            )
                            );

            // Note the links to set in the even this is a totals (basic-bar) chart
            $totalChartLinks[] = '/finding/remediation/list?q=' .
                '/denormalizedStatus/enumIsNot/CLOSED' .
                '/currentEcd/dateBetween/' . $fromDay->toString(Fisma_Date::FORMAT_DATE) . '/'
                . $toDay->toString(Fisma_Date::FORMAT_DATE);
        }

        // Show, hide and filter chart data as requested
        switch (strtolower($threatLvl)) {
            case "totals":
                // Crunch numbers
                $thisChart
                    ->convertFromStackedToRegular()
                    ->setLinks($totalChartLinks)
                    ->setThreatLegendVisibility(false)
                    ->setColors(array(self::COLOR_BLUE));
                break;
            case "high, moderate, and low":
                // Remove the nullCount layer
                $thisChart->deleteLayer(0);
                break;
            case "high":
                // Remove the nullCount layer
                $thisChart->deleteLayer(0);
                // Remove the Low and Moderate columns/layers
                $thisChart->deleteLayer(2);
                $thisChart->deleteLayer(1);
                $thisChart->setColors(array(self::COLOR_HIGH));
                break;
            case "moderate":
                // Remove the nullCount layer
                $thisChart->deleteLayer(0);
                // Remove the Low and High columns/layers
                $thisChart->deleteLayer(2);
                $thisChart->deleteLayer(0);
                $thisChart->setColors(array(self::COLOR_MODERATE));
                break;
            case "low":
                // Remove the nullCount layer
                $thisChart->deleteLayer(0);
                // Remove the Moderate and High columns/layers
                $thisChart->deleteLayer(1);
                $thisChart->deleteLayer(0);
                $thisChart->setColors(array(self::COLOR_LOW));
                break;
        }

        // Export as array, the context switch will translate it to a JSON responce
        $this->view->chart = $thisChart->export('array');
    }

    /**
     * Return a nested array of organization types and system types for filtering the organization charts.
     *
     * @return array
     */
    private function _getOrgChartFilterList()
    {
        return array_merge(
            Doctrine::getTable('OrganizationType')->getOrganizationTypeArray(),
            Doctrine::getTable('SystemType')->getTypeList()
        );
    }
}

