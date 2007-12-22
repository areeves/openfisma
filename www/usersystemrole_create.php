<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

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

case 'Create':

	// create usersystemrole if we are referrer
	if ($_POST['referrer'] == 'usersystemrole_create.php') {

		// create a new usersystemrole instance
		$usersystemrole = new UserSystemRole($_DB);
	
		// update the newly created usersystemrole with sanitized input	
		$usersystemrole->setRoleId($_DB->sanitize($_POST['role_id']));
		$usersystemrole->setSysgroupId($_DB->sanitize($_POST['sysgroup_id']));
		$usersystemrole->setSystemId($_DB->sanitize($_POST['system_id']));
		$usersystemrole->setUserId($_DB->sanitize($_POST['user_id']));

		$usersystemrole->saveUserSystemRole();
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

require_once('../lib/Template.config.php');

$_TEMPLATE->assign('this_page',  'usersystemrole_create.php');
$_TEMPLATE->assign('this_title', 'USERSYSTEMROLE > create');
$_TEMPLATE->assign('menu_header', 'user system role');


// --------------------------------------------------------------------
// 
// DISPLAY MENU AND HEADER
// 
// --------------------------------------------------------------------

// identify our page to the header
define('PAGE_NAME',  'usersystemrole_create.php'); 
define('PAGE_TITLE', 'UserSystemRole: create');

// load header file
require_once('header.php');
require_once('menu.php');


// --------------------------------------------------------------------
// 
// DISPLAY PAGE CONTENT
// 
// --------------------------------------------------------------------

// display the template
$_TEMPLATE->display('usersystemrole_create.tpl');


// --------------------------------------------------------------------
// 
// DISPLAY FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load footer file
require_once('footer.php');

?>