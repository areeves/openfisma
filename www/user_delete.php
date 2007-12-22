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

case 'Delete':

	// only update if we sent the post
	if ($_POST['referrer'] == 'user_delete.php') {
	
		// create a new user instance
		$user = new User($_DB, $_E, $_E->decrypt($_POST['user_id']));
		
		// delete the bastard
		$user->deleteUser();
	
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

// assign the template values
$_TEMPLATE->assign('this_page',  'user_delete.php');
$_TEMPLATE->assign('this_title', 'USER > delete');
$_TEMPLATE->assign('menu_header', 'user');

// retrieve the existing user's values
$user = new User($_DB, $_E, $_E->decrypt($_POST['user_id']));

$_TEMPLATE->assign('user_id',              $_POST['user_id']);
$_TEMPLATE->assign('user_name_first',      $user->getUserNameFirst()     );
$_TEMPLATE->assign('user_name_middle',     $user->getUserNameMiddle()    );
$_TEMPLATE->assign('user_name_last',       $user->getUserNameLast()      );

$_TEMPLATE->assign('user_phone_office',    $user->getUserPhoneOffice()   );
$_TEMPLATE->assign('user_phone_mobile',    $user->getUserPhoneMobile()   );

$_TEMPLATE->assign('user_name',            $user->getUserName()          );
$_TEMPLATE->assign('user_is_active',       $user->getUserIsActive()      );
$_TEMPLATE->assign('role_id',       $user->getUserRoleId()      );


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
$_TEMPLATE->display('user_delete.tpl');


// --------------------------------------------------------------------
// 
// DISPLAY FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load footer file
require_once('footer.php');

?>