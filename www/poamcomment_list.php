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

require_once('../lib/PoamcommentList.class.php');
require_once('../lib/Pager.class.php');


// --------------------------------------------------------------------
// 
// FORM HANLDING
// 
// --------------------------------------------------------------------

// handle the form action
switch ($_POST['form_action']) {

case 'V': header("Location: ".$_CONFIG->APP_URL()."poamcomment_view.php");   break;
case 'U': header("Location: ".$_CONFIG->APP_URL()."poamcomment_update.php"); break;	
case 'D': header("Location: ".$_CONFIG->APP_URL()."poamcomment_delete.php"); break;

default: break;
	
} // switch form_action


// --------------------------------------------------------------------
//
// DATA RETRIEVAL / MANIPULATION
//
// --------------------------------------------------------------------

// create the poamcomment list for the listing
$poamcomments = new PoamcommentList($_DB);

// select desired list parameters (column headers)
$poamcomments->getCommentId();
$poamcomments->getPoamId();
$poamcomments->getUserId();
$poamcomments->getCommentDate();
$poamcomments->getCommentParent();

// retrieve unique items in each column for filter options
$uniques = $poamcomments->getUniques();

// prepend "any" option to unique filter options
$keys = array_keys($uniques);
while ($key = array_pop($keys)) { $uniques[$key] = array('-- any --'=>'-- any --') + $uniques[$key]; }

// initialize our boolean options for filter boolean options
$bool_options = array('is', 'is not');
$bool_values  = array(1, 0);

// add filters and sort information if not asked to reset
if ($_POST['form_action'] != 'reset') {

  // add filters to list
  if (isset($_POST['poam_id'])                  && ($_POST['poam_id']                 != '-- any --')) { $poamcomments->filterPoamId($_POST['poam_id'], $_POST['poam_id_bool']);                  }
  if (isset($_POST['user_id'])                  && ($_POST['user_id']                 != '-- any --')) { $poamcomments->filterUserId($_POST['user_id'], $_POST['user_id_bool']);                  }
  if (isset($_POST['comment_parent'])           && ($_POST['comment_parent']          != '-- any --')) { $poamcomments->filterCommentParent($_POST['comment_parent'], $_POST['comment_parent_bool']);           }
  if (isset($_POST['comment_date'])             && ($_POST['comment_date']            != '-- any --')) { $poamcomments->filterCommentDate($_POST['comment_date'], $_POST['comment_date_bool']);             }
  
  // propagate sort parameters
  if (isset($_POST['sort_params'])) { $poamcomments->setOrder($_POST['sort_params']); }

}

// reset the filters
else {

  $_POST['poam_id']                  = NULL;
  $_POST['user_id']                  = NULL;
  $_POST['comment_parent']           = NULL;
  $_POST['comment_date']             = NULL;

  $_POST['poam_id_bool']             = NULL;
  $_POST['user_id_bool']             = NULL;
  $_POST['comment_parent_bool']      = NULL;
  $_POST['comment_date_bool']        = NULL;

}

// retrieve the overall list size (for the pager)
$_PAGER->setListSize($poamcomments->getListSize());

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
$poamcomment_list = $poamcomments->getList($_PAGER->getPageOffset(), $_PAGER->getPageSize());

// encrypt the ids
for ($row = 0; $row < count($poamcomment_list); $row++) {

  $poamcomment_list [$row]['comment_id'] = $_E->encrypt($poamcomment_list [$row]['comment_id']);

}


// --------------------------------------------------------------------
//
// TEMPLATE POPULATION
//
// --------------------------------------------------------------------

// header
$_TEMPLATE->assign('this_page',  'poamcomment_list.php');
$_TEMPLATE->assign('this_title', 'POAMCOMMENT > list');
$_TEMPLATE->assign('menu_header', 'poam comment');

// filter variables
$_TEMPLATE->assign('filter_standalone',             0);
$_TEMPLATE->assign('bool_options',                  $bool_options);
$_TEMPLATE->assign('bool_values',                   $bool_values);

$_TEMPLATE->assign('poam_ids',                  $uniques['poam_id']);
$_TEMPLATE->assign('user_ids',                  $uniques['user_id']);
$_TEMPLATE->assign('comment_parents',           $uniques['comment_parent']);
$_TEMPLATE->assign('comment_dates',             $uniques['comment_date']);

$_TEMPLATE->assign('poam_id',                  $_POST['poam_id']);
$_TEMPLATE->assign('user_id',                  $_POST['user_id']);
$_TEMPLATE->assign('comment_parent',           $_POST['comment_parent']);
$_TEMPLATE->assign('comment_date',             $_POST['comment_date']);

$_TEMPLATE->assign('poam_id_bool',             $_POST['poam_id_bool']);
$_TEMPLATE->assign('user_id_bool',             $_POST['user_id_bool']);
$_TEMPLATE->assign('comment_parent_bool',      $_POST['comment_parent_bool']);
$_TEMPLATE->assign('comment_date_bool',        $_POST['comment_date_bool']);

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

// poamcomment_list
$_TEMPLATE->assign('poamcomment_list', $poamcomment_list);


// --------------------------------------------------------------------
// 
// PAGE DISPLAY
// 
// --------------------------------------------------------------------

// display header and menu
require_once('header.php');
require_once('menu.php');

// display the content
$_TEMPLATE->display('poamcomment_filter.tpl');
$_TEMPLATE->display('pager.tpl');
$_TEMPLATE->display('poamcomment_list.tpl');

// display footer
require_once('footer.php');

?>