<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

// class includes
require_once('../lib/Config.class.php');
require_once('../lib/RoleSysgroup.class.php');
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
	header("Location: ".$_CONFIG->APP_URL()."rolesysgroup_list.php");
	break;

case 'Delete':

	// only update if we sent the post
	if ($_POST['referrer'] == 'rolesysgroup_delete.php') {
	
		// create a new rolesysgroup instance
		$rolesysgroup = new RoleSysgroup($_DB, $_E->decrypt($_POST['role_group_id']));
		
		// delete the bastard
		$rolesysgroup->deleteRoleSysgroup();
	
		// return to the list
		header("Location: ".$_CONFIG->APP_URL()."rolesysgroup_list.php");
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
$_TEMPLATE->assign('this_page',  'rolesysgroup_delete.php');
$_TEMPLATE->assign('this_title', 'ROLESYSGROUP > delete');
$_TEMPLATE->assign('menu_header', 'role sysgroup');

// retrieve the existing rolesysgroup's values
$rolesysgroup = new RoleSysgroup($_DB, $_E->decrypt($_POST['role_group_id']));

$_TEMPLATE->assign('role_group_id',              $_POST['role_group_id']);
$_TEMPLATE->assign('role_id',      $rolesysgroup->getRoleId()     );
$_TEMPLATE->assign('sysgroup_id',      $rolesysgroup->getSysgroupId()     );

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
$_TEMPLATE->display('rolesysgroup_delete.tpl');


// --------------------------------------------------------------------
// 
// DISPLAY FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load footer file
require_once('footer.php');

?>