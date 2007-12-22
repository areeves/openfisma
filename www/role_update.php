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

case 'Update':

	// only update if we sent the post
	if ($_POST['referrer'] == 'role_update.php') {
	
		// create a new role instance
		$role = new Role($_DB, $_E->decrypt($_POST['role_id']));
	
		// update the newly created role with sanitized input	
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

// load the template
require_once('../lib/Template.config.php');

// retrieve the existing role's values
$role = new Role($_DB, $_E->decrypt($_POST['role_id']));

// assign the template values
$_TEMPLATE->assign('this_page',  'role_update.php');
$_TEMPLATE->assign('this_title', 'ROLE > update');
$_TEMPLATE->assign('menu_header', 'role');


$_TEMPLATE->assign('role_id',              $_POST['role_id']);
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
$_TEMPLATE->display('role_update.tpl');


// --------------------------------------------------------------------
// 
// DISPLAY FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load footer file
require_once('footer.php');

?>