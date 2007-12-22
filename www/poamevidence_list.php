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

require_once('../lib/PoamevidenceList.class.php');
require_once('../lib/Pager.class.php');


// --------------------------------------------------------------------
// 
// FORM HANLDING
// 
// --------------------------------------------------------------------

// handle the form action
switch ($_POST['form_action']) {

case 'V': header("Location: ".$_CONFIG->APP_URL()."poamevidence_view.php");   break;
case 'U': header("Location: ".$_CONFIG->APP_URL()."poamevidence_update.php"); break;	
case 'D': header("Location: ".$_CONFIG->APP_URL()."poamevidence_delete.php"); break;

default: break;
	
} // switch form_action


// --------------------------------------------------------------------
//
// DATA RETRIEVAL / MANIPULATION
//
// --------------------------------------------------------------------

// create the poamevidence list for the listing
$poamevidences = new PoamevidenceList($_DB);

// select desired list parameters (column headers)
$poamevidences->getEvId();
$poamevidences->getEvSubmission();
$poamevidences->getEvSubmittedBy();
$poamevidences->getEvDateSubmitted();
$poamevidences->getPoamId();

// retrieve unique items in each column for filter options
$uniques = $poamevidences->getUniques();

// prepend "any" option to unique filter options
$keys = array_keys($uniques);
while ($key = array_pop($keys)) { $uniques[$key] = array('-- any --'=>'-- any --') + $uniques[$key]; }

// initialize our boolean options for filter boolean options
$bool_options = array('is', 'is not');
$bool_values  = array(1, 0);

// add filters and sort information if not asked to reset
if ($_POST['form_action'] != 'reset') {

  // add filters to list
  if (isset($_POST['poam_id'])                     && ($_POST['poam_id']                    != '-- any --')) { $poamevidences->filterPoamId($_POST['poam_id'], $_POST['poam_id_bool']);                     }
  if (isset($_POST['ev_submission'])               && ($_POST['ev_submission']              != '-- any --')) { $poamevidences->filterEvSubmission($_POST['ev_submission'], $_POST['ev_submission_bool']);               }
  if (isset($_POST['ev_submitted_by'])             && ($_POST['ev_submitted_by']            != '-- any --')) { $poamevidences->filterEvSubmittedBy($_POST['ev_submitted_by'], $_POST['ev_submitted_by_bool']);             }
  if (isset($_POST['ev_date_submitted'])           && ($_POST['ev_date_submitted']          != '-- any --')) { $poamevidences->filterEvDateSubmitted($_POST['ev_date_submitted'], $_POST['ev_date_submitted_bool']);           }
  
  // propagate sort parameters
  if (isset($_POST['sort_params'])) { $poamevidences->setOrder($_POST['sort_params']); }

}

// reset the filters
else {

  $_POST['poam_id']                     = NULL;
  $_POST['ev_submission']               = NULL;
  $_POST['ev_submitted_by']             = NULL;
  $_POST['ev_date_submitted']           = NULL;

  $_POST['poam_id_bool']                = NULL;
  $_POST['ev_submission_bool']          = NULL;
  $_POST['ev_submitted_by_bool']        = NULL;
  $_POST['ev_date_submitted_bool']      = NULL;

}

// retrieve the overall list size (for the pager)
$_PAGER->setListSize($poamevidences->getListSize());

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
$poamevidence_list = $poamevidences->getList($_PAGER->getPageOffset(), $_PAGER->getPageSize());

// encrypt the ids
for ($row = 0; $row < count($poamevidence_list); $row++) {

  $poamevidence_list [$row]['ev_id'] = $_E->encrypt($poamevidence_list [$row]['ev_id']);

}


// --------------------------------------------------------------------
//
// TEMPLATE POPULATION
//
// --------------------------------------------------------------------

// header
$_TEMPLATE->assign('this_page',  'poamevidence_list.php');
$_TEMPLATE->assign('this_title', 'POAMEVIDENCE > list');
$_TEMPLATE->assign('menu_header', 'poam evidence');

// filter variables
$_TEMPLATE->assign('filter_standalone',             0);
$_TEMPLATE->assign('bool_options',                  $bool_options);
$_TEMPLATE->assign('bool_values',                   $bool_values);

$_TEMPLATE->assign('poam_ids',                     $uniques['poam_id']);
$_TEMPLATE->assign('ev_submissions',               $uniques['ev_submission']);
$_TEMPLATE->assign('ev_submitted_bies',             $uniques['ev_submitted_by']);
$_TEMPLATE->assign('ev_date_submitteds',           $uniques['ev_date_submitted']);

$_TEMPLATE->assign('poam_id',                     $_POST['poam_id']);
$_TEMPLATE->assign('ev_submission',               $_POST['ev_submission']);
$_TEMPLATE->assign('ev_submitted_by',             $_POST['ev_submitted_by']);
$_TEMPLATE->assign('ev_date_submitted',           $_POST['ev_date_submitted']);

$_TEMPLATE->assign('poam_id_bool',                $_POST['poam_id_bool']);
$_TEMPLATE->assign('ev_submission_bool',          $_POST['ev_submission_bool']);
$_TEMPLATE->assign('ev_submitted_by_bool',        $_POST['ev_submitted_by_bool']);
$_TEMPLATE->assign('ev_date_submitted_bool',      $_POST['ev_date_submitted_bool']);

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

// poamevidence_list
$_TEMPLATE->assign('poamevidence_list', $poamevidence_list);


// --------------------------------------------------------------------
// 
// PAGE DISPLAY
// 
// --------------------------------------------------------------------

// display header and menu
require_once('header.php');
require_once('menu.php');

// display the content
$_TEMPLATE->display('poamevidence_filter.tpl');
$_TEMPLATE->display('pager.tpl');
$_TEMPLATE->display('poamevidence_list.tpl');

// display footer
require_once('footer.php');

?>