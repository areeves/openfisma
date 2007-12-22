<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

require_once('../lib/Config.class.php');
require_once('../lib/User.class.php');
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
	header("Location: ".$_CONFIG->APP_URL()."user_list.php");
	break;

case 'Create':

	// create user if we are referrer
	if ($_POST['referrer'] == 'user_create.php') {

		// create a new user instance
		$user = new User($_DB, $_E);
	
		// create the user
		$user->createUser();
	
		// update the newly created user with sanitized input	
		$user->setUserNameFirst  ($_DB->sanitize($_POST['user_name_first']));
		$user->setUserNameMiddle ($_DB->sanitize($_POST['user_name_middle']));	
		$user->setUserNameLast   ($_DB->sanitize($_POST['user_name_last']));

		$user->setUserTitle      ($_DB->sanitize($_POST['user_title']));
		$user->setUserPhoneOffice($_DB->sanitize($_POST['user_phone_office']));
		$user->setUserPhoneMobile($_DB->sanitize($_POST['user_phone_mobile']));
	
		$user->setUserName       ($_DB->sanitize($_POST['user_name']));
		$user->setUserPassword   ($_DB->sanitize($_POST['user_password']));
		$user->setUserRoleId  ($_DB->sanitize($_POST['role_id']));
	
		// return to the list
		header("Location: ".$_CONFIG->APP_URL()."user_list.php");
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

$_TEMPLATE->assign('this_page',  'user_create.php');
$_TEMPLATE->assign('this_title', 'USER > create');
$_TEMPLATE->assign('menu_header', 'user');

require_once('../lib/RoleList.class.php');
$roles = new RoleList($_DB);
$roles->getRoleId(TRUE);
$roles->getRoleNickname();
$_TEMPLATE->assign('role_list', $roles->getKeyList());
// --------------------------------------------------------------------
// 
// DISPLAY MENU AND HEADER
// 
// --------------------------------------------------------------------

// identify our page to the header
define('PAGE_NAME',  'user_create.php'); 
define('PAGE_TITLE', 'User: create');

// load header file
require_once('header.php');
require_once('menu.php');


// --------------------------------------------------------------------
// 
// DISPLAY PAGE CONTENT
// 
// --------------------------------------------------------------------

// display the template
$_TEMPLATE->display('user_create.tpl');


// --------------------------------------------------------------------
// 
// DISPLAY FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load footer file
require_once('footer.php');

?>