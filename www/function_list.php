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

require_once('../lib/FunctionsList.class.php');
require_once('../lib/Pager.class.php');


// --------------------------------------------------------------------
// 
// FORM HANLDING
// 
// --------------------------------------------------------------------

// handle the form action
switch ($_POST['form_action']) {

case 'V': header("Location: ".$_CONFIG->APP_URL()."function_view.php");   break;
case 'U': header("Location: ".$_CONFIG->APP_URL()."function_update.php"); break;	
case 'D': header("Location: ".$_CONFIG->APP_URL()."function_delete.php"); break;

default: break;
	
} // switch form_action


// --------------------------------------------------------------------
//
// DATA RETRIEVAL / MANIPULATION
//
// --------------------------------------------------------------------

// create the function list for the listing
$functions = new FunctionsList($_DB);

// select desired list parameters (column headers)
$functions->getFunctionId();
$functions->getFunctionName();
$functions->getFunctionScreen();
$functions->getFunctionAction();
$functions->getFunctionDesc();
$functions->getFunctionOpen();

// retrieve unique items in each column for filter options
$uniques = $functions->getUniques();

// prepend "any" option to unique filter options
$keys = array_keys($uniques);
while ($key = array_pop($keys)) { $uniques[$key] = array('-- any --'=>'-- any --') + $uniques[$key]; }

// initialize our boolean options for filter boolean options
$bool_options = array('is', 'is not');
$bool_values  = array(1, 0);

// add filters and sort information if not asked to reset
if ($_POST['form_action'] != 'reset') {

  // add filters to list
  if (isset($_POST['function_name'])             && ($_POST['function_name']            != '-- any --')) { $functions->filterFunctionName($_POST['function_name'], $_POST['function_name_bool']);             }
  if (isset($_POST['function_screen'])           && ($_POST['function_screen']          != '-- any --')) { $functions->filterFunctionScreen($_POST['function_screen'], $_POST['function_screen_bool']);           }
  if (isset($_POST['function_action'])           && ($_POST['function_action']          != '-- any --')) { $functions->filterFunctionAction($_POST['function_action'], $_POST['function_action_bool']);           }
  if (isset($_POST['function_desc'])             && ($_POST['function_desc']            != '-- any --')) { $functions->filterFunctionDesc($_POST['function_desc'], $_POST['function_desc_bool']);             }
  if (isset($_POST['function_open'])             && ($_POST['function_open']            != '-- any --')) { $functions->filterFunctionOpen($_POST['function_open'], $_POST['function_open_bool']);             }
  
  // propagate sort parameters
  if (isset($_POST['sort_params'])) { $functions->setOrder($_POST['sort_params']); }

}

// reset the filters
else {

  $_POST['function_name']             = NULL;
  $_POST['function_screen']           = NULL;
  $_POST['function_action']           = NULL;
  $_POST['function_desc']             = NULL;
  $_POST['function_open']             = NULL;

  $_POST['function_name_bool']        = NULL;
  $_POST['function_screen_bool']      = NULL;
  $_POST['function_action_bool']      = NULL;
  $_POST['function_desc_bool']        = NULL;
  $_POST['function_open_bool']        = NULL;

}

// retrieve the overall list size (for the pager)
$_PAGER->setListSize($functions->getListSize());

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
$function_list = $functions->getList($_PAGER->getPageOffset(), $_PAGER->getPageSize());

// encrypt the ids
for ($row = 0; $row < count($function_list); $row++) {

  $function_list [$row]['function_id'] = $_E->encrypt($function_list [$row]['function_id']);

}


// --------------------------------------------------------------------
//
// TEMPLATE POPULATION
//
// --------------------------------------------------------------------

// header
$_TEMPLATE->assign('this_page',  'function_list.php');
$_TEMPLATE->assign('this_title', 'FUNCTION > list');
$_TEMPLATE->assign('menu_header', 'function');

// filter variables
$_TEMPLATE->assign('filter_standalone',             0);
$_TEMPLATE->assign('bool_options',                  $bool_options);
$_TEMPLATE->assign('bool_values',                   $bool_values);

$_TEMPLATE->assign('unique_names',             $uniques['function_name']);
$_TEMPLATE->assign('unique_screens',           $uniques['function_screen']);
$_TEMPLATE->assign('unique_actions',           $uniques['function_action']);
$_TEMPLATE->assign('unique_descs',             $uniques['function_desc']);
$_TEMPLATE->assign('unique_opens',             $uniques['function_open']);

$_TEMPLATE->assign('selected_name',             $_POST['function_name']);
$_TEMPLATE->assign('selected_screen',           $_POST['function_screen']);
$_TEMPLATE->assign('selected_action',           $_POST['function_action']);
$_TEMPLATE->assign('selected_desc',             $_POST['function_desc']);
$_TEMPLATE->assign('selected_open',             $_POST['function_open']);

$_TEMPLATE->assign('selected_name_bool',        $_POST['function_name_bool']);
$_TEMPLATE->assign('selected_screen_bool',      $_POST['function_screen_bool']);
$_TEMPLATE->assign('selected_action_bool',      $_POST['function_action_bool']);
$_TEMPLATE->assign('selected_desc_bool',        $_POST['function_desc_bool']);
$_TEMPLATE->assign('selected_open_bool',        $_POST['function_open_bool']);

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

// function_list
$_TEMPLATE->assign('function_list', $function_list);


// --------------------------------------------------------------------
// 
// PAGE DISPLAY
// 
// --------------------------------------------------------------------

// display header and menu
require_once('header.php');
require_once('menu.php');

// display the content
$_TEMPLATE->display('function_filter.tpl');
$_TEMPLATE->display('pager.tpl');
$_TEMPLATE->display('function_list.tpl');

// display footer
require_once('footer.php');

?>