<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

require_once('../lib/SystemAsset.class.php');
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

// create a systemasset to view
$systemasset = new SystemAsset($_DB);

// validate that the systemasset actually exists

if ($systemasset->systemassetExists(unserialize($_E->decrypt($_POST['systemasset_id'])))) {

	// retrieve the user
	$systemasset->getSystemAsset(unserialize($_E->decrypt($_POST['systemasset_id'])));
	
}

// redirect on bum systemasset
else { header("Location: ".$_CONFIG->APP_URL()."systemasset_list.php"); }


// --------------------------------------------------------------------
// 
// TEMPLATE POPULATION
// 
// --------------------------------------------------------------------

require_once('../lib/Template.config.php');

// assign the template values
$_TEMPLATE->assign('this_page',  'systemasset_view.php');
$_TEMPLATE->assign('this_title', 'SYSTEMASSET > view');
$_TEMPLATE->assign('menu_header', 'system asset');


$_TEMPLATE->assign('systemasset_id', $_POST['systemasset_id']);

$_TEMPLATE->assign('system_id',      $systemasset->getSystemId()     );
$_TEMPLATE->assign('asset_id',      $systemasset->getAssetId()     );
$_TEMPLATE->assign('system_is_owner',      $systemasset->getSystemIsOwner()     );

// --------------------------------------------------------------------
// 
// HEADER SECTION
// 
// --------------------------------------------------------------------

// load header file
require_once('header.php');
require_once('menu.php');


// --------------------------------------------------------------------
// 
// BODY SECTION
// 
// --------------------------------------------------------------------

// display the template
$_TEMPLATE->display('systemasset_view.tpl');


// --------------------------------------------------------------------
// 
// FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load footer file
require_once('footer.php');

?>