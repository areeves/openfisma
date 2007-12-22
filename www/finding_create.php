<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

require_once('../lib/Config.class.php');
require_once('../lib/Finding.class.php');
require_once('../lib/Database.class.php');
//require_once('../lib/Encryption.class.php');	


// --------------------------------------------------------------------
// 
// FORM HANDLING - NO OUTPUT SHOULD OCCUR HERE!
// 
// --------------------------------------------------------------------

// handle the form action
switch ($_POST['form_action']) {
case 'Cancel': 

	// action cancelled
	header("Location: ".$_CONFIG->APP_URL()."finding_list.php");
	break;

case 'Create':

	// create finding if we are referrer
	if ($_POST['referrer'] == 'finding_create.php') {

		// create a new finding instance
		$finding = new Finding($_DB);
	
		// create the finding
		$finding->setSourceId  ($_DB->sanitize($_POST['source_id']));
		$finding->setAssetId  ($_DB->sanitize($_POST['asset_id']));
		$finding->setFindingStatus  ($_DB->sanitize($_POST['finding_status']));
		$finding->setFindingDateCreated  ($_DB->sanitize($_POST['finding_date_created_Year'].'-'.$_POST['finding_date_created_Month'].'-'.$_POST['finding_date_created_Day'].' '.$_POST['finding_date_created_Hour'].':'.$_POST['finding_date_created_Minute'].':'.$_POST['finding_date_created_Second']));
		$finding->setFindingDateDiscovered  ($_DB->sanitize($_POST['finding_date_discovered_Year'].'-'.$_POST['finding_date_discovered_Month'].'-'.$_POST['finding_date_discovered_Day']));
		$finding->setFindingDateClosed  ($_DB->sanitize($_POST['finding_date_closed_Year'].'-'.$_POST['finding_date_closed_Month'].'-'.$_POST['finding_date_closed_Day'].' '.$_POST['finding_date_closed_Hour'].':'.$_POST['finding_date_closed_Minute'].':'.$_POST['finding_date_closed_Second']));
		$finding->setFindingData  ($_DB->sanitize($_POST['finding_data']));
		
		$finding->saveFinding();
	
		// return to the list
		header("Location: ".$_CONFIG->APP_URL()."finding_list.php");
		break;
		
	}
		
default: break;		
	
} // switch form_action


// --------------------------------------------------------------------
//
// TEMPLATE POPULATION
//
// --------------------------------------------------------------------

require_once('../lib/Template.config.php');

$_TEMPLATE->assign('this_page',   'finding_create.php');
$_TEMPLATE->assign('this_title',  'FINDING > create');
$_TEMPLATE->assign('menu_header', 'finding');

require_once('../lib/FindingSourceList.class.php');
$sources = new FindingSourceList($_DB);
$sources->getSourceId(TRUE);
$sources->getSourceNickname();
$_TEMPLATE->assign('source_list', $sources->getKeyList());

require_once('../lib/AssetList.class.php');
$assets = new AssetList($_DB);
$assets->getAssetId(TRUE);
$assets->getAssetName();
$_TEMPLATE->assign('asset_list', $assets->getKeyList());


$_TEMPLATE->assign('finding_status_list', array('OPEN', 'CLOSED', 'REMEDIATION', 'DELETED'));
// --------------------------------------------------------------------
// 
// DISPLAY MENU AND HEADER
// 
// --------------------------------------------------------------------

// load header file
require_once('header.php');
require_once('menu.php');


// --------------------------------------------------------------------
// 
// DISPLAY PAGE CONTENT
// 
// --------------------------------------------------------------------

// display the template
$_TEMPLATE->display('finding_create.tpl');


// --------------------------------------------------------------------
// 
// DISPLAY FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load footer file
require_once('footer.php');

?>