<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

// class includes
require_once('../lib/Config.class.php');
require_once('../lib/RoleFunction.class.php');
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
	header("Location: ".$_CONFIG->APP_URL()."rolefunction_list.php");
	break;

case 'Delete':

	// only update if we sent the post
	if ($_POST['referrer'] == 'rolefunction_delete.php') {
	
		// create a new rolefunction instance
		$rolefunction = new RoleFunction($_DB, $_E->decrypt($_POST['role_func_id']));
		
		// delete the bastard
		$rolefunction ->deleteRoleFunction();
	
		// return to the list
		header("Location: ".$_CONFIG->APP_URL()."rolefunction_list.php");
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
$_TEMPLATE->assign('this_page',  'rolefunction_delete.php');
$_TEMPLATE->assign('this_title', 'ROLEFUNCTION > delete');
$_TEMPLATE->assign('menu_header', 'role function');

// retrieve the existing rolefunction's values
$rolefunction = new RoleFunction($_DB, $_E->decrypt($_POST['role_func_id']));

$_TEMPLATE->assign('role_func_id',              $_POST['role_func_id']);
$_TEMPLATE->assign('role_id',      $rolefunction->getRoleId()     );
$_TEMPLATE->assign('function_id',      $rolefunction->getFunctionId()     );


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
$_TEMPLATE->display('rolefunction_delete.tpl');


// --------------------------------------------------------------------
// 
// DISPLAY FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load footer file
require_once('footer.php');

?>