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
// FORM HANDLING
// 
// --------------------------------------------------------------------

// handle the form action
switch ($_POST['form_action']) {

case 'create': header("Location: ".$_CONFIG->APP_URL()."remediation_create.php"); break;
case 'view'  : header("Location: ".$_CONFIG->APP_URL()."remediation_view.php");   break;
case 'update': header("Location: ".$_CONFIG->APP_URL()."remediation_update.php"); break;
case 'delete': header("Location: ".$_CONFIG->APP_URL()."remediation_delete.php"); break;
		
default: break;
	
} // switch form_action


// --------------------------------------------------------------------
//
// DATA RETRIEVAL / MANIPULATION
//
// --------------------------------------------------------------------

require_once('../lib/PoamList.class.php');
require_once('../lib/FindingList.class.php');
require_once('../lib/FindingSourceList.class.php');
require_once('../lib/AssetList.class.php');
require_once('../lib/SystemAssetList.class.php');
require_once('../lib/SystemList.class.php');

// we need to work with POAMs, findings, sources, systems
$_POAMS    = new PoamList($_DB);
$_FINDINGS = new FindingList($_DB);
$_SOURCES  = new FindingSourceList($_DB);
$_ASSETS   = new AssetList($_DB);
$_SYSTEMS  = new SystemList($_DB);

// create the system_assets link
$_SYSASS   = new SystemAssetList($_DB);

// create our array of unique values
$_UNIQUES  = array();


// 
// GET UNIQUE VALUES
// 

// finding sources
$_POAMS->reset();
$_POAMS->getFindingId();
$temp = $_POAMS->getUniques();
$_UNIQUES['finding_ids'] = $temp['finding_id']; 

$_FINDINGS->reset();
$_FINDINGS->getSourceId();
$_FINDINGS->filterFindingId($temp['finding_id']);
$temp = $_FINDINGS->getUniques();

$_SOURCES->reset();
$_SOURCES->getSourceId(TRUE);
$_SOURCES->getSourceNickname();
$_SOURCES->setOrder('source_nickname ASC');
$_UNIQUES['finding_sources'] = $_SOURCES->getKeyList();

// poam types
$_POAMS->reset();
$_POAMS->getPoamType();
$_POAMS->setOrder('poam_type ASC');
$temp = $_POAMS->getUniques();
$_UNIQUES['poam_type'] = $_FILTER->arrayToOptions($temp['poam_type']);

// poam statuses
$_POAMS->reset();
$_POAMS->getPoamStatus();
$_POAMS->setOrder('poam_status ASC');
$temp = $_POAMS->getUniques();
$_UNIQUES['poam_status'] = $_FILTER->arrayToOptions($temp['poam_status']);

// systems
$_FINDINGS->reset();
$_FINDINGS->getAssetId();
$_FINDINGS->filterFindingId($_UNIQUES['finding_ids']);
$temp = $_FINDINGS->getUniques();

$_UNIQUES['asset_ids'] = $temp['asset_id'];

$_SYSASS->reset();
$_SYSASS->getSystemId(TRUE);
$_SYSASS->filterAssetId($_UNIQUES['asset_ids'], TRUE);
$_SYSASS->filterSystemIsOwner('1', TRUE);
$temp = $_SYSASS->getUniques();

$_SYSTEMS->reset();
$_SYSTEMS->getSystemId(TRUE);
$_SYSTEMS->getSystemNickname();
$_SYSTEMS->setOrder('system_nickname ASC');
$_UNIQUES['systems'] = $_SYSTEMS->getKeyList();

// select our poam columns
$_POAMS->getPoamId();
$_POAMS->getFindingId();
$_POAMS->getPoamType();
$_POAMS->getPoamStatus();
$_POAMS->getPoamActionOwner();
$_POAMS->getPoamActionDateEst();

// get a list of our unique column values
$poam_uniques = $_POAMS->getUniques();

// select our finding columns
$_FINDINGS->getSourceId();

// get a list of the unique column values
$finding_uniques = $_FINDINGS->getUniques();

// select our finding_source columns
$_SOURCES->getSourceId(TRUE);
$_SOURCES->getSourceNickname();
$_SOURCES->filterSourceId($finding_uniques['source_id']);
$_SOURCES->setOrder('source_name ASC');


//
// SET UP FILTERS 
//

// clear out our posted filter values if we are resetting
if ($_POST['form_action'] == 'reset') {

	unset($_POST['source_id']);
	unset($_POST['poam_type']);
	unset($_POST['poam_status']);
	unset($_POST['system_id']); 	
	
}


// add filters to our filter list
$_FILTER->addFilter('Finding Source', 'source_id',   $_UNIQUES['finding_sources'], $_POST['source_id'],   1, isset($_POST['source_id_bool'])      ? $_POST['source_id_bool']   : 1 );
$_FILTER->addFilter('POA&M Type',     'poam_type',   $_UNIQUES['poam_type'],       $_POST['poam_type'],   1, isset($_POST['finding_status_bool']) ? $_POST['poam_type_bool']   : 1 );
$_FILTER->addFilter('POA&M Status',   'poam_status', $_UNIQUES['poam_status'],     $_POST['poam_status'], 1, isset($_POST['finding_status_bool']) ? $_POST['poam_status_bool'] : 1 );
$_FILTER->addFilter('Action Owner',   'system_id',   $_UNIQUES['systems'],         $_POST['system_id'],   1, isset($_POST['system_bool'])         ? $_POST['system_bool']      : 1 );

// update the form information and commit the changes
$_FILTER->setFormTags(TRUE, FALSE);
$_FILTER->commit($_TEMPLATE);

//
// SET UP POAMS
// 

$_POAMS->reset();
$_POAMS->getPoamId();
$_POAMS->getPoamType();
$_POAMS->getPoamStatus();
$_POAMS->getPoamActionOwner();
$_POAMS->getFindingId();

// apply direct filters 
if (isset($_POST['poam_type'])   && $_POST['poam_type']   != $_FILTER->getALL()) { $_POAMS->filterPoamType  ($_POST['poam_type'],      ($_POST['poam_type_bool']   == 1) ? TRUE : FALSE ); }
if (isset($_POST['poam_status']) && $_POST['poam_status'] != $_FILTER->getALL()) { $_POAMS->filterPoamStatus($_POST['poam_status'],    ($_POST['poam_status_bool'] == 1) ? TRUE : FALSE ); }
if (isset($_POST['system_id'])   && $_POST['system_id']   != $_FILTER->getALL()) { $_POAMS->filterPoamActionOwner($_POST['system_id'], ($_POST['system_id_bool']   == 1) ? TRUE : FALSE ); }

// apply indirect filters
if (isset($_POST['source_id']) && $_POST['source_id'] != $_FILTER->getALL()) { 
	
	// retrieve the source through the findings
	$_FINDINGS->reset();
	$_FINDINGS->getFindingId();
	$_FINDINGS->filterSourceId($_POST['source_id'], ($_POST['source_id_bool'] == 1) ? TRUE : FALSE );
	$temp = $_FINDINGS->getUniques();
	
	// filter the POAMS on the returned finding_ids
	$_POAMS->filterFindingId($temp['finding_id']);

}


//
// SET UP PAGER
//

// set up the list_size, current_page and page_size
$_PAGER->setListSize   ($_POAMS->getListSize());
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

$_LISTING->addColumn("ID",      TRUE, "id",                FALSE, 1, "LEFT");
$_LISTING->addColumn("Source",  TRUE, "finding_source",    FALSE, 1, "LEFT");
$_LISTING->addColumn("System",  TRUE, "poam_action_owner", FALSE, 1, "LEFT");
$_LISTING->addColumn("Type",    TRUE, "poam_type",         FALSE, 1, "LEFT");
$_LISTING->addColumn("Status",  TRUE, "poam_status",       FALSE, 1, "LEFT");
$_LISTING->addColumn("Finding", TRUE, "finding_data",      FALSE, 1, "LEFT");

// retrieve the appropriate page from the list (pager provides offset and page size)
$poam_list = $_POAMS->getList($_PAGER->getPageOffset(), $_PAGER->getPageSize());

// 
// PREP THE LIST FOR DISPLAY
// 
foreach ($poam_list as $row) {

	// grab the finding_data, finding_source 
	$_FINDINGS->reset();
	$_FINDINGS->getFindingId(TRUE);
	$_FINDINGS->getFindingData();
	$_FINDINGS->getSourceId();
	$_FINDINGS->getAssetId();
	$_FINDINGS->filterFindingId($row['finding_id']);
	$temp = $_FINDINGS->getList();

	// add the values
	$row['id'] = $row['poam_id'];
	$row['finding_data']   = $temp[0]['finding_data'];
	$row['finding_source'] = $_UNIQUES['finding_sources'][$temp[0]['source_id']];

	$row['poam_action_owner'] = $_UNIQUES['systems'][$row['poam_action_owner']];

	// add the row to the listing
	$_LISTING->addRow(TRUE, $row, TRUE, FALSE, FALSE);

}

// commit the results to the template
$_LISTING->setRowIndex('id');

$_LISTING->showCheckboxes(FALSE);
$_LISTING->showActions();
$_LISTING->setCreateTarget('poam_create.php');
$_LISTING->setViewTarget('remediation_view.php');
$_LISTING->setUpdateTarget('poam_update.php');
$_LISTING->setDeleteTarget('poam_delete.php');

$_LISTING->showCreate();
$_LISTING->setFormTags(FALSE, TRUE);
$_LISTING->commit($_TEMPLATE);


// --------------------------------------------------------------------
//
// TEMPLATE POPULATION
//
// --------------------------------------------------------------------

// header values
$_TEMPLATE->assign('this_page',   'remediation_list.php');
$_TEMPLATE->assign('this_title',  'POAM > list');
$_TEMPLATE->assign('menu_header', 'remediation');

// poam list
$_TEMPLATE->assign('remediation_list', $poam_list);


// --------------------------------------------------------------------
// 
// PAGE DISPLAY
// 
// --------------------------------------------------------------------

// display header and menu
require_once('header.php');
require_once('menu.php');

// display content
$_TEMPLATE->display('filter.tpl');
$_TEMPLATE->display('pager.tpl');
$_TEMPLATE->display('listing.tpl');

print "<PRE>";

//print_r($_POST);
//print_r($_UNIQUES);

print "</PRE>";

// display footer
require_once('footer.php');

?>