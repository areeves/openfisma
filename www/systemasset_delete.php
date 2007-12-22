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

case 'Delete':

	// only update if we sent the post
	if ($_POST['referrer'] == 'systemasset_delete.php') {
	
		// create a new systemasset instance
		$systemasset = new SystemAsset($_DB, unserialize($_E->decrypt($_POST['systemasset_id'])));
		
		// delete the bastard
		$systemasset ->deleteSystemAsset(unserialize($_E->decrypt($_POST['systemasset_id'])));
	
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

// assign the template values
$_TEMPLATE->assign('this_page',  'systemasset_delete.php');
$_TEMPLATE->assign('this_title', 'SYSTEMASSET > delete');
$_TEMPLATE->assign('menu_header', 'system asset');

// retrieve the existing systemasset's values
$systemasset = new SystemAsset($_DB, unserialize($_E->decrypt($_POST['systemasset_id'])));

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
$_TEMPLATE->display('systemasset_delete.tpl');


// --------------------------------------------------------------------
// 
// DISPLAY FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load footer file
require_once('footer.php');

?>