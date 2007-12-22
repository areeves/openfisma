<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

require_once('../lib/UserSystemRole.class.php');
require_once('../lib/Config.class.php');
require_once('../lib/Encryption.class.php');

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

// create a usersystemrole to view
$usersystemrole = new UserSystemRole($_DB);

// validate that the usersystemrole actually exists

if ($usersystemrole->usersystemroleExists(unserialize($_E->decrypt($_POST['usersystemrole_id'])))) {

	// retrieve the user
	$usersystemrole->getUserSystemRole(unserialize($_E->decrypt($_POST['usersystemrole_id'])));
	
}

// redirect on bum usersystemrole
else { header("Location: ".$_CONFIG->APP_URL()."usersystemrole_list.php"); }


// --------------------------------------------------------------------
// 
// TEMPLATE POPULATION
// 
// --------------------------------------------------------------------

require_once('../lib/Template.config.php');

// assign the template values
$_TEMPLATE->assign('this_page',  'usersystemrole_view.php');
$_TEMPLATE->assign('this_title', 'USERSYSTEMROLE > view');
$_TEMPLATE->assign('menu_header', 'user system role');

$_TEMPLATE->assign('usersystemrole_id', $_POST['usersystemrole_id']);

$_TEMPLATE->assign('user_id',      $usersystemrole->getUserId()     );
$_TEMPLATE->assign('system_id',      $usersystemrole->getSystemId()     );
$_TEMPLATE->assign('role_id',      $usersystemrole->getRoleId()     );
$_TEMPLATE->assign('sysgroup_id',      $usersystemrole->getSysgroupId()     );

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
$_TEMPLATE->display('usersystemrole_view.tpl');


// --------------------------------------------------------------------
// 
// FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load footer file
require_once('footer.php');

?>