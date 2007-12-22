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

require_once('../lib/PoamList.class.php');
require_once('../lib/Pager.class.php');


// --------------------------------------------------------------------
// 
// FORM HANLDING
// 
// --------------------------------------------------------------------

// handle the form action
switch ($_POST['form_action']) {

case 'V': header("Location: ".$_CONFIG->APP_URL()."poam_view.php");   break;
case 'U': header("Location: ".$_CONFIG->APP_URL()."poam_update.php"); break;	
case 'D': header("Location: ".$_CONFIG->APP_URL()."poam_delete.php"); break;

default: break;
	
} // switch form_action


// --------------------------------------------------------------------
//
// DATA RETRIEVAL / MANIPULATION
//
// --------------------------------------------------------------------

// create the poam list for the listing
$poams = new PoamList($_DB);

// select desired list parameters (column headers)
$poams->getPoamId();
$poams->getFindingId();
$poams->getPoamType();
$poams->getPoamStatus();
$poams->getPoamActionOwner();
$poams->getPoamActionDateEst();

// retrieve unique items in each column for filter options
$uniques = $poams->getUniques();

// here is special
$strFinding = array();
for ($i=0;$i<count($uniques['finding_id']);$i++){
    $strFinding[] = 'finding #'.$uniques['finding_id'][$i];
}
$uniques['finding_id'] = array_combine($uniques['finding_id'], $strFinding);

require_once('../lib/UserList.class.php');
$users = new UserList($_DB);
$users->getUserId(TRUE);
$users->getUserName();
$users->filterUserId($uniques['poam_action_owner']);
$users->setOrder('user_name ASC');
$uniques['poam_action_owner'] = $users->getKeyList();

// prepend "any" option to unique filter options
$keys = array_keys($uniques);
while ($key = array_pop($keys)) { $uniques[$key] = array('-- any --'=>'-- any --') + $uniques[$key]; }

// initialize our boolean options for filter boolean options
$bool_options = array('is', 'is not');
$bool_values  = array(1, 0);

// add filters and sort information if not asked to reset
if ($_POST['form_action'] != 'reset') {

  // add filters to list
  if (isset($_POST['finding_id'])                     && ($_POST['finding_id']                    != '-- any --')) { $poams->filterFindingId($_POST['finding_id'], $_POST['finding_id_bool']);                     }
  if (isset($_POST['poam_type'])                      && ($_POST['poam_type']                     != '-- any --')) { $poams->filterPoamType($_POST['poam_type'], $_POST['poam_type_bool']);                      }
  if (isset($_POST['poam_status'])                    && ($_POST['poam_status']                   != '-- any --')) { $poams->filterPoamStatus($_POST['poam_status'], $_POST['poam_status_bool']);                    }
  if (isset($_POST['poam_action_owner'])              && ($_POST['poam_action_owner']             != '-- any --')) { $poams->filterPoamActionOwner($_POST['poam_action_owner'], $_POST['poam_action_owner_bool']);              }
  if (isset($_POST['poam_action_date_est'])           && ($_POST['poam_action_date_est']          != '-- any --')) { $poams->filterPoamActionDateEst($_POST['poam_action_date_est'], $_POST['poam_action_date_est_bool']);           }
  
  // propagate sort parameters
  if (isset($_POST['sort_params'])) { $poams->setOrder($_POST['sort_params']); }

}

// reset the filters
else {

  $_POST['finding_id']                     = NULL;
  $_POST['poam_type']                      = NULL;
  $_POST['poam_status']                    = NULL;
  $_POST['poam_action_owner']              = NULL;
  $_POST['poam_action_date_est']           = NULL;

  $_POST['finding_id_bool']                = NULL;
  $_POST['poam_type_bool']                 = NULL;
  $_POST['poam_status_bool']               = NULL;
  $_POST['poam_action_owner_bool']         = NULL;
  $_POST['poam_action_date_est_bool']      = NULL;

}

// retrieve the overall list size (for the pager)
$_PAGER->setListSize($poams->getListSize());

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
$poam_list = $poams->getList($_PAGER->getPageOffset(), $_PAGER->getPageSize());

// encrypt the ids
for ($row = 0; $row < count($poam_list); $row++) {

  $poam_list [$row]['poam_id'] = $_E->encrypt($poam_list [$row]['poam_id']);

}


// --------------------------------------------------------------------
//
// TEMPLATE POPULATION
//
// --------------------------------------------------------------------

// header
$_TEMPLATE->assign('this_page',  'poam_list.php');
$_TEMPLATE->assign('this_title', 'POAM > list');
$_TEMPLATE->assign('menu_header', 'poam');

// filter variables
$_TEMPLATE->assign('filter_standalone',             0);
$_TEMPLATE->assign('bool_options',                  $bool_options);
$_TEMPLATE->assign('bool_values',                   $bool_values);

$_TEMPLATE->assign('finding_ids',                     $uniques['finding_id']);
$_TEMPLATE->assign('unique_types',                      $uniques['poam_type']);
$_TEMPLATE->assign('unique_statuss',                    $uniques['poam_status']);
$_TEMPLATE->assign('unique_action_owners',              $uniques['poam_action_owner']);
$_TEMPLATE->assign('unique_action_date_ests',           $uniques['poam_action_date_est']);

$_TEMPLATE->assign('finding_id',                     $_POST['finding_id']);
$_TEMPLATE->assign('selected_type',                      $_POST['poam_type']);
$_TEMPLATE->assign('selected_status',                    $_POST['poam_status']);
$_TEMPLATE->assign('selected_action_owner',              $_POST['poam_action_owner']);
$_TEMPLATE->assign('selected_action_date_est',           $_POST['poam_action_date_est']);

$_TEMPLATE->assign('finding_id_bool',                $_POST['finding_id_bool']);
$_TEMPLATE->assign('selected_type_bool',                 $_POST['poam_type_bool']);
$_TEMPLATE->assign('selected_status_bool',               $_POST['poam_status_bool']);
$_TEMPLATE->assign('selected_action_owner_bool',         $_POST['poam_action_owner_bool']);
$_TEMPLATE->assign('selected_action_date_est_bool',      $_POST['poam_action_date_est_bool']);

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

// poam_list
$_TEMPLATE->assign('poam_list', $poam_list);


// --------------------------------------------------------------------
// 
// PAGE DISPLAY
// 
// --------------------------------------------------------------------

// display header and menu
require_once('header.php');
require_once('menu.php');

// display the content
$_TEMPLATE->display('poam_filter.tpl');
$_TEMPLATE->display('pager.tpl');
$_TEMPLATE->display('poam_list.tpl');

// display footer
require_once('footer.php');

?>