<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

// class includes
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

case 'Update':

	// only update if we sent the post
	if ($_POST['referrer'] == 'user_update.php') {
	
		// create a new user instance
		$user = new User($_DB, $_E, $_E->decrypt($_POST['user_id']));
	
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

// load the template
require_once('../lib/Template.config.php');

// retrieve the existing user's values
$user = new User($_DB, $_E, $_E->decrypt($_POST['user_id']));

// assign the template values
$_TEMPLATE->assign('this_page',  'user_update.php');
$_TEMPLATE->assign('this_title', 'USER > update');
$_TEMPLATE->assign('menu_header', 'user');


$_TEMPLATE->assign('user_id',              $_POST['user_id']);
$_TEMPLATE->assign('user_name_first',      $user->getUserNameFirst()     );
$_TEMPLATE->assign('user_name_middle',     $user->getUserNameMiddle()    );
$_TEMPLATE->assign('user_name_last',       $user->getUserNameLast()      );

$_TEMPLATE->assign('user_phone_office',    $user->getUserPhoneOffice()   );
$_TEMPLATE->assign('user_phone_mobile',    $user->getUserPhoneMobile()   );

$_TEMPLATE->assign('user_name',            $user->getUserName()          );
$_TEMPLATE->assign('user_is_active',       $user->getUserIsActive()      );
$_TEMPLATE->assign('role_id',       $user->getUserRoleId()      );

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

// load header file
require_once('header.php');
require_once('menu.php');


// --------------------------------------------------------------------
// 
// DISPLAY PAGE CONTENT
// 
// --------------------------------------------------------------------

// display the template
$_TEMPLATE->display('user_update.tpl');


// --------------------------------------------------------------------
// 
// DISPLAY FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load footer file
require_once('footer.php');

?>