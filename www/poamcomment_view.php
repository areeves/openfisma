<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

require_once('../lib/PoamComment.class.php');
require_once('../lib/Config.class.php');
require_once('../lib/Encryption.class.php');

// --------------------------------------------------------------------
// 
// FORM HANDLING
// 
// --------------------------------------------------------------------


// --------------------------------------------------------------------
// 
// DATA MANIPULATION
// 
// --------------------------------------------------------------------

// create a poamcomment to view
$poamcomment = new PoamComment($_DB);

// validate that the poamcomment actually exists

if ($poamcomment->poamcommentExists($_E->decrypt($_POST['comment_id']))) {

	// retrieve the user
	$poamcomment->getPoamComment($_E->decrypt($_POST['comment_id']));
	
}

// redirect on bum poamcomment
else { header("Location: ".$_CONFIG->APP_URL()."poamcomment_list.php"); }


// --------------------------------------------------------------------
// 
// TEMPLATE POPULATION
// 
// --------------------------------------------------------------------

require_once('../lib/Template.config.php');

// assign the template values
$_TEMPLATE->assign('this_page',  'poamcomment_view.php');
$_TEMPLATE->assign('this_title', 'POAMCOMMENT > view');
$_TEMPLATE->assign('menu_header', 'poam comment');


$_TEMPLATE->assign('comment_id', $_POST['comment_id']);

$_TEMPLATE->assign('poam_id',      $poamcomment->getPoamId()     );
$_TEMPLATE->assign('user_id',      $poamcomment->getUserId()     );
$_TEMPLATE->assign('comment_parent',      $poamcomment->getCommentParent()     );
$_TEMPLATE->assign('comment_date',      $poamcomment->getCommentDate()     );
$_TEMPLATE->assign('comment_topic',      $poamcomment->getCommentTopic()     );
$_TEMPLATE->assign('comment_body',      $poamcomment->getCommentBody()     );

// set up button display options
$_TEMPLATE->assign('show_cancel', 1);
$_TEMPLATE->assign('show_update', 1);
$_TEMPLATE->assign('show_delete', 1);


// --------------------------------------------------------------------
// 
// HEADER SECTION
// 
// --------------------------------------------------------------------

// load header file
require_once('header.php');
require_once('menu.php');


// --------------------------------------------------------------------
// 
// BODY SECTION
// 
// --------------------------------------------------------------------

// display the template
$_TEMPLATE->display('poamcomment_view.tpl');


// --------------------------------------------------------------------
// 
// FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load footer file
require_once('footer.php');

?>