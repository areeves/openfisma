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

require_once('../lib/UserList.class.php');
require_once('../lib/Pager.class.php');


// --------------------------------------------------------------------
// 
// FORM HANLDING
// 
// --------------------------------------------------------------------

// handle the form action
switch ($_POST['form_action']) {

case 'V': header("Location: ".$_CONFIG->APP_URL()."user_view.php");   break;
case 'U': header("Location: ".$_CONFIG->APP_URL()."user_update.php"); break;	
case 'D': header("Location: ".$_CONFIG->APP_URL()."user_delete.php"); break;

default: break;
	
} // switch form_action


// --------------------------------------------------------------------
//
// DATA RETRIEVAL / MANIPULATION
//
// --------------------------------------------------------------------

// create the user list for the listing
$users = new UserList($_DB);

// select desired list parameters (column headers)
$users->getUserId();
$users->getUserNameFirst();
$users->getUserNameLast();
$users->getUserName();

// retrieve unique items in each column for filter options
$uniques = $users->getUniques();

// prepend "any" option to unique filter options
$keys = array_keys($uniques);
while ($key = array_pop($keys)) { $uniques[$key] = array('-- any --'=>'-- any --') + $uniques[$key]; }

// initialize our boolean options for filter boolean options
$bool_options = array('is', 'is not');
$bool_values  = array(1, 0);

// add filters and sort information if not asked to reset
if ($_POST['form_action'] != 'reset') {

  // add filters to list
  if (isset($_POST['user_name_first'])           && ($_POST['user_name_first']          != '-- any --')) { $users->filterUserNameFirst($_POST['user_name_first'], $_POST['user_name_first_bool']);           }
  if (isset($_POST['user_name_last'])            && ($_POST['user_name_last']           != '-- any --')) { $users->filterUserNameLast($_POST['user_name_last'], $_POST['user_name_last_bool']);            }
  if (isset($_POST['user_name'])                 && ($_POST['user_name']                != '-- any --')) { $users->filterUserName($_POST['user_name'], $_POST['user_name_bool']);                 }
  
  // propagate sort parameters
  if (isset($_POST['sort_params'])) { $users->setOrder($_POST['sort_params']); }

}

// reset the filters
else {

  $_POST['user_name_first']           = NULL;
  $_POST['user_name_last']            = NULL;
  $_POST['user_name']                 = NULL;

  $_POST['user_name_first_bool']      = NULL;
  $_POST['user_name_last_bool']       = NULL;
  $_POST['user_name_bool']            = NULL;

}

// retrieve the overall list size (for the pager)
$_PAGER->setListSize($users->getListSize());

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
$user_list = $users->getList($_PAGER->getPageOffset(), $_PAGER->getPageSize());

// encrypt the ids
for ($row = 0; $row < count($user_list); $row++) {

  $user_list [$row]['user_id'] = $_E->encrypt($user_list [$row]['user_id']);

}


// --------------------------------------------------------------------
//
// TEMPLATE POPULATION
//
// --------------------------------------------------------------------

// header
$_TEMPLATE->assign('this_page',  'user_list.php');
$_TEMPLATE->assign('this_title', 'USER > list');
$_TEMPLATE->assign('menu_header', 'user');

// filter variables
$_TEMPLATE->assign('filter_standalone',             0);
$_TEMPLATE->assign('bool_options',                  $bool_options);
$_TEMPLATE->assign('bool_values',                   $bool_values);

$_TEMPLATE->assign('unique_name_firsts',           $uniques['user_name_first']);
$_TEMPLATE->assign('unique_name_lasts',            $uniques['user_name_last']);
$_TEMPLATE->assign('unique_names',                 $uniques['user_name']);

$_TEMPLATE->assign('selected_name_first',           $_POST['user_name_first']);
$_TEMPLATE->assign('selected_name_last',            $_POST['user_name_last']);
$_TEMPLATE->assign('selected_name',                 $_POST['user_name']);

$_TEMPLATE->assign('selected_name_first_bool',      $_POST['user_name_first_bool']);
$_TEMPLATE->assign('selected_name_last_bool',       $_POST['user_name_last_bool']);
$_TEMPLATE->assign('selected_name_bool',            $_POST['user_name_bool']);

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

// user_list
$_TEMPLATE->assign('user_list', $user_list);


// --------------------------------------------------------------------
// 
// PAGE DISPLAY
// 
// --------------------------------------------------------------------

// display header and menu
require_once('header.php');
require_once('menu.php');

// display the content
$_TEMPLATE->display('user_filter.tpl');
$_TEMPLATE->display('pager.tpl');
$_TEMPLATE->display('user_list.tpl');

// display footer
require_once('footer.php');

?>