<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

// class includes
require_once('../lib/Config.class.php');
require_once('../lib/Role.class.php');
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
	header("Location: ".$_CONFIG->APP_URL()."role_list.php");
	break;

case 'Delete':

	// only update if we sent the post
	if ($_POST['referrer'] == 'role_delete.php') {
	
		// create a new role instance
		$role = new Role($_DB, $_E->decrypt($_POST['role_id']));
		
		// delete the bastard
		$role->deleteRole();
	
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

// load the template
require_once('../lib/Template.config.php');

// assign the template values
$_TEMPLATE->assign('this_page',  'role_delete.php');
$_TEMPLATE->assign('this_title', 'ROLE > delete');
$_TEMPLATE->assign('menu_header', 'role');

// retrieve the existing role's values
$role = new Role($_DB, $_E->decrypt($_POST['role_id']));

$_TEMPLATE->assign('role_id', $_POST['role_id']);

$_TEMPLATE->assign('role_name',      $role->getRoleName()     );
$_TEMPLATE->assign('role_nickname',      $role->getRoleNickname()     );
$_TEMPLATE->assign('role_desc',      $role->getRoleDesc()     );


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
$_TEMPLATE->display('role_delete.tpl');


// --------------------------------------------------------------------
// 
// DISPLAY FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load footer file
require_once('footer.php');

?>