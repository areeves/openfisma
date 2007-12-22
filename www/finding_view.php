<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

require_once('../lib/Finding.class.php');
require_once('../lib/Config.class.php');
require_once('../lib/Encryption.class.php');

// --------------------------------------------------------------------
// 
// FORM HANDLING
// 
// --------------------------------------------------------------------


// --------------------------------------------------------------------
// 
// DATA MANIPULATION
// 
// --------------------------------------------------------------------

// create a finding to view
$finding = new Finding($_DB);

// validate that the finding actually exists
if ($finding->findingExists($_POST['id'])) {

	// retrieve the user
	$finding->getFinding($_POST['id']);
	
}

// redirect on bum finding
else { header("Location: ".$_CONFIG->APP_URL()."finding_list.php"); }


// --------------------------------------------------------------------
// 
// TEMPLATE POPULATION
// 
// --------------------------------------------------------------------

require_once('../lib/Template.config.php');

// assign the template values
$_TEMPLATE->assign('this_page',   'finding_view.php');
$_TEMPLATE->assign('this_title',  'FINDING > view');
$_TEMPLATE->assign('menu_header', 'finding');

$_TEMPLATE->assign('id', $_POST['id']);

$_TEMPLATE->assign('source_id',               $finding->getSourceId());
$_TEMPLATE->assign('asset_id',                $finding->getAssetId());
$_TEMPLATE->assign('finding_status',          $finding->getFindingStatus());
$_TEMPLATE->assign('finding_date_created',    $finding->getFindingDateCreated());
$_TEMPLATE->assign('finding_date_discovered', $finding->getFindingDateDiscovered());
$_TEMPLATE->assign('finding_date_closed',     $finding->getFindingDateClosed());
$_TEMPLATE->assign('finding_data',            $finding->getFindingData());

// set up button display options
$_TEMPLATE->assign('show_cancel', 1);
$_TEMPLATE->assign('show_update', 1);
$_TEMPLATE->assign('show_delete', 1);


// --------------------------------------------------------------------
// 
// PAGE DISPLAY
// 
// --------------------------------------------------------------------

// show header and menu
require_once('header.php');
require_once('menu.php');

// display the page content
$_TEMPLATE->display('finding_view.tpl');

// display the footer
require_once('footer.php');

?>