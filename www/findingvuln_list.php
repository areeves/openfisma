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

require_once('../lib/FindingVulnList.class.php');
require_once('../lib/Pager.class.php');


// --------------------------------------------------------------------
// 
// FORM HANLDING
// 
// --------------------------------------------------------------------

// handle the form action
switch ($_POST['form_action']) {

case 'V': header("Location: ".$_CONFIG->APP_URL()."findingvuln_view.php");   break;
case 'U': header("Location: ".$_CONFIG->APP_URL()."findingvuln_update.php"); break;	
case 'D': header("Location: ".$_CONFIG->APP_URL()."findingvuln_delete.php"); break;

default: break;
	
} // switch form_action


// --------------------------------------------------------------------
//
// DATA RETRIEVAL / MANIPULATION
//
// --------------------------------------------------------------------

// create the findingvuln list for the listing
$findingvulns = new FindingvulnList($_DB);

// select desired list parameters (column headers)
$findingvulns->getVulnSeq();
$findingvulns->getVulnType();
$findingvulns->getFindingId();

// retrieve unique items in each column for filter options
$uniques = $findingvulns->getUniques();

// prepend "any" option to unique filter options
$keys = array_keys($uniques);
while ($key = array_pop($keys)) { array_unshift($uniques[$key], '-- any --'); }

// initialize our boolean options for filter boolean options
$bool_options = array('is', 'is not');
$bool_values  = array(1, 0);

// add filters and sort information if not asked to reset
if ($_POST['form_action'] != 'reset') {

  // add filters to list
  if (isset($_POST['vuln_type'])            && ($_POST['vuln_type']           != '-- any --')) { $findingvulns->filterVulnType($_POST['vuln_type'], $_POST['vuln_type_bool']);            }
  if (isset($_POST['vuln_seq'])             && ($_POST['vuln_seq']            != '-- any --')) { $findingvulns->filterVulnSeq($_POST['vuln_seq'], $_POST['vuln_seq_bool']);             }
  if (isset($_POST['finding_id'])           && ($_POST['finding_id']          != '-- any --')) { $findingvulns->filterFindingId($_POST['finding_id'], $_POST['finding_id_bool']);           }
  
  // propagate sort parameters
  if (isset($_POST['sort_params'])) { $findingvulns->setOrder($_POST['sort_params']); }

}

// reset the filters
else {

  $_POST['vuln_type']            = NULL;
  $_POST['vuln_seq']             = NULL;
  $_POST['finding_id']           = NULL;

  $_POST['vuln_type_bool']       = NULL;
  $_POST['vuln_seq_bool']        = NULL;
  $_POST['finding_id_bool']      = NULL;

}

// retrieve the overall list size (for the pager)
$_PAGER->setListSize($findingvulns->getListSize());

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
$findingvuln_list = $findingvulns->getList($_PAGER->getPageOffset(), $_PAGER->getPageSize());

// encrypt the ids
for ($row = 0; $row < count($findingvuln_list); $row++) {

    foreach ($findingvuln_list [$row] as $k=>$v){
        
    }
  $findingvuln_list [$row]['findingvuln_id'] = $_E->encrypt(serialize($findingvuln_list [$row]));

}


// --------------------------------------------------------------------
//
// TEMPLATE POPULATION
//
// --------------------------------------------------------------------

// header
$_TEMPLATE->assign('this_page',  'findingvuln_list.php');
$_TEMPLATE->assign('this_title', 'FINDINGVULN > list');
$_TEMPLATE->assign('menu_header', 'finding vuln');

// filter variables
$_TEMPLATE->assign('filter_standalone',             0);
$_TEMPLATE->assign('bool_options',                  $bool_options);
$_TEMPLATE->assign('bool_values',                   $bool_values);

$_TEMPLATE->assign('vuln_types',            $uniques['vuln_type']);
$_TEMPLATE->assign('vuln_seqs',             $uniques['vuln_seq']);
$_TEMPLATE->assign('finding_ids',           $uniques['finding_id']);

$_TEMPLATE->assign('vuln_type',            $_POST['vuln_type']);
$_TEMPLATE->assign('vuln_seq',             $_POST['vuln_seq']);
$_TEMPLATE->assign('finding_id',           $_POST['finding_id']);

$_TEMPLATE->assign('vuln_type_bool',       $_POST['vuln_type_bool']);
$_TEMPLATE->assign('vuln_seq_bool',        $_POST['vuln_seq_bool']);
$_TEMPLATE->assign('finding_id_bool',      $_POST['finding_id_bool']);

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

// findingvuln_list
$_TEMPLATE->assign('findingvuln_list', $findingvuln_list);


// --------------------------------------------------------------------
// 
// PAGE DISPLAY
// 
// --------------------------------------------------------------------

// display header and menu
require_once('header.php');
require_once('menu.php');

// display the content
$_TEMPLATE->display('findingvuln_filter.tpl');
$_TEMPLATE->display('pager.tpl');
$_TEMPLATE->display('findingvuln_list.tpl');

// display footer
require_once('footer.php');

?>