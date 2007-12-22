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

case 'update':

	// only update if we sent the post
	if ($_POST['referrer'] == 'asset_update.php') {
	
		// create a new asset instance
		$asset = new Asset($_DB, $_E->decrypt($_POST['asset_id']));
	
		// update the newly created asset with sanitized input	
		$asset->setProdId($_DB->sanitize($_POST['prod_id']));
		$asset->setAssetName   ($_DB->sanitize($_POST['asset_name']));
		$asset->setAssetSource ($_DB->sanitize($_POST['asset_source']));
		$asset->saveAsset();
		
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

require_once('../lib/Template.config.php');

// assign the template values
$_TEMPLATE->assign('this_page',  'asset_update.php');
$_TEMPLATE->assign('this_title', 'ASSET > update');
$_TEMPLATE->assign('menu_header', 'asset');


$_TEMPLATE->assign('asset_id',     $_POST['asset_id'] );
$_TEMPLATE->assign('prod_id', $asset->getProdId());
$_TEMPLATE->assign('asset_name',   $asset->getAssetName()  );
$_TEMPLATE->assign('asset_source', $asset->getAssetSource());

require_once('../lib/ProductList.class.php');
$prods = new ProductList($_DB);
$prods->getProdId(TRUE);
$prods->getProdName();
$_TEMPLATE->assign('prod_list', $prods->getKeyList());


$_TEMPLATE->assign('asset_source_list', array('MANUAL', 'SCAN', 'INVENTORY'));
// --------------------------------------------------------------------
// 
// PAGE DISPLAY
// 
// --------------------------------------------------------------------

// display header and menu
require_once('header.php');
require_once('menu.php');

// display content
$_TEMPLATE->display('asset_update.tpl');

// display footer
require_once('footer.php');

?>