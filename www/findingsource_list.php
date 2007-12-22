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

require_once('../lib/FindingsourceList.class.php');
require_once('../lib/Pager.class.php');


// --------------------------------------------------------------------
// 
// FORM HANLDING
// 
// --------------------------------------------------------------------

// handle the form action
switch ($_POST['form_action']) {

case 'V': header("Location: ".$_CONFIG->APP_URL()."findingsource_view.php");   break;
case 'U': header("Location: ".$_CONFIG->APP_URL()."findingsource_update.php"); break;	
case 'D': header("Location: ".$_CONFIG->APP_URL()."findingsource_delete.php"); break;

default: break;
	
} // switch form_action


// --------------------------------------------------------------------
//
// DATA RETRIEVAL / MANIPULATION
//
// --------------------------------------------------------------------

// create the findingsource list for the listing
$findingsources = new FindingsourceList($_DB);

// select desired list parameters (column headers)
$findingsources->getSourceId();
$findingsources->getSourceName();
$findingsources->getSourceNickname();
$findingsources->getSourceDesc();

// retrieve unique items in each column for filter options
$uniques = $findingsources->getUniques();

// prepend "any" option to unique filter options
$keys = array_keys($uniques);
while ($key = array_pop($keys)) { $uniques[$key] = array('-- any --'=>'-- any --') + $uniques[$key]; }

// initialize our boolean options for filter boolean options
$bool_options = array('is', 'is not');
$bool_values  = array(1, 0);

// add filters and sort information if not asked to reset
if ($_POST['form_action'] != 'reset') {

  // add filters to list
  if (isset($_POST['source_name'])               && ($_POST['source_name']              != '-- any --')) { $findingsources->filterSourceName($_POST['source_name'], $_POST['source_name_bool']);               }
  if (isset($_POST['source_nickname'])           && ($_POST['source_nickname']          != '-- any --')) { $findingsources->filterSourceNickname($_POST['source_nickname'], $_POST['source_nickname_bool']);           }
  if (isset($_POST['source_desc'])               && ($_POST['source_desc']              != '-- any --')) { $findingsources->filterSourceDesc($_POST['source_desc'], $_POST['source_desc_bool']);               }
  
  // propagate sort parameters
  if (isset($_POST['sort_params'])) { $findingsources->setOrder($_POST['sort_params']); }

}

// reset the filters
else {

  $_POST['source_name']               = NULL;
  $_POST['source_nickname']           = NULL;
  $_POST['source_desc']               = NULL;

  $_POST['source_name_bool']          = NULL;
  $_POST['source_nickname_bool']      = NULL;
  $_POST['source_desc_bool']          = NULL;

}

// retrieve the overall list size (for the pager)
$_PAGER->setListSize($findingsources->getListSize());

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
$findingsource_list = $findingsources->getList($_PAGER->getPageOffset(), $_PAGER->getPageSize());

// encrypt the ids
for ($row = 0; $row < count($findingsource_list); $row++) {

  $findingsource_list [$row]['source_id'] = $_E->encrypt($findingsource_list [$row]['source_id']);

}


// --------------------------------------------------------------------
//
// TEMPLATE POPULATION
//
// --------------------------------------------------------------------

// header
$_TEMPLATE->assign('this_page',  'findingsource_list.php');
$_TEMPLATE->assign('this_title', 'FINDINGSOURCE > list');
$_TEMPLATE->assign('menu_header', 'finding source');

// filter variables
$_TEMPLATE->assign('filter_standalone',             0);
$_TEMPLATE->assign('bool_options',                  $bool_options);
$_TEMPLATE->assign('bool_values',                   $bool_values);

$_TEMPLATE->assign('source_names',               $uniques['source_name']);
$_TEMPLATE->assign('source_nicknames',           $uniques['source_nickname']);
$_TEMPLATE->assign('source_descs',               $uniques['source_desc']);

$_TEMPLATE->assign('source_name',               $_POST['source_name']);
$_TEMPLATE->assign('source_nickname',           $_POST['source_nickname']);
$_TEMPLATE->assign('source_desc',               $_POST['source_desc']);

$_TEMPLATE->assign('source_name_bool',          $_POST['source_name_bool']);
$_TEMPLATE->assign('source_nickname_bool',      $_POST['source_nickname_bool']);
$_TEMPLATE->assign('source_desc_bool',          $_POST['source_desc_bool']);

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

// findingsource_list
$_TEMPLATE->assign('findingsource_list', $findingsource_list);


// --------------------------------------------------------------------
// 
// PAGE DISPLAY
// 
// --------------------------------------------------------------------

// display header and menu
require_once('header.php');
require_once('menu.php');

// display the content
$_TEMPLATE->display('findingsource_filter.tpl');
$_TEMPLATE->display('pager.tpl');
$_TEMPLATE->display('findingsource_list.tpl');

// display footer
require_once('footer.php');

?>