<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

require_once('../lib/Config.class.php');
require_once('../lib/SystemAsset.class.php');
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
	header("Location: ".$_CONFIG->APP_URL()."systemasset_list.php");
	break;

case 'Create':

	// create systemasset if we are referrer
	if ($_POST['referrer'] == 'systemasset_create.php') {

		// create a new systemasset instance
		$systemasset = new SystemAsset($_DB);
	
		// update the newly created systemasset with sanitized input	
		$systemasset->setSystemId($_DB->sanitize($_POST['system_id']));
		$systemasset->setAssetId($_DB->sanitize($_POST['asset_id']));
		$systemasset->setSystemIsOwner($_DB->sanitize($_POST['system_is_owner']));
		
		$systemasset->saveSystemAsset();
	
		// return to the list
		header("Location: ".$_CONFIG->APP_URL()."systemasset_list.php");
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

$_TEMPLATE->assign('this_page',  'systemasset_create.php');
$_TEMPLATE->assign('this_title', 'SYSTEMASSET > create');
$_TEMPLATE->assign('menu_header', 'system asset');


// --------------------------------------------------------------------
// 
// DISPLAY MENU AND HEADER
// 
// --------------------------------------------------------------------

// identify our page to the header
define('PAGE_NAME',  'systemasset_create.php'); 
define('PAGE_TITLE', 'SystemAsset: create');

// load header file
require_once('header.php');
require_once('menu.php');


// --------------------------------------------------------------------
// 
// DISPLAY PAGE CONTENT
// 
// --------------------------------------------------------------------

// display the template
$_TEMPLATE->display('systemasset_create.tpl');


// --------------------------------------------------------------------
// 
// DISPLAY FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load footer file
require_once('footer.php');

?>