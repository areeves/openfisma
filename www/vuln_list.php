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

require_once('../lib/VulnList.class.php');
require_once('../lib/Pager.class.php');


// --------------------------------------------------------------------
// 
// FORM HANLDING
// 
// --------------------------------------------------------------------

// handle the form action
switch ($_POST['form_action']) {

case 'V': header("Location: ".$_CONFIG->APP_URL()."vuln_view.php");   break;
case 'U': header("Location: ".$_CONFIG->APP_URL()."vuln_update.php"); break;	
case 'D': header("Location: ".$_CONFIG->APP_URL()."vuln_delete.php"); break;

default: break;
	
} // switch form_action


// --------------------------------------------------------------------
//
// DATA RETRIEVAL / MANIPULATION
//
// --------------------------------------------------------------------

// create the vuln list for the listing
$vulns = new VulnList($_DB);

// select desired list parameters (column headers)
$vulns->getVulnSeq();
$vulns->getVulnType();
$vulns->getVulnDescPrimary();
$vulns->getVulnDescSecondary();

// retrieve unique items in each column for filter options
$uniques = $vulns->getUniques();

// prepend "any" option to unique filter options
$keys = array_keys($uniques);
while ($key = array_pop($keys)) { $uniques[$key] = array('-- any --'=>'-- any --') + $uniques[$key]; }

// initialize our boolean options for filter boolean options
$bool_options = array('is', 'is not');
$bool_values  = array(1, 0);

// add filters and sort information if not asked to reset
if ($_POST['form_action'] != 'reset') {

  // add filters to list
  if (isset($_POST['vuln_type'])                     && ($_POST['vuln_type']                    != '-- any --')) { $vulns->filterVulnType($_POST['vuln_type'], $_POST['vuln_type_bool']);                     }
  if (isset($_POST['vuln_desc_primary'])             && ($_POST['vuln_desc_primary']            != '-- any --')) { $vulns->filterVulnDescPrimary($_POST['vuln_desc_primary'], $_POST['vuln_desc_primary_bool']);             }
  if (isset($_POST['vuln_desc_secondary'])           && ($_POST['vuln_desc_secondary']          != '-- any --')) { $vulns->filterVulnDescSecondary($_POST['vuln_desc_secondary'], $_POST['vuln_desc_secondary_bool']);           }
  
  // propagate sort parameters
  if (isset($_POST['sort_params'])) { $vulns->setOrder($_POST['sort_params']); }

}

// reset the filters
else {

  $_POST['vuln_type']                     = NULL;
  $_POST['vuln_desc_primary']             = NULL;
  $_POST['vuln_desc_secondary']           = NULL;

  $_POST['vuln_type_bool']                = NULL;
  $_POST['vuln_desc_primary_bool']        = NULL;
  $_POST['vuln_desc_secondary_bool']      = NULL;

}

// retrieve the overall list size (for the pager)
$_PAGER->setListSize($vulns->getListSize());

// propagate pager state to new pager
if (isset($_POST['current_page'])) { $_PAGER->setCurrentPage($_POST['current_page']); }
if (isset($_POST['page_size']))    { $_PAGER->setPageSize($_POST['page_size']);       }

// update the pager state to new pager
switch ($_POST['form_action']) {

 // reset the page on a filter request
 case 'filter'     : $_PAGER->page_first(); break;

 // handle pager requests
 case 'page_jump'  : $_PAGER->page_jump($_POST['page_jump']); break;
 case 'page_first' : $_PAGER->page_first(); break;
 case 'page_prev'  : $_PAGER->page_prev();  break;
 case 'page_next'  : $_PAGER->page_next();  break;
 case 'page_last'  : $_PAGER->page_last();  break;
 case 'page_size'  : $_PAGER->setPageSize($_POST['page_size']); $_PAGER->page_first(); break;

} // end switch

// retrieve the appropriate page from the list (pager provides offset and page size)
$vuln_list = $vulns->getList($_PAGER->getPageOffset(), $_PAGER->getPageSize());

// encrypt the ids
for ($row = 0; $row < count($vuln_list); $row++) {

  $vuln_list [$row]['vuln_seq'] = $_E->encrypt($vuln_list [$row]['vuln_seq']);

}


// --------------------------------------------------------------------
//
// TEMPLATE POPULATION
//
// --------------------------------------------------------------------

// header
$_TEMPLATE->assign('this_page',   'vuln_list.php');
$_TEMPLATE->assign('this_title',  'VULN > list');
$_TEMPLATE->assign('menu_header', 'vuln');

// filter variables
$_TEMPLATE->assign('filter_standalone',             0);
$_TEMPLATE->assign('bool_options',                  $bool_options);
$_TEMPLATE->assign('bool_values',                   $bool_values);

$_TEMPLATE->assign('unique_types',                     $uniques['vuln_type']);
$_TEMPLATE->assign('unique_desc_primaries',             $uniques['vuln_desc_primary']);
$_TEMPLATE->assign('unique_desc_secondaries',           $uniques['vuln_desc_secondary']);

$_TEMPLATE->assign('selected_type',                     $_POST['vuln_type']);
$_TEMPLATE->assign('selected_desc_primary',             $_POST['vuln_desc_primary']);
$_TEMPLATE->assign('selected_desc_secondary',           $_POST['vuln_desc_secondary']);

$_TEMPLATE->assign('selected_type_bool',                $_POST['vuln_type_bool']);
$_TEMPLATE->assign('selected_desc_primary_bool',        $_POST['vuln_desc_primary_bool']);
$_TEMPLATE->assign('selected_desc_secondary_bool',      $_POST['vuln_desc_secondary_bool']);

// pager variables
$_TEMPLATE->assign('pager_standalone',              0);
$_TEMPLATE->assign('current_page',                  $_PAGER->getCurrentPage());
$_TEMPLATE->assign('page_size',                     $_PAGER->getPageSize());
$_TEMPLATE->assign('page_jumps',                    $_PAGER->getPageJumps());
$_TEMPLATE->assign('page_max',                      $_PAGER->getLastPage());
$_TEMPLATE->assign('page_sizes',                    $_PAGER->getPageSizes());
$_TEMPLATE->assign('list_start',                    $_PAGER->getListStart());
$_TEMPLATE->assign('list_end',                      $_PAGER->getListEnd());
$_TEMPLATE->assign('list_size',                     $_PAGER->getListSize());

// sorting variables
$_TEMPLATE->assign('sort_standalone',               0);
$_TEMPLATE->assign('sort_params',                   $_POST['sort_params']);

// vuln_list
$_TEMPLATE->assign('vuln_list', $vuln_list);


// --------------------------------------------------------------------
// 
// PAGE DISPLAY
// 
// --------------------------------------------------------------------

// display header and menu
require_once('header.php');
require_once('menu.php');

// display the content
$_TEMPLATE->display('vuln_filter.tpl');
$_TEMPLATE->display('pager.tpl');
$_TEMPLATE->display('vuln_list.tpl');

// display footer
require_once('footer.php');

?>