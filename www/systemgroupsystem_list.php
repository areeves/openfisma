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

require_once('../lib/SystemGroupSystemList.class.php');
require_once('../lib/Pager.class.php');


// --------------------------------------------------------------------
// 
// FORM HANLDING
// 
// --------------------------------------------------------------------

// handle the form action
switch ($_POST['form_action']) {

case 'V': header("Location: ".$_CONFIG->APP_URL()."systemgroupsystem_view.php");   break;
case 'U': header("Location: ".$_CONFIG->APP_URL()."systemgroupsystem_update.php"); break;	
case 'D': header("Location: ".$_CONFIG->APP_URL()."systemgroupsystem_delete.php"); break;

default: break;
	
} // switch form_action


// --------------------------------------------------------------------
//
// DATA RETRIEVAL / MANIPULATION
//
// --------------------------------------------------------------------

// create the systemgroupsystem list for the listing
$systemgroupsystems = new SystemGroupSystemList($_DB);

// select desired list parameters (column headers)
$systemgroupsystems->getSysgroupId();
$systemgroupsystems->getSystemId();

// retrieve unique items in each column for filter options
$uniques = $systemgroupsystems->getUniques();

// prepend "any" option to unique filter options
$keys = array_keys($uniques);
while ($key = array_pop($keys)) { array_unshift($uniques[$key], '-- any --'); }

// initialize our boolean options for filter boolean options
$bool_options = array('is', 'is not');
$bool_values  = array(1, 0);

// add filters and sort information if not asked to reset
if ($_POST['form_action'] != 'reset') {

  // add filters to list
  if (isset($_POST['sysgroup_id'])           && ($_POST['sysgroup_id']          != '-- any --')) { $systemgroupsystems->filterSysgroupId($_POST['sysgroup_id'], $_POST['sysgroup_id_bool']);           }
  if (isset($_POST['system_id'])             && ($_POST['system_id']            != '-- any --')) { $systemgroupsystems->filterSystemId($_POST['system_id'], $_POST['system_id_bool']);             }
  
  // propagate sort parameters
  if (isset($_POST['sort_params'])) { $systemgroupsystems->setOrder($_POST['sort_params']); }

}

// reset the filters
else {

  $_POST['sysgroup_id']           = NULL;
  $_POST['system_id']             = NULL;

  $_POST['sysgroup_id_bool']      = NULL;
  $_POST['system_id_bool']        = NULL;

}

// retrieve the overall list size (for the pager)
$_PAGER->setListSize($systemgroupsystems->getListSize());

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
$systemgroupsystem_list = $systemgroupsystems->getList($_PAGER->getPageOffset(), $_PAGER->getPageSize());

// encrypt the ids
for ($row = 0; $row < count($systemgroupsystem_list); $row++) {

  $systemgroupsystem_list [$row]['systemgroupsystem_id'] = $_E->encrypt(serialize($systemgroupsystem_list [$row]));

}


// --------------------------------------------------------------------
//
// TEMPLATE POPULATION
//
// --------------------------------------------------------------------

// header
$_TEMPLATE->assign('this_page',  'systemgroupsystem_list.php');
$_TEMPLATE->assign('this_title', 'SYSTEMGROUPSYSTEM > list');
$_TEMPLATE->assign('menu_header', 'system group system');

// filter variables
$_TEMPLATE->assign('filter_standalone',             0);
$_TEMPLATE->assign('bool_options',                  $bool_options);
$_TEMPLATE->assign('bool_values',                   $bool_values);

$_TEMPLATE->assign('sysgroup_ids',           $uniques['sysgroup_id']);
$_TEMPLATE->assign('system_ids',             $uniques['system_id']);

$_TEMPLATE->assign('sysgroup_id',           $_POST['sysgroup_id']);
$_TEMPLATE->assign('system_id',             $_POST['system_id']);

$_TEMPLATE->assign('sysgroup_id_bool',      $_POST['sysgroup_id_bool']);
$_TEMPLATE->assign('system_id_bool',        $_POST['system_id_bool']);

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

// systemgroupsystem_list
$_TEMPLATE->assign('systemgroupsystem_list', $systemgroupsystem_list);


// --------------------------------------------------------------------
// 
// PAGE DISPLAY
// 
// --------------------------------------------------------------------

// display header and menu
require_once('header.php');
require_once('menu.php');

// display the content
$_TEMPLATE->display('systemgroupsystem_filter.tpl');
$_TEMPLATE->display('pager.tpl');
$_TEMPLATE->display('systemgroupsystem_list.tpl');

// display footer
require_once('footer.php');

?>