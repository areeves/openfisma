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

require_once('../lib/RolesysgroupList.class.php');
require_once('../lib/Pager.class.php');


// --------------------------------------------------------------------
// 
// FORM HANLDING
// 
// --------------------------------------------------------------------

// handle the form action
switch ($_POST['form_action']) {

case 'V': header("Location: ".$_CONFIG->APP_URL()."rolesysgroup_view.php");   break;
case 'U': header("Location: ".$_CONFIG->APP_URL()."rolesysgroup_update.php"); break;	
case 'D': header("Location: ".$_CONFIG->APP_URL()."rolesysgroup_delete.php"); break;

default: break;
	
} // switch form_action


// --------------------------------------------------------------------
//
// DATA RETRIEVAL / MANIPULATION
//
// --------------------------------------------------------------------

// create the rolesysgroup list for the listing
$rolesysgroups = new RolesysgroupList($_DB);

// select desired list parameters (column headers)
$rolesysgroups->getRoleGroupId();
$rolesysgroups->getRoleId();
$rolesysgroups->getSysgroupId();

// retrieve unique items in each column for filter options
$uniques = $rolesysgroups->getUniques();

require_once('../lib/RoleList.class.php');
$roles = new RoleList($_DB);
$roles->getRoleId(TRUE);
$roles->getRoleNickname();
$roles->filterRoleId($uniques['role_id']);
$roles->setOrder('role_nickname ASC');
$uniques['role_id'] = $roles->getKeyList();

require_once('../lib/SystemGroupList.class.php');
$groups = new SystemGroupList($_DB);
$groups->getSysgroupId(TRUE);
$groups->getSysgroupNickname();
$groups->filterSysgroupId($uniques['sysgroup_id']);
$groups->setOrder('sysgroup_nickname ASC');
$uniques['sysgroup_id'] = $groups->getKeyList();

// prepend "any" option to unique filter options
$keys = array_keys($uniques);
while ($key = array_pop($keys)) { $uniques[$key] = array('-- any --'=>'-- any --') + $uniques[$key]; }

// initialize our boolean options for filter boolean options
$bool_options = array('is', 'is not');
$bool_values  = array(1, 0);

// add filters and sort information if not asked to reset
if ($_POST['form_action'] != 'reset') {

  // add filters to list
  if (isset($_POST['role_id'])               && ($_POST['role_id']              != '-- any --')) { $rolesysgroups->filterRoleId($_POST['role_id'], $_POST['role_id_bool']);               }
  if (isset($_POST['sysgroup_id'])           && ($_POST['sysgroup_id']          != '-- any --')) { $rolesysgroups->filterSysgroupId($_POST['sysgroup_id'], $_POST['sysgroup_id_bool']);           }
  
  // propagate sort parameters
  if (isset($_POST['sort_params'])) { $rolesysgroups->setOrder($_POST['sort_params']); }

}

// reset the filters
else {

  $_POST['role_id']               = NULL;
  $_POST['sysgroup_id']           = NULL;

  $_POST['role_id_bool']          = NULL;
  $_POST['sysgroup_id_bool']      = NULL;

}

// retrieve the overall list size (for the pager)
$_PAGER->setListSize($rolesysgroups->getListSize());

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
$rolesysgroup_list = $rolesysgroups->getList($_PAGER->getPageOffset(), $_PAGER->getPageSize());

// encrypt the ids
for ($row = 0; $row < count($rolesysgroup_list); $row++) {

  $rolesysgroup_list [$row]['role_group_id'] = $_E->encrypt($rolesysgroup_list [$row]['role_group_id']);

}


// --------------------------------------------------------------------
//
// TEMPLATE POPULATION
//
// --------------------------------------------------------------------

// header
$_TEMPLATE->assign('this_page',  'rolesysgroup_list.php');
$_TEMPLATE->assign('this_title', 'ROLESYSGROUP > list');
$_TEMPLATE->assign('menu_header', 'role sysgroup');

// filter variables
$_TEMPLATE->assign('filter_standalone',             0);
$_TEMPLATE->assign('bool_options',                  $bool_options);
$_TEMPLATE->assign('bool_values',                   $bool_values);

$_TEMPLATE->assign('role_ids',               $uniques['role_id']);
$_TEMPLATE->assign('sysgroup_ids',           $uniques['sysgroup_id']);

$_TEMPLATE->assign('role_id',               $_POST['role_id']);
$_TEMPLATE->assign('sysgroup_id',           $_POST['sysgroup_id']);

$_TEMPLATE->assign('role_id_bool',          $_POST['role_id_bool']);
$_TEMPLATE->assign('sysgroup_id_bool',      $_POST['sysgroup_id_bool']);

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

// rolesysgroup_list
$_TEMPLATE->assign('rolesysgroup_list', $rolesysgroup_list);


// --------------------------------------------------------------------
// 
// PAGE DISPLAY
// 
// --------------------------------------------------------------------

// display header and menu
require_once('header.php');
require_once('menu.php');

// display the content
$_TEMPLATE->display('rolesysgroup_filter.tpl');
$_TEMPLATE->display('pager.tpl');
$_TEMPLATE->display('rolesysgroup_list.tpl');

// display footer
require_once('footer.php');

?>