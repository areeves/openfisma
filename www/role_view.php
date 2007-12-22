<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

require_once('../lib/Role.class.php');
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

// create a role to view
$role = new Role($_DB);

// validate that the role actually exists

if ($role->roleExists($_E->decrypt($_POST['role_id']))) {

	// retrieve the user
	$role->getRole($_E->decrypt($_POST['role_id']));
	
}

// redirect on bum role
else { header("Location: ".$_CONFIG->APP_URL()."role_list.php"); }


// --------------------------------------------------------------------
// 
// TEMPLATE POPULATION
// 
// --------------------------------------------------------------------

require_once('../lib/Template.config.php');

// assign the template values
$_TEMPLATE->assign('this_page',  'role_view.php');
$_TEMPLATE->assign('this_title', 'ROLE > view');
$_TEMPLATE->assign('menu_header', 'role');


$_TEMPLATE->assign('role_id', $_POST['role_id']);

$_TEMPLATE->assign('role_name',      $role->getRoleName()     );
$_TEMPLATE->assign('role_nickname',      $role->getRoleNickname()     );
$_TEMPLATE->assign('role_desc',      $role->getRoleDesc()     );


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
$_TEMPLATE->display('role_view.tpl');


// --------------------------------------------------------------------
// 
// FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load footer file
require_once('footer.php');

?>