<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

require_once('../lib/Template.config.php');

require_once('../lib/Chart.class.php');
require_once('../lib/PoamList.class.php');


// --------------------------------------------------------------------
// 
// FUNCTIONS
// 
// --------------------------------------------------------------------


function getTypeCounts($pl) {

	// reset our poam list handler
	$pl->reset();
	$pl->getPoamId();

	// create our type array and reset
	$type = array();

	// type NONE
	$pl->filterPoamType('NONE');
	$type['NONE'] = $pl->getListSize(); 

	// type CAP
	$pl->filterPoamType('CAP');
	$type['CAP'] = $pl->getListSize();

	// type AR
	$pl->filterPoamType('AR');
	$type['AR'] = $pl->getListSize();

	// type FP
	$pl->filterPoamType('FP');
	$type['FP'] = $pl->getListSize();
	
	// return our values
	return $type;
	
} // getTypeCounts()


function getStatusCounts($pl, $_NOW) {

	// create our status array
	$status = array();

	// NEW
	$pl->reset();
	$pl->getPoamId();
	$pl->filterPoamType('NONE');
	$status['NEW'] = $pl->getListSize(); 

	// OPEN
	$pl->reset();
	$pl->getPoamId();
	$pl->filterPoamStatus('OPEN');
	$status['OPEN'] = $pl->getListSize();

	// EN
	$pl->reset();
	$pl->getPoamId();
	$pl->filterPoamStatus('EN');
	$pl->filterPoamActionDateEst($_NOW, '<');
	$status['EN'] = $pl->getListSize();

	// EO
	$pl->reset();
	$pl->getPoamId();
	$pl->filterPoamStatus('EN');
	$pl->filterPoamActionDateEst($_NOW, '>=');
	$status['EO'] = $pl->getListSize();
	
	// EP
	$pl->reset();
	$pl->getPoamId();
	$pl->filterPoamStatus('EP');
	$status['EP'] = $pl->getListSize(); 

	// ES
	$pl->reset();
	$pl->getPoamId();
	$pl->filterPoamStatus('ES');
	$status['ES'] = $pl->getListSize();

	// CLOSED
	$pl->filterPoamStatus('CLOSED');
	$status['CLOSED'] = $pl->getListSize();

	// return our results
	return $status;

} // getStatusCounts()


function getStatusAlerts($status) {

	$messages = array();

	// status alerts
	if ($status['NEW']  > 0) { array_push($messages, 'There are ' . $status['NEW']  . ' items awaiting mitigation strategy and approval '); }
	if ($status['OPEN'] > 0) { array_push($messages, 'There are ' . $status['OPEN'] . ' items awaiting approval'); }
	if ($status['EN']   > 0) { array_push($messages, 'There are ' . $status['EN']   . ' items awaiting evidence'); }
	if ($status['EO']   > 0) { array_push($messages, 'There are ' . $status['EO']   . ' items awaiting overdue evidence'); }

	// return our results
	return $messages;

} // getAlerts()


function getUpdateAlerts($pl) {
	
	$messages = array();
	
	// get our 30+ day alert
	$pl->reset();
	$pl->getPoamDateModified();
	$pl->filterPoamDateModified(date('Y-m-d', strtotime('-30 days')), '<');
	$count = $pl->getListSize();
	if ($count > 0) { array_push($messages, "There are $count items that have not been updated in 30 or more days "); }

	// get our 60+ day alert
	$pl->reset();
	$pl->getPoamDateModified();
	$pl->filterPoamDateModified(date('Y-m-d', strtotime('-60 days')), '<');
	$count = $pl->getListSize();
	if ($count > 0) { array_push($messages, "There are $count items that have not been updated in 60 or more days "); }

	// get our 90+ day alert
	$pl->reset();
	$pl->getPoamDateModified();
	$pl->filterPoamDateModified(date('Y-m-d', strtotime('-90 days')), '<');
	$count = $pl->getListSize();
	if ($count > 0) { array_push($messages, "There are $count items that have not been updated in 90 or more days "); }
	
	// return our results
	return $messages;
	
}


// --------------------------------------------------------------------
// 
// HEADER VALUES
// 
// --------------------------------------------------------------------

$_TEMPLATE->assign('this_page',   'dashboard.php');
$_TEMPLATE->assign('this_title',  'Dashboard');
$_TEMPLATE->assign('menu_header', 'dashboard');


// --------------------------------------------------------------------
// 
// PAGE DISPLAY
// 
// --------------------------------------------------------------------

// load header file
require_once('header.php');
require_once('menu.php');

// create our POAM list
$pl = new PoamList($_DB);

// get our counts
$type   = getTypeCounts($pl);
$status = getStatusCounts($pl, $_NOW);

// set up our alerts
$_TEMPLATE->assign('message_title', 'Update Alerts');
$_TEMPLATE->assign('messages', getUpdateAlerts($pl));
$_TEMPLATE->display('message.tpl');


$_TEMPLATE->assign('message_title', 'Status Alerts');
$_TEMPLATE->assign('messages', getStatusAlerts($status));
$_TEMPLATE->display('message.tpl');

// TODO: at some point move the chart generation into a helper and hook into
//       updates that change statuses (for a drastic performance increase) 

// set up our charts
$_CHART->generatePieChart('chart_pie1.png', 'POA&Ms by TYPE',   $type);
$_CHART->generateBarChart('chart_bar1.png', 'POA&Ms by STATUS', $status);
$_CHART->generatePieChart('chart_pie2.png', 'POA&Ms by STATUS', $status);

// display the page template
$_TEMPLATE->display('dashboard.tpl');

// load footer file
require_once('footer.php');



?>
