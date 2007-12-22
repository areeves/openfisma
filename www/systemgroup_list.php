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

require_once('../lib/SystemgroupList.class.php');
require_once('../lib/Pager.class.php');


// --------------------------------------------------------------------
// 
// FORM HANLDING
// 
// --------------------------------------------------------------------

// handle the form action
switch ($_POST['form_action']) {

case 'V': header("Location: ".$_CONFIG->APP_URL()."systemgroup_view.php");   break;
case 'U': header("Location: ".$_CONFIG->APP_URL()."systemgroup_update.php"); break;	
case 'D': header("Location: ".$_CONFIG->APP_URL()."systemgroup_delete.php"); break;

default: break;
	
} // switch form_action


// --------------------------------------------------------------------
//
// DATA RETRIEVAL / MANIPULATION
//
// --------------------------------------------------------------------

// create the systemgroup list for the listing
$systemgroups = new SystemgroupList($_DB);

// select desired list parameters (column headers)
$systemgroups->getSysgroupId();
$systemgroups->getSysgroupName();
$systemgroups->getSysgroupNickname();
$systemgroups->getSysgroupIsIdentity();

// retrieve unique items in each column for filter options
$uniques = $systemgroups->getUniques();

// prepend "any" option to unique filter options
$keys = array_keys($uniques);
while ($key = array_pop($keys)) { $uniques[$key] = array('-- any --'=>'-- any --') + $uniques[$key]; }

// initialize our boolean options for filter boolean options
$bool_options = array('is', 'is not');
$bool_values  = array(1, 0);

// add filters and sort information if not asked to reset
if ($_POST['form_action'] != 'reset') {

  // add filters to list
  if (isset($_POST['sysgroup_name'])                  && ($_POST['sysgroup_name']                 != '-- any --')) { $systemgroups->filterSysgroupName($_POST['sysgroup_name'], $_POST['sysgroup_name_bool']);                  }
  if (isset($_POST['sysgroup_nickname'])              && ($_POST['sysgroup_nickname']             != '-- any --')) { $systemgroups->filterSysgroupNickname($_POST['sysgroup_nickname'], $_POST['sysgroup_nickname_bool']);              }
  if (isset($_POST['sysgroup_is_identity'])           && ($_POST['sysgroup_is_identity']          != '-- any --')) { $systemgroups->filterSysgroupIsIdentity($_POST['sysgroup_is_identity'], $_POST['sysgroup_is_identity_bool']);           }
  
  // propagate sort parameters
  if (isset($_POST['sort_params'])) { $systemgroups->setOrder($_POST['sort_params']); }

}

// reset the filters
else {

  $_POST['sysgroup_name']                  = NULL;
  $_POST['sysgroup_nickname']              = NULL;
  $_POST['sysgroup_is_identity']           = NULL;

  $_POST['sysgroup_name_bool']             = NULL;
  $_POST['sysgroup_nickname_bool']         = NULL;
  $_POST['sysgroup_is_identity_bool']      = NULL;

}

// retrieve the overall list size (for the pager)
$_PAGER->setListSize($systemgroups->getListSize());

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
$systemgroup_list = $systemgroups->getList($_PAGER->getPageOffset(), $_PAGER->getPageSize());

// encrypt the ids
for ($row = 0; $row < count($systemgroup_list); $row++) {

  $systemgroup_list [$row]['sysgroup_id'] = $_E->encrypt($systemgroup_list [$row]['sysgroup_id']);

}


// --------------------------------------------------------------------
//
// TEMPLATE POPULATION
//
// --------------------------------------------------------------------

// header
$_TEMPLATE->assign('this_page',  'systemgroup_list.php');
$_TEMPLATE->assign('this_title', 'SYSTEMGROUP > list');
$_TEMPLATE->assign('menu_header', 'system group');

// filter variables
$_TEMPLATE->assign('filter_standalone',             0);
$_TEMPLATE->assign('bool_options',                  $bool_options);
$_TEMPLATE->assign('bool_values',                   $bool_values);

$_TEMPLATE->assign('sysgroup_names',                  $uniques['sysgroup_name']);
$_TEMPLATE->assign('sysgroup_nicknames',              $uniques['sysgroup_nickname']);
$_TEMPLATE->assign('siesgroup_is_identities',           $uniques['sysgroup_is_identity']);

$_TEMPLATE->assign('sysgroup_name',                  $_POST['sysgroup_name']);
$_TEMPLATE->assign('sysgroup_nickname',              $_POST['sysgroup_nickname']);
$_TEMPLATE->assign('sysgroup_is_identity',           $_POST['sysgroup_is_identity']);

$_TEMPLATE->assign('sysgroup_name_bool',             $_POST['sysgroup_name_bool']);
$_TEMPLATE->assign('sysgroup_nickname_bool',         $_POST['sysgroup_nickname_bool']);
$_TEMPLATE->assign('sysgroup_is_identity_bool',      $_POST['sysgroup_is_identity_bool']);

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

// systemgroup_list
$_TEMPLATE->assign('systemgroup_list', $systemgroup_list);


// --------------------------------------------------------------------
// 
// PAGE DISPLAY
// 
// --------------------------------------------------------------------

// display header and menu
require_once('header.php');
require_once('menu.php');

// display the content
$_TEMPLATE->display('systemgroup_filter.tpl');
$_TEMPLATE->display('pager.tpl');
$_TEMPLATE->display('systemgroup_list.tpl');

// display footer
require_once('footer.php');

?>