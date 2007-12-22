<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

require_once('../lib/Config.class.php');
require_once('../lib/Database.class.php');
require_once('../lib/Encryption.class.php');

require_once('../lib/Asset.class.php');


// --------------------------------------------------------------------
// 
// FORM HANDLING
// 
// --------------------------------------------------------------------

// handle the form action
switch ($_POST['form_action']) {
	
case 'cancel': 

	// action cancelled
	header("Location: ".$_CONFIG->APP_URL()."asset_list.php");
	break;

case 'delete':

	// only update if we sent the post
	if ($_POST['referrer'] == 'asset_delete.php') {
	
		// create a new asset instance
		$asset = new Asset($_DB, $_E->decrypt($_POST['asset_id']));
		
		// delete the bastard
		$asset->deleteAsset();
	
		// return to the list
		header("Location: ".$_CONFIG->APP_URL()."asset_list.php");
		break;
		
	}
		
default: break;		
	
} // switch form_action


// --------------------------------------------------------------------
//
// DATA RETRIEVAL/MANIPULATION
//
// --------------------------------------------------------------------

// retrieve the existing asset's values
$asset = new Asset($_DB, $_E->decrypt($_POST['asset_id']));


// --------------------------------------------------------------------
//
// TEMPLATE POPULATION
//
// --------------------------------------------------------------------

// load the template
require_once('../lib/Template.config.php');

// assign the template values
$_TEMPLATE->assign('this_page',  'asset_delete.php');
$_TEMPLATE->assign('this_title', 'ASSET > delete');
$_TEMPLATE->assign('menu_header', 'asset');

$_TEMPLATE->assign('asset_id',           $_POST['asset_id']);
$_TEMPLATE->assign('prod_id', $asset->getProdId());
$_TEMPLATE->assign('asset_name',         $asset->getAssetName()        );
$_TEMPLATE->assign('asset_date_created', $asset->getAssetDateCreated() );
$_TEMPLATE->assign('asset_source',       $asset->getAssetSource()      );


// --------------------------------------------------------------------
// 
// PAGE DISPLAY
// 
// --------------------------------------------------------------------

// display header and menu
require_once('header.php');
require_once('menu.php');

// display content
$_TEMPLATE->display('asset_delete.tpl');

// display footer
require_once('footer.php');

?>