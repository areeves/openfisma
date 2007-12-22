<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

// class includes
require_once('../lib/Config.class.php');
require_once('../lib/Finding.class.php');
require_once('../lib/Database.class.php');
require_once('../lib/Encryption.class.php');


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

case 'Delete':

	// only update if we sent the post
	if ($_POST['referrer'] == 'finding_delete.php') {
	
		// create a new finding instance
		$finding = new Finding($_DB, $_POST['id']);
		
		// delete the bastard
		$finding->deleteFinding();
	
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

// load the template
require_once('../lib/Template.config.php');

// assign the template values
$_TEMPLATE->assign('this_page',   'finding_delete.php');
$_TEMPLATE->assign('this_title',  'FINDING > delete');
$_TEMPLATE->assign('menu_header', 'finding');

// retrieve the existing finding's values
$finding = new Finding($_DB, $_POST['id']);

$_TEMPLATE->assign('id',                      $_POST['id']);
$_TEMPLATE->assign('source_id',               $finding->getSourceId());
$_TEMPLATE->assign('asset_id',                $finding->getAssetId());
$_TEMPLATE->assign('finding_status',          $finding->getFindingStatus());
$_TEMPLATE->assign('finding_date_created',    $finding->getFindingDateCreated());
$_TEMPLATE->assign('finding_date_discovered', $finding->getFindingDateDiscovered());
$_TEMPLATE->assign('finding_date_closed',     $finding->getFindingDateClosed());
$_TEMPLATE->assign('finding_data',            $finding->getFindingData());


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
$_TEMPLATE->display('finding_delete.tpl');


// --------------------------------------------------------------------
// 
// DISPLAY FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load footer file
require_once('footer.php');

?>