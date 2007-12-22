<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

require_once('../lib/Config.class.php');
require_once('../lib/Database.class.php');
require_once('../lib/Encryption.class.php');

require_once('../lib/Template.config.php');
require_once('../lib/Filter.class.php');
require_once('../lib/Pager.class.php');
require_once('../lib/Listing.class.php');


// --------------------------------------------------------------------
// 
// FORM HANLDING
// 
// --------------------------------------------------------------------

// handle the form action
switch ($_POST['form_action']) {

case 'create': header("Location: ".$_CONFIG->APP_URL()."finding_create.php"); break;
case 'view'  : header("Location: ".$_CONFIG->APP_URL()."finding_view.php");   break;
case 'update': header("Location: ".$_CONFIG->APP_URL()."finding_update.php"); break;	
case 'delete': header("Location: ".$_CONFIG->APP_URL()."finding_delete.php"); break;

default: break;
	
} // switch form_action


// --------------------------------------------------------------------
//
// DATA RETRIEVAL / MANIPULATION
//
// --------------------------------------------------------------------

// includes
require_once('../lib/FindingList.class.php');
require_once('../lib/FindingSourceList.class.php');
require_once('../lib/AssetList.class.php');
require_once('../lib/AssetAddressList.class.php');
require_once('../lib/SystemList.class.php');
require_once('../lib/SystemAssetList.class.php');


// create the lists
$_FINDINGS   = new FindingList($_DB);
$_SOURCES    = new FindingSourceList($_DB);
$_ASSETS     = new AssetList($_DB);
$_ADDRESSES  = new AssetAddressList($_DB);
$_SYSTEMS    = new SystemList($_DB);

// create the system_assets link
$_SYSASS = new SystemAssetList($_DB);

// create our unique items array
$_UNIQUES    = array();


// 
// GET UNIQUE VALUES
// 

// FINDING SOURCES
$_FINDINGS->reset();
$_FINDINGS->getSourceId();
$_FINDINGS->setOrder('source_id ASC');
$temp = $_FINDINGS->getUniques();

$_SOURCES->getSourceId(TRUE);
$_SOURCES->getSourceNickname();
$_SOURCES->filterSourceId($temp['source_id'], TRUE);
$_SOURCES->setOrder('source_name ASC');
$_UNIQUES['finding_sources'] = $_SOURCES->getKeyList();

// FINDING STATUS
$_FINDINGS->reset();
$_FINDINGS->getFindingStatus(TRUE);
$_FINDINGS->setOrder('finding_status ASC');
$temp = $_FINDINGS->getUniques();
$_UNIQUES['finding_status'] = $_FILTER->arrayToOptions($temp['finding_status']);

// ASSETS
$_FINDINGS->reset();
$_FINDINGS->getAssetId();
$temp = $_FINDINGS->getUniques();

$_ASSETS->getAssetId(TRUE);
$_ASSETS->getAssetName();
$_ASSETS->filterAssetId($temp['asset_id']);
$_ASSETS->setOrder('asset_name ASC');
$_UNIQUES['assets'] = $_ASSETS->getKeyList();

// SYSTEMS (from assets)
$_SYSASS->getSystemId(TRUE);
$_SYSASS->filterAssetId($temp['asset_id']);
$_SYSASS->filterSystemIsOwner('1');
$temp = $_SYSASS->getUniques();

$_SYSTEMS->getSystemId(TRUE);
$_SYSTEMS->getSystemNickname();
$_SYSTEMS->filterSystemId($temp['system_id']);
$_SYSTEMS->setOrder('system_nickname ASC');
$_UNIQUES['systems'] = $_SYSTEMS->getKeyList(); 


//
// SET UP FILTERS 
//

// clear out our posted filter values if we are resetting
if ($_POST['form_action'] == 'reset') {

	unset($_POST['source_id']);
	unset($_POST['finding_status']);
	unset($_POST['system_id']);
	unset($_POST['asset_id']); 	
	
}


// add filters to our filter list
$_FILTER->addFilter('Finding Source', 'source_id',      $_UNIQUES['finding_sources'], $_POST['source_id'],      1, isset($_POST['source_bool'])         ? $_POST['source_bool']         : 1 );
$_FILTER->addFilter('Status',         'finding_status', $_UNIQUES['finding_status'],  $_POST['finding_status'], 1, isset($_POST['finding_status_bool']) ? $_POST['finding_status_bool'] : 1 );
$_FILTER->addFilter('System',         'system_id',      $_UNIQUES['systems'],         $_POST['system_id'],      1, isset($_POST['system_bool'])         ? $_POST['system_bool']         : 1 );
$_FILTER->addFilter('Asset',          'asset_id',       $_UNIQUES['assets'],          $_POST['asset_id'],       1, isset($_POST['asset_bool'])          ? $_POST['asset_bool']          : 1 );


// update the form information and commit the changes
$_FILTER->setFormTags(TRUE, FALSE);
$_FILTER->commit($_TEMPLATE);


// 
// SET UP FINDINGS 
// 

// columns
$_FINDINGS->reset();
$_FINDINGS->getFindingId();
$_FINDINGS->getSourceId();
$_FINDINGS->getAssetId();
$_FINDINGS->getFindingDateDiscovered();
$_FINDINGS->getFindingStatus();
$_FINDINGS->getFindingData();

// apply direct filters
if (isset($_POST['source_id'])      && $_POST['source_id']      != $_FILTER->getALL()) { $_FINDINGS->filterSourceId     ($_POST['source_id'],      ($_POST['source_id_bool'] == 1)      ? TRUE : FALSE ); } 
if (isset($_POST['finding_status']) && $_POST['finding_status'] != $_FILTER->getALL()) { $_FINDINGS->filterFindingStatus($_POST['finding_status'], ($_POST['finding_status_bool'] == 1) ? TRUE : FALSE ); }
if (isset($_POST['asset_id'])       && $_POST['asset_id']       != $_FILTER->getALL()) { $_FINDINGS->filterAssetId      ($_POST['asset_id'],       ($_POST['asset_id_bool'] == 1)       ? TRUE : FALSE ); }

// apply indirect filters 
if (isset($_POST['system_id']) && $_POST['system_id']      != $_FILTER->getALL()) { 

	// collect assets for the given system
	$_SYSASS->reset();
	$_SYSASS->getAssetId(TRUE);
	$_SYSASS->filterSystemId($_POST['system_id'], TRUE);
	$_SYSASS->filterSystemIsOwner('1');
	$temp = $_SYSASS->getUniques();

	// add in the appropriate filter
	$_FINDINGS->filterAssetId($temp['asset_id'], ($_POST['system_id_bool'] == 1) ? TRUE : FALSE );
	
}


// 
// SET UP PAGER
// 

// set up the list_size, current_page and page_size
$_PAGER->setListSize   ($_FINDINGS->getListSize());
$_PAGER->setCurrentPage(isset($_POST['current_page']) ? $_POST['current_page'] : 1);
$_PAGER->setPageSize   (isset($_POST['page_size'])    ? $_POST['page_size']    : $_CONFIG->PAGE_SIZE());

// handle the pager action
$_PAGER->doPageAction($_POST['form_action'], 
					  isset($_POST['page_size']) ? $_POST['page_size'] : $_CONFIG->PAGE_SIZE(),
					  isset($_POST['page_jump']) ? $_POST['page_jump'] : 1);
					  
// commit the results to the template
$_PAGER->setFormTags(FALSE, FALSE);
$_PAGER->commit($_TEMPLATE);


//
// INITIALIZE LISTING TABLE 
//

// add in the column headers
//$_LISTING->addColumn("X",               TRUE, "NOID",                    FALSE, 1, "CENTER");
$_LISTING->addColumn("ID",              TRUE, "finding_id",              FALSE, 1, "LEFT");
$_LISTING->addColumn("Date Discovered", TRUE, "finding_date_discovered", FALSE, 1, "LEFT");
$_LISTING->addColumn("Source",          TRUE, "source_id",               FALSE, 1, "LEFT");
$_LISTING->addColumn("System",          TRUE, "system_id",               FALSE, 1, "LEFT");
$_LISTING->addColumn("Status",          TRUE, "finding_status",          FALSE, 1, "LEFT");
$_LISTING->addColumn("Finding",         TRUE, "finding_data",            FALSE, 1, "LEFT");

// retrieve the appropriate page from the list (pager provides offset and page size)
$finding_list = $_FINDINGS->getList($_PAGER->getPageOffset(), $_PAGER->getPageSize());


//
// PREP THE LISTING FOR DISPLAY
//
foreach ($finding_list as $row) {
	
	// give a nice name for the finding source
	$row['source_id'] = $_UNIQUES['finding_sources'][$row['source_id']];
	
	// get the system name
	$_SYSASS->reset();
	$_SYSASS->getSystemid();
	$_SYSASS->filterAssetId($row['asset_id']);
	$system_name = $_SYSASS->getList();

	// assign the system_name	
	$row['system_id'] = $_UNIQUES['systems'][$system_name[0]['system_id']];

	// add the row to the listing	
	$_LISTING->addRow(TRUE, $row, TRUE, TRUE, TRUE);	
	
}

// commit the results to the template
$_LISTING->setRowIndex('finding_id');
$_LISTING->showCheckboxes(FALSE);

$_LISTING->showActions();
$_LISTING->setCreateTarget('finding_create.php');
$_LISTING->setViewTarget('finding_view.php');
$_LISTING->setUpdateTarget('finding_update.php');
$_LISTING->setDeleteTarget('finding_delete.php');

$_LISTING->showCreate();
$_LISTING->setFormTags(FALSE, TRUE);
$_LISTING->commit($_TEMPLATE);


// --------------------------------------------------------------------
//
// TEMPLATE POPULATION
//
// --------------------------------------------------------------------

// header
$_TEMPLATE->assign('this_page',   'finding_list.php');
$_TEMPLATE->assign('this_title',  'FINDING > list');
$_TEMPLATE->assign('menu_header', 'finding');

// sorting variables
$_TEMPLATE->assign('sort_standalone',  0);
$_TEMPLATE->assign('sort_params',      $_POST['sort_params']);

// finding_list
$_TEMPLATE->assign('finding_list', $finding_list);


// --------------------------------------------------------------------
// 
// PAGE DISPLAY
// 
// --------------------------------------------------------------------

// display header and menu
require_once('header.php');
require_once('menu.php');

// display the content
$_TEMPLATE->display('filter.tpl');
$_TEMPLATE->display('pager.tpl');
$_TEMPLATE->display('listing.tpl');

// display footer
require_once('footer.php');

?>