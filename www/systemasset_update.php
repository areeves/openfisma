<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

// class includes
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

case 'Update':

	// only update if we sent the post
	if ($_POST['referrer'] == 'systemasset_update.php') {
	
		// create a new systemasset instance
		$systemasset = new SystemAsset($_DB, unserialize($_E->decrypt($_POST['systemasset_id'])));
	
		// update the newly created systemasset with sanitized input	
		$systemasset->setSystemId($_DB->sanitize($_POST['system_id']));
		$systemasset->setAssetId($_DB->sanitize($_POST['asset_id']));
		$systemasset->setSystemIsOwner($_DB->sanitize($_POST['system_is_owner']));
		
		$systemasset->saveSystemAsset(unserialize($_E->decrypt($_POST['systemasset_id'])));
		
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

// load the template
require_once('../lib/Template.config.php');

// retrieve the existing systemasset's values
$systemasset = new SystemAsset($_DB, unserialize($_E->decrypt($_POST['systemasset_id'])));

// assign the template values
$_TEMPLATE->assign('this_page',  'systemasset_update.php');
$_TEMPLATE->assign('this_title', 'SYSTEMASSET > update');
$_TEMPLATE->assign('menu_header', 'system asset');


$_TEMPLATE->assign('systemasset_id',              $_POST['systemasset_id']);
$_TEMPLATE->assign('system_id',      $systemasset->getSystemId()     );
$_TEMPLATE->assign('asset_id',      $systemasset->getAssetId()     );
$_TEMPLATE->assign('system_is_owner',      $systemasset->getSystemIsOwner()     );


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
$_TEMPLATE->display('systemasset_update.tpl');


// --------------------------------------------------------------------
// 
// DISPLAY FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load footer file
require_once('footer.php');

?>