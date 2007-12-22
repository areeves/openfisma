<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

require_once('../lib/Config.class.php');
require_once('../lib/PoamComment.class.php');
require_once('../lib/Database.class.php');
require_once('../lib/Encryption.class.php');	


// --------------------------------------------------------------------
// 
// FORM HANDLING - NO OUTPUT SHOULD OCCUR HERE!
// 
// --------------------------------------------------------------------

// handle the form action
switch ($_POST['form_action']) {
case 'Cancel': 

	// action cancelled
	header("Location: ".$_CONFIG->APP_URL()."poamcomment_list.php");
	break;

case 'Create':

	// create poamcomment if we are referrer
	if ($_POST['referrer'] == 'poamcomment_create.php') {

		// create a new poamcomment instance
		$poamcomment = new PoamComment($_DB);
	
		// create the poamcomment
		$poamcomment->setPoamId($_DB->sanitize($_POST['poam_id']));
		$poamcomment->setUserId($_DB->sanitize($_POST['user_id']));
		$poamcomment->setCommentParent($_DB->sanitize($_POST['comment_parent']));
		$poamcomment->setCommentDate($_DB->sanitize($_POST['comment_date_Year'].'-'.$_POST['comment_date_Month'].'-'.$_POST['comment_date_Day'].' '.$_POST['comment_date_Hour'].':'.$_POST['comment_date_Minute'].':'.$_POST['comment_date_Second']));
		$poamcomment->setCommentTopic($_DB->sanitize($_POST['comment_topic']));
		$poamcomment->setCommentBody($_DB->sanitize($_POST['comment_body']));
		
		$poamcomment->savePoamComment();
	
		// return to the list
		header("Location: ".$_CONFIG->APP_URL()."poamcomment_list.php");
		break;
		
	}
		
default: break;		
	
} // switch form_action


// --------------------------------------------------------------------
//
// TEMPLATE POPULATION
//
// --------------------------------------------------------------------

require_once('../lib/Template.config.php');

$_TEMPLATE->assign('this_page',  'poamcomment_create.php');
$_TEMPLATE->assign('this_title', 'POAMCOMMENT > create');
$_TEMPLATE->assign('menu_header', 'poam comment');

require_once('../lib/PoamList.class.php');
$poams = new PoamList($_DB);
$poams->getPoamId(TRUE);
$poams->getPoamType();
$poam_id_list = $poams->getKeyList();
$poam_list = array();
foreach ($poam_id_list as $k=>$f) {
	$poam_list[$k] = 'poam #'.$k;
}
$_TEMPLATE->assign('poam_list', $poam_list);

require_once('../lib/UserList.class.php');
$users = new UserList($_DB, $_E);
$users->getUserId(TRUE);
$users->getUserName();
$_TEMPLATE->assign('user_list', $users->getKeyList());
// --------------------------------------------------------------------
// 
// DISPLAY MENU AND HEADER
// 
// --------------------------------------------------------------------

// identify our page to the header
define('PAGE_NAME',  'poamcomment_create.php'); 
define('PAGE_TITLE', 'PoamComment: create');

// load header file
require_once('header.php');
require_once('menu.php');


// --------------------------------------------------------------------
// 
// DISPLAY PAGE CONTENT
// 
// --------------------------------------------------------------------

// display the template
$_TEMPLATE->display('poamcomment_create.tpl');


// --------------------------------------------------------------------
// 
// DISPLAY FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load footer file
require_once('footer.php');

?>