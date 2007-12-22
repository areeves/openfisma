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

require_once('../lib/BlscrList.class.php');
require_once('../lib/Pager.class.php');


// --------------------------------------------------------------------
// 
// FORM HANLDING
// 
// --------------------------------------------------------------------

// handle the form action
switch ($_POST['form_action']) {

case 'V': header("Location: ".$_CONFIG->APP_URL()."blscr_view.php");   break;
case 'U': header("Location: ".$_CONFIG->APP_URL()."blscr_update.php"); break;	
case 'D': header("Location: ".$_CONFIG->APP_URL()."blscr_delete.php"); break;

default: break;
	
} // switch form_action


// --------------------------------------------------------------------
//
// DATA RETRIEVAL / MANIPULATION
//
// --------------------------------------------------------------------

// create the blscr list for the listing
$blscrs = new BlscrList($_DB);

// select desired list parameters (column headers)
$blscrs->getBlscrNumber();
$blscrs->getBlscrClass();
$blscrs->getBlscrSubclass();
$blscrs->getBlscrFamily();

// retrieve unique items in each column for filter options
$uniques = $blscrs->getUniques();

// prepend "any" option to unique filter options
$keys = array_keys($uniques);
while ($key = array_pop($keys)) { $uniques[$key] = array('-- any --'=>'-- any --') + $uniques[$key]; }

// initialize our boolean options for filter boolean options
$bool_options = array('is', 'is not');
$bool_values  = array(1, 0);

// add filters and sort information if not asked to reset
if ($_POST['form_action'] != 'reset') {

  // add filters to list
  if (isset($_POST['blscr_class'])              && ($_POST['blscr_class']             != '-- any --')) { $blscrs->filterBlscrClass($_POST['blscr_class'], $_POST['blscr_class_bool']);              }
  if (isset($_POST['blscr_subclass'])           && ($_POST['blscr_subclass']          != '-- any --')) { $blscrs->filterBlscrSubclass($_POST['blscr_subclass'], $_POST['blscr_subclass_bool']);           }
  if (isset($_POST['blscr_family'])             && ($_POST['blscr_family']            != '-- any --')) { $blscrs->filterBlscrFamily($_POST['blscr_family'], $_POST['blscr_family_bool']);             }
  
  // propagate sort parameters
  if (isset($_POST['sort_params'])) { $blscrs->setOrder($_POST['sort_params']); }

}

// reset the filters
else {

  $_POST['blscr_class']              = NULL;
  $_POST['blscr_subclass']           = NULL;
  $_POST['blscr_family']             = NULL;

  $_POST['blscr_class_bool']         = NULL;
  $_POST['blscr_subclass_bool']      = NULL;
  $_POST['blscr_family_bool']        = NULL;

}

// retrieve the overall list size (for the pager)
$_PAGER->setListSize($blscrs->getListSize());

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
$blscr_list = $blscrs->getList($_PAGER->getPageOffset(), $_PAGER->getPageSize());

// encrypt the ids
for ($row = 0; $row < count($blscr_list); $row++) {

  $blscr_list [$row]['blscr_number'] = $_E->encrypt($blscr_list [$row]['blscr_number']);

}


// --------------------------------------------------------------------
//
// TEMPLATE POPULATION
//
// --------------------------------------------------------------------

// header
$_TEMPLATE->assign('this_page',  'blscr_list.php');
$_TEMPLATE->assign('this_title', 'BLSCR > list');
$_TEMPLATE->assign('menu_header', 'blscr');

// filter variables
$_TEMPLATE->assign('filter_standalone',             0);
$_TEMPLATE->assign('bool_options',                  $bool_options);
$_TEMPLATE->assign('bool_values',                   $bool_values);

$_TEMPLATE->assign('unique_classs',              $uniques['blscr_class']);
$_TEMPLATE->assign('unique_subclasss',           $uniques['blscr_subclass']);
$_TEMPLATE->assign('unique_families',             $uniques['blscr_family']);

$_TEMPLATE->assign('selected_class',              $_POST['blscr_class']);
$_TEMPLATE->assign('selected_subclass',           $_POST['blscr_subclass']);
$_TEMPLATE->assign('selected_family',             $_POST['blscr_family']);

$_TEMPLATE->assign('selected_class_bool',         $_POST['blscr_class_bool']);
$_TEMPLATE->assign('selected_subclass_bool',      $_POST['blscr_subclass_bool']);
$_TEMPLATE->assign('selected_family_bool',        $_POST['blscr_family_bool']);

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

// blscr_list
$_TEMPLATE->assign('blscr_list', $blscr_list);


// --------------------------------------------------------------------
// 
// PAGE DISPLAY
// 
// --------------------------------------------------------------------

// display header and menu
require_once('header.php');
require_once('menu.php');

// display the content
$_TEMPLATE->display('blscr_filter.tpl');
$_TEMPLATE->display('pager.tpl');
$_TEMPLATE->display('blscr_list.tpl');

// display footer
require_once('footer.php');

?>