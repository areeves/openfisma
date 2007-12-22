<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

require_once('../lib/Config.class.php');
require_once('../lib/Encryption.class.php');
require_once('../lib/User.class.php');


// --------------------------------------------------------------------
// 
// FORM HANDLING
// 
// --------------------------------------------------------------------


// --------------------------------------------------------------------
// 
// DATA MANIPULATION
// 
// --------------------------------------------------------------------

// create a user to view
$user = new User($_DB, $_E);

// validate that the user actually exists
if ($user->userExists($_E->decrypt($_POST['user_id']))) {

	// retrieve the user
	$user->getUser($_E->decrypt($_POST['user_id']));
	
}

// redirect on bum user
else { header("Location: ".$_CONFIG->APP_URL()."user_list.php"); }


// --------------------------------------------------------------------
// 
// TEMPLATE POPULATION
// 
// --------------------------------------------------------------------

require_once('../lib/Template.config.php');

// assign the template values
$_TEMPLATE->assign('this_page',  'user_view.php');
$_TEMPLATE->assign('this_title', 'USER > view');
$_TEMPLATE->assign('menu_header', 'user');


$_TEMPLATE->assign('user_id', $_POST['user_id']);

$_TEMPLATE->assign('user_name_first',      $user->getUserNameFirst()     );
$_TEMPLATE->assign('user_name_middle',     $user->getUserNameMiddle()    );
$_TEMPLATE->assign('user_name_last',       $user->getUserNameLast()      );

$_TEMPLATE->assign('user_phone_office',    $user->getUserPhoneOffice()   );
$_TEMPLATE->assign('user_phone_mobile',    $user->getUserPhoneMobile()   );

$_TEMPLATE->assign('user_name',            $user->getUserName()          );
$_TEMPLATE->assign('user_date_created',    $user->getUserDateCreated()   );
$_TEMPLATE->assign('user_date_last_login', $user->getUserDateLastLogin() );
$_TEMPLATE->assign('user_is_active',       $user->getUserIsActive()      );
$_TEMPLATE->assign('role_id',       $user->getUserRoleId()      );


// --------------------------------------------------------------------
// 
// HEADER SECTION
// 
// --------------------------------------------------------------------

// load header file
require_once('header.php');
require_once('menu.php');


// --------------------------------------------------------------------
// 
// BODY SECTION
// 
// --------------------------------------------------------------------

// display the template
$_TEMPLATE->display('user_view.tpl');


// --------------------------------------------------------------------
// 
// FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load footer file
require_once('footer.php');

?>
