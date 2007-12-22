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

require_once('../lib/SystemList.class.php');
require_once('../lib/Pager.class.php');


// --------------------------------------------------------------------
// 
// FORM HANLDING
// 
// --------------------------------------------------------------------

// handle the form action
switch ($_POST['form_action']) {

case 'V': header("Location: ".$_CONFIG->APP_URL()."system_view.php");   break;
case 'U': header("Location: ".$_CONFIG->APP_URL()."system_update.php"); break;	
case 'D': header("Location: ".$_CONFIG->APP_URL()."system_delete.php"); break;

default: break;
	
} // switch form_action


// --------------------------------------------------------------------
//
// DATA RETRIEVAL / MANIPULATION
//
// --------------------------------------------------------------------

// create the system list for the listing
$systems = new SystemList($_DB);

// select desired list parameters (column headers)
$systems->getSystemId();
$systems->getSystemName();
$systems->getSystemType();
$systems->getSystemConfidentiality();
$systems->getSystemIntegrity();
$systems->getSystemAvailability();

// retrieve unique items in each column for filter options
$uniques = $systems->getUniques();

// prepend "any" option to unique filter options
$keys = array_keys($uniques);
while ($key = array_pop($keys)) { $uniques[$key] = array('-- any --'=>'-- any --') + $uniques[$key]; }

// initialize our boolean options for filter boolean options
$bool_options = array('is', 'is not');
$bool_values  = array(1, 0);

// add filters and sort information if not asked to reset
if ($_POST['form_action'] != 'reset') {

  // add filters to list
  if (isset($_POST['system_name'])                      && ($_POST['system_name']                     != '-- any --')) { $systems->filterSystemName($_POST['system_name'], $_POST['system_name_bool']);                      }
  if (isset($_POST['system_type'])                      && ($_POST['system_type']                     != '-- any --')) { $systems->filterSystemType($_POST['system_type'], $_POST['system_type_bool']);                      }
  if (isset($_POST['system_confidentiality'])           && ($_POST['system_confidentiality']          != '-- any --')) { $systems->filterSystemConfidentiality($_POST['system_confidentiality'], $_POST['system_confidentiality_bool']);           }
  if (isset($_POST['system_integrity'])                 && ($_POST['system_integrity']                != '-- any --')) { $systems->filterSystemIntegrity($_POST['system_integrity'], $_POST['system_integrity_bool']);                 }
  if (isset($_POST['system_availability'])              && ($_POST['system_availability']             != '-- any --')) { $systems->filterSystemAvailability($_POST['system_availability'], $_POST['system_availability_bool']);              }
  
  // propagate sort parameters
  if (isset($_POST['sort_params'])) { $systems->setOrder($_POST['sort_params']); }

}

// reset the filters
else {

  $_POST['system_name']                      = NULL;
  $_POST['system_type']                      = NULL;
  $_POST['system_confidentiality']           = NULL;
  $_POST['system_integrity']                 = NULL;
  $_POST['system_availability']              = NULL;

  $_POST['system_name_bool']                 = NULL;
  $_POST['system_type_bool']                 = NULL;
  $_POST['system_confidentiality_bool']      = NULL;
  $_POST['system_integrity_bool']            = NULL;
  $_POST['system_availability_bool']         = NULL;

}

// retrieve the overall list size (for the pager)
$_PAGER->setListSize($systems->getListSize());

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
$system_list = $systems->getList($_PAGER->getPageOffset(), $_PAGER->getPageSize());

// encrypt the ids
for ($row = 0; $row < count($system_list); $row++) {

  $system_list [$row]['system_id'] = $_E->encrypt($system_list [$row]['system_id']);

}


// --------------------------------------------------------------------
//
// TEMPLATE POPULATION
//
// --------------------------------------------------------------------

// header
$_TEMPLATE->assign('this_page',  'system_list.php');
$_TEMPLATE->assign('this_title', 'SYSTEM > list');
$_TEMPLATE->assign('menu_header', 'system');

// filter variables
$_TEMPLATE->assign('filter_standalone',             0);
$_TEMPLATE->assign('bool_options',                  $bool_options);
$_TEMPLATE->assign('bool_values',                   $bool_values);

$_TEMPLATE->assign('unique_names',                      $uniques['system_name']);
$_TEMPLATE->assign('unique_types',                      $uniques['system_type']);
$_TEMPLATE->assign('unique_confidentialities',           $uniques['system_confidentiality']);
$_TEMPLATE->assign('unique_integrities',                 $uniques['system_integrity']);
$_TEMPLATE->assign('unique_availabilities',              $uniques['system_availability']);

$_TEMPLATE->assign('selected_name',                      $_POST['system_name']);
$_TEMPLATE->assign('selected_type',                      $_POST['system_type']);
$_TEMPLATE->assign('selected_confidentiality',           $_POST['system_confidentiality']);
$_TEMPLATE->assign('selected_integrity',                 $_POST['system_integrity']);
$_TEMPLATE->assign('selected_availability',              $_POST['system_availability']);

$_TEMPLATE->assign('selected_name_bool',                 $_POST['system_name_bool']);
$_TEMPLATE->assign('selected_type_bool',                 $_POST['system_type_bool']);
$_TEMPLATE->assign('selected_confidentiality_bool',      $_POST['system_confidentiality_bool']);
$_TEMPLATE->assign('selected_integrity_bool',            $_POST['system_integrity_bool']);
$_TEMPLATE->assign('selected_availability_bool',         $_POST['system_availability_bool']);

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

// system_list
$_TEMPLATE->assign('system_list', $system_list);


// --------------------------------------------------------------------
// 
// PAGE DISPLAY
// 
// --------------------------------------------------------------------

// display header and menu
require_once('header.php');
require_once('menu.php');

// display the content
$_TEMPLATE->display('system_filter.tpl');
$_TEMPLATE->display('pager.tpl');
$_TEMPLATE->display('system_list.tpl');

// display footer
require_once('footer.php');

?>