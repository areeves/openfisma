<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

require_once('../lib/Config.class.php');
require_once('../lib/Functions.class.php');
require_once('../lib/Database.class.php');
//require_once('../lib/Encryption.class.php');	


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

case 'Create':

	// create functions if we are referrer
	if ($_POST['referrer'] == 'function_create.php') {

		// create a new functions instance
		$function = new Functions($_DB);
	
		// create the functions
		$function->setFunctionName  ($_DB->sanitize($_POST['function_name']));
		$function->setFunctionScreen  ($_DB->sanitize($_POST['function_screen']));
		$function->setFunctionAction  ($_DB->sanitize($_POST['function_action']));
		$function->setFunctionDesc  ($_DB->sanitize($_POST['function_desc']));
		$function->setFunctionOpen  ($_DB->sanitize($_POST['function_open']));
		
        $function->saveFunction();
	
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

require_once('../lib/Template.config.php');

$_TEMPLATE->assign('this_page',  'function_create.php');
$_TEMPLATE->assign('this_title', 'FUNCTION > create');
$_TEMPLATE->assign('menu_header', 'function');


// --------------------------------------------------------------------
// 
// DISPLAY MENU AND HEADER
// 
// --------------------------------------------------------------------

// identify our page to the header
define('PAGE_NAME',  'function_create.php'); 
define('PAGE_TITLE', 'Function: create');

// load header file
require_once('header.php');
require_once('menu.php');


// --------------------------------------------------------------------
// 
// DISPLAY PAGE CONTENT
// 
// --------------------------------------------------------------------

// display the template
$_TEMPLATE->display('function_create.tpl');


// --------------------------------------------------------------------
// 
// DISPLAY FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load footer file
require_once('footer.php');

?>