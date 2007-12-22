<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

// class includes
require_once('../lib/Config.class.php');
require_once('../lib/UserSystemRole.class.php');
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
	header("Location: ".$_CONFIG->APP_URL()."usersystemrole_list.php");
	break;

case 'Delete':

	// only update if we sent the post
	if ($_POST['referrer'] == 'usersystemrole_delete.php') {
	
		// create a new usersystemrole instance
		$usersystemrole = new UserSystemRole($_DB, unserialize($_E->decrypt($_POST['usersystemrole_id'])));
		
		// delete the bastard
		$usersystemrole ->deleteUserSystemRole(unserialize($_E->decrypt($_POST['usersystemrole_id'])));
	
		// return to the list
		header("Location: ".$_CONFIG->APP_URL()."usersystemrole_list.php");
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
$_TEMPLATE->assign('this_page',  'usersystemrole_delete.php');
$_TEMPLATE->assign('this_title', 'USERSYSTEMROLE > delete');
$_TEMPLATE->assign('menu_header', 'user system role');

// retrieve the existing usersystemrole's values
$usersystemrole = new UserSystemRole($_DB, unserialize($_E->decrypt($_POST['usersystemrole_id'])));

$_TEMPLATE->assign('usersystemrole_id',              $_POST['usersystemrole_id']);
$_TEMPLATE->assign('user_id',      $usersystemrole->getUserId()     );
$_TEMPLATE->assign('system_id',      $usersystemrole->getSystemId()     );
$_TEMPLATE->assign('role_id',      $usersystemrole->getRoleId()     );
$_TEMPLATE->assign('sysgroup_id',      $usersystemrole->getSysgroupId()     );

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
$_TEMPLATE->display('usersystemrole_delete.tpl');


// --------------------------------------------------------------------
// 
// DISPLAY FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load footer file
require_once('footer.php');

?>