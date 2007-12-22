<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

require_once('../lib/Config.class.php');
require_once('../lib/Template.config.php');
require_once('../lib/Filter.class.php');
require_once('../lib/Listing.class.php');

require_once('../lib/Asset.class.php');

require_once('../lib/Finding.class.php');
require_once('../lib/FindingList.class.php');
require_once('../lib/FindingSourceList.class.php');

require_once('../lib/System.class.php');
require_once('../lib/SystemList.class.php');

require_once('../lib/PoamList.class.php');


// --------------------------------------------------------------------
//
// DATA RETRIEVAL / MANIPULATION
//
// --------------------------------------------------------------------

// 
// GET UNIQUE VALUES
// 

// set up listing variables
$_FINDINGS  = new FindingList($_DB);
$_POAMS     = new PoamList($_DB);
$_SYSTEMS   = new SystemList($_DB);
$_SOURCES   = new FindingSourceList($_DB);

// set up our uniques array
$_UNIQUES = array();

// grab our system list
$_SYSTEMS->reset();
$_SYSTEMS->getSystemId(TRUE);
$_SYSTEMS->getSystemNickname();
$_SYSTEMS->setOrder('system_nickname ASC');
$_UNIQUES['systems'] = $_SYSTEMS->getKeyList();

// grab our source list
$_SOURCES->reset();
$_SOURCES->getSourceId(TRUE);
$_SOURCES->getSourceNickname();
$_SOURCES->setOrder('source_nickname ASC');
$_UNIQUES['sources'] = $_SOURCES->getKeyList();

// grab our POA&M types
$_POAMS->reset();
$_POAMS->getPoamType();
$_POAMS->setOrder('poam_type ASC');
$temp = $_POAMS->getUniques();
$_UNIQUES['poam_types'] = $_FILTER->arrayToOptions($temp['poam_type']);

// grab our POA&M statuses
$_POAMS->reset();
$_POAMS->getPoamStatus();
$_POAMS->setOrder('poam_status ASC');
$temp = $_POAMS->getUniques();
$_UNIQUES['poam_statuses'] = $_FILTER->arrayToOptions($temp['poam_status']);


// 
// SET UP THE FILTERS
// 

// handle the reset
if ($_POST['form_action'] == 'reset') { unset($_POST); }

$_FILTER->addFilter('Finding Source', 'source_id',   $_UNIQUES['sources'],       $_POST['source_id'],   1, isset($_POST['source_id_bool'])   ? $_POST['source_id_bool'] : 1 );
//$_FILTER->addFilter('Fiscal Year',   'fiscal_year', $_UNIQUES['fiscal_years'],  $_POST['fiscal_year'], 1, isset($_POST['fiscal_year_bool']) ? $_POST['fiscal_year_bool'] : 1 );
$_FILTER->addFilter('POA&M Type',     'poam_type',   $_UNIQUES['poam_types'],    $_POST['poam_type'],   1, isset($_POST['poam_type_bool'])   ? $_POST['poam_type_bool'] : 1   );
$_FILTER->addFilter('POA&M Status',   'poam_status', $_UNIQUES['poam_statuses'], $_POST['poam_status'], 1, isset($_POST['poam_status_bool']) ? $_POST['poam_status_bool'] : 1 );
$_FILTER->addFilter('System',         'system_id',   $_UNIQUES['systems'],       $_POST['system_id'],   1, isset($_POST['system_id_bool'])   ? $_POST['system_id_bool'] : 1 );

// save everything to the filter and commit it to the template
$_FILTER->setFormTags(TRUE, TRUE);
$_FILTER->commit($_TEMPLATE);


// 
// SET UP THE LISTING
// 

// only if something was actually posted
if (! empty($_POST)) {

	// 
	// SET UP THE COLUMNS
	//

	$_LISTING->addColumn("PO",         TRUE, "primary_office",   FALSE, 1, "LEFT");
	$_LISTING->addColumn("System",     TRUE, "system_nickname",  FALSE, 1, "LEFT");
	$_LISTING->addColumn("Tier",       TRUE, "system_tier",      FALSE, 1, "LEFT");
	$_LISTING->addColumn("ID#",        TRUE, "poam_id",          FALSE, 1, "LEFT");
	$_LISTING->addColumn("Finding",    TRUE, "finding_data",     FALSE, 1, "LEFT");

	$_LISTING->addColumn("Type",       TRUE, "poam_type",        FALSE, 1, "LEFT");
	$_LISTING->addColumn("Status",     TRUE, "poam_status",      FALSE, 1, "LEFT");
	$_LISTING->addColumn("Server/DB",  TRUE, "asset_name",       FALSE, 1, "LEFT");
	$_LISTING->addColumn("Location",   TRUE, "network_name",     FALSE, 1, "LEFT");
	$_LISTING->addColumn("Risk Level", TRUE, "risk_level",       FALSE, 1, "LEFT");

	$_LISTING->addColumn("Corrective Action",         TRUE, "poam_action_planned",   FALSE, 1, "LEFT");
	$_LISTING->addColumn("Recommendation",            TRUE, "poam_action_suggested", FALSE, 1, "LEFT");
	$_LISTING->addColumn("Estimated Completion Date", TRUE, "poam_action_date_est",  FALSE, 1, "LEFT");

	// 
	// RETRIEVE THE LIST
	// 

	$_ASSET   = new Asset($_DB);
	$_SYSTEM  = new System($_DB);
	$_FINDING = new Finding($_DB);

	$_POAMS->reset();
	$_POAMS->getFindingId();
	$_POAMS->getPoamActionOwner();
	$_POAMS->getPoamId();
	$_POAMS->getPoamType();
	$_POAMS->getPoamStatus();
	$_POAMS->getPoamActionPlanned();
	$_POAMS->getPoamActionSuggested();
	$_POAMS->getPoamActionDateEst();

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

	// retrieve the list
	$list = $_POAMS->getList();

	// list handling
	foreach ($list as $row) {

		// TODO: add risk level and location

		// retrieve the appropriate findings
		$_SYSTEM->getSystem($row['poam_action_owner']);
		$_FINDING->getFinding($row['finding_id']);
		$_ASSET->getAsset($_FINDING->getAssetId());
	
		// fill in the blanks	
		$row['primary_office']  = 'FSA';	
		$row['system_nickname'] = $_SYSTEM->getSystemNickname();
		$row['system_tier']     = $_SYSTEM->getSystemTier();	
		$row['finding_data']    = $_FINDING->getFindingData();	
		$row['asset_name']      = $_ASSET->getAssetName();
	
		// add the row
		$_LISTING->addRow(FALSE, $row, FALSE, FALSE, FALSE);
	
	}

	// commit the rows
	$_LISTING->setFormTags(FALSE, FALSE);
	$_LISTING->commit($_TEMPLATE);

}

// --------------------------------------------------------------------
// 
// HEADER VALUES
// 
// --------------------------------------------------------------------

$_TEMPLATE->assign('this_page',   'report_poam.php');
$_TEMPLATE->assign('this_title',  'Reports > POA&M');
$_TEMPLATE->assign('menu_header', 'reporting');


// --------------------------------------------------------------------
// 
// PAGE DISPLAY
// 
// --------------------------------------------------------------------

// load header file
require_once('header.php');
require_once('menu.php');

// display page contents
$_TEMPLATE->display('filter.tpl');
if (! empty($_POST)) { $_TEMPLATE->display('listing.tpl'); }

// load footer file
require_once('footer.php');

?>
