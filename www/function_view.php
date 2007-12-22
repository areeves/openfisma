<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

require_once('../lib/Functions.class.php');
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

// create a functions to view
$function = new Functions($_DB);

// validate that the functions actually exists

if ($function->functionExists($_E->decrypt($_POST['function_id']))) {

	// retrieve the user
	$function->getFunction($_E->decrypt($_POST['function_id']));
	
}

// redirect on bum functions
else { header("Location: ".$_CONFIG->APP_URL()."function_list.php"); }


// --------------------------------------------------------------------
// 
// TEMPLATE POPULATION
// 
// --------------------------------------------------------------------

require_once('../lib/Template.config.php');

// assign the template values
$_TEMPLATE->assign('this_page',  'function_view.php');
$_TEMPLATE->assign('this_title', 'FUNCTION > view');
$_TEMPLATE->assign('menu_header', 'function');


$_TEMPLATE->assign('function_id', $_POST['function_id']);

$_TEMPLATE->assign('function_name',  $function->getFunctionName());
$_TEMPLATE->assign('function_screen',  $function->getFunctionScreen());
$_TEMPLATE->assign('function_action',  $function->getFunctionAction());
$_TEMPLATE->assign('function_desc',  $function->getFunctionDesc());
$_TEMPLATE->assign('function_open',  $function->getFunctionOpen());

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
$_TEMPLATE->display('function_view.tpl');


// --------------------------------------------------------------------
// 
// FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load footer file
require_once('footer.php');

?>