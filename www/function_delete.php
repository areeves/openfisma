<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

// class includes
require_once('../lib/Config.class.php');
require_once('../lib/Functions.class.php');
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
	header("Location: ".$_CONFIG->APP_URL()."function_list.php");
	break;

case 'Delete':

	// only update if we sent the post
	if ($_POST['referrer'] == 'function_delete.php') {
	
		// create a new functions instance
		$function = new Functions($_DB, $_E->decrypt($_POST['function_id']));
		
		// delete the bastard
		$function->deleteFunction();
	
		// return to the list
		header("Location: ".$_CONFIG->APP_URL()."function_list.php");
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
$_TEMPLATE->assign('this_page',  'function_delete.php');
$_TEMPLATE->assign('this_title', 'FUNCTION > delete');

// retrieve the existing functions's values
$function = new Functions($_DB, $_E->decrypt($_POST['function_id']));

$_TEMPLATE->assign('function_id',    $_POST['function_id']);
$_TEMPLATE->assign('function_name',  $function->getFunctionName());
$_TEMPLATE->assign('function_screen',  $function->getFunctionScreen());
$_TEMPLATE->assign('function_action',  $function->getFunctionAction());
$_TEMPLATE->assign('function_desc',  $function->getFunctionDesc());
$_TEMPLATE->assign('function_open',  $function->getFunctionOpen());

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
$_TEMPLATE->display('function_delete.tpl');


// --------------------------------------------------------------------
// 
// DISPLAY FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load footer file
require_once('footer.php');

?>