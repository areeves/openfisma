<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

require_once('../lib/RoleSysgroup.class.php');
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

// create a rolesysgroup to view
$rolesysgroup = new RoleSysgroup($_DB);

// validate that the rolesysgroup actually exists

if ($rolesysgroup->rolesysgroupExists($_E->decrypt($_POST['role_group_id']))) {

	// retrieve the user
	$rolesysgroup->getRoleSysgroup($_E->decrypt($_POST['role_group_id']));
	
}

// redirect on bum rolesysgroup
else { header("Location: ".$_CONFIG->APP_URL()."rolesysgroup_list.php"); }


// --------------------------------------------------------------------
// 
// TEMPLATE POPULATION
// 
// --------------------------------------------------------------------

require_once('../lib/Template.config.php');

// assign the template values
$_TEMPLATE->assign('this_page',  'rolesysgroup_view.php');
$_TEMPLATE->assign('this_title', 'ROLESYSGROUP > view');
$_TEMPLATE->assign('menu_header', 'role sysgroup');


$_TEMPLATE->assign('role_group_id', $_POST['role_group_id']);

$_TEMPLATE->assign('role_id',      $rolesysgroup->getRoleId()     );
$_TEMPLATE->assign('sysgroup_id',      $rolesysgroup->getSysgroupId()     );

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
$_TEMPLATE->display('rolesysgroup_view.tpl');


// --------------------------------------------------------------------
// 
// FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load footer file
require_once('footer.php');

?>