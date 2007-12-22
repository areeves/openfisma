<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

// class includes
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

case 'Delete':

	// only update if we sent the post
	if ($_POST['referrer'] == 'poamcomment_delete.php') {
	
		// create a new poamcomment instance
		$poamcomment = new PoamComment($_DB, $_E->decrypt($_POST['comment_id']));
		
		// delete the bastard
		$poamcomment->deletePoamComment();
	
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

// load the template
require_once('../lib/Template.config.php');

// assign the template values
$_TEMPLATE->assign('this_page',  'poamcomment_delete.php');
$_TEMPLATE->assign('this_title', 'POAMCOMMENT > delete');
$_TEMPLATE->assign('menu_header', 'poam comment');

// retrieve the existing poamcomment's values
$poamcomment = new PoamComment($_DB, $_E->decrypt($_POST['comment_id']));

$_TEMPLATE->assign('comment_id',              $_POST['comment_id']);
$_TEMPLATE->assign('poam_id',      $poamcomment->getPoamId()     );
$_TEMPLATE->assign('user_id',      $poamcomment->getUserId()     );
$_TEMPLATE->assign('comment_parent',      $poamcomment->getCommentParent()     );
$_TEMPLATE->assign('comment_date',      $poamcomment->getCommentDate()     );
$_TEMPLATE->assign('comment_topic',      $poamcomment->getCommentTopic()     );
$_TEMPLATE->assign('comment_body',      $poamcomment->getCommentBody()     );


// --------------------------------------------------------------------
// 
// DISPLAY MENU AND HEADER
// 
// --------------------------------------------------------------------

// load header file
require_once('header.php');
require_once('menu.php');


// --------------------------------------------------------------------
// 
// DISPLAY PAGE CONTENT
// 
// --------------------------------------------------------------------

// display the template
$_TEMPLATE->display('poamcomment_delete.tpl');


// --------------------------------------------------------------------
// 
// DISPLAY FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load footer file
require_once('footer.php');

?>