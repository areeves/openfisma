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


// --------------------------------------------------------------------
// 
// DATA RETRIEVAL/MANIPULATION
// 
// --------------------------------------------------------------------

// create a asset to view
$asset = new Asset($_DB, $_E);

// validate that the asset actually exists
if ($asset->assetExists($_E->decrypt($_POST['asset_id']))) {

	// retrieve the asset
	$asset->getAsset($_E->decrypt($_POST['asset_id']));
	
}

// redirect on bum asset
else { header("Location: ".$_CONFIG->APP_URL()."asset_list.php"); }


// --------------------------------------------------------------------
// 
// TEMPLATE POPULATION
// 
// --------------------------------------------------------------------

require_once('../lib/Template.config.php');

// assign the template values
$_TEMPLATE->assign('this_page',  'asset_view.php');
$_TEMPLATE->assign('this_title', 'ASSET > view');
$_TEMPLATE->assign('menu_header', 'asset');


$_TEMPLATE->assign('asset_id',           $_POST['asset_id']);
$_TEMPLATE->assign('prod_id',            $asset->getProdId());
$_TEMPLATE->assign('asset_name',         $asset->getAssetName());
$_TEMPLATE->assign('asset_date_created', $asset->getAssetDateCreated());
$_TEMPLATE->assign('asset_source',       $asset->getAssetSource());

// set up button display options
$_TEMPLATE->assign('show_cancel', 1);
$_TEMPLATE->assign('show_update', 1);
$_TEMPLATE->assign('show_delete', 1);


// --------------------------------------------------------------------
// 
// PAGE DISPLAY
// 
// --------------------------------------------------------------------

// display header and menu
require_once('header.php');
require_once('menu.php');

// display content
$_TEMPLATE->display('asset_view.tpl');

// display footer file
require_once('footer.php');

?>