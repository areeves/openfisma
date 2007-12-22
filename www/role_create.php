<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

require_once('../lib/Config.class.php');
require_once('../lib/Role.class.php');
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
	header("Location: ".$_CONFIG->APP_URL()."role_list.php");
	break;

case 'Create':

	// create role if we are referrer
	if ($_POST['referrer'] == 'role_create.php') {

		// create a new role instance
		$role = new Role($_DB);
	
		// create the role
        $role->setRoleName($_DB->sanitize($_POST['role_name']));
        $role->setRoleNickname($_DB->sanitize($_POST['role_nickname']));
        $role->setRoleDesc($_DB->sanitize($_POST['role_desc']));
        
        $role->saveRole();
	
		// return to the list
		header("Location: ".$_CONFIG->APP_URL()."role_list.php");
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

$_TEMPLATE->assign('this_page',  'role_create.php');
$_TEMPLATE->assign('this_title', 'ROLE > create');
$_TEMPLATE->assign('menu_header', 'role');


// --------------------------------------------------------------------
// 
// DISPLAY MENU AND HEADER
// 
// --------------------------------------------------------------------

// identify our page to the header
define('PAGE_NAME',  'role_create.php'); 
define('PAGE_TITLE', 'Role: create');

// load header file
require_once('header.php');
require_once('menu.php');


// --------------------------------------------------------------------
// 
// DISPLAY PAGE CONTENT
// 
// --------------------------------------------------------------------

// display the template
$_TEMPLATE->display('role_create.tpl');


// --------------------------------------------------------------------
// 
// DISPLAY FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load footer file
require_once('footer.php');

?>