<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

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

case 'Create':

	// create rolesysgroup if we are referrer
	if ($_POST['referrer'] == 'rolesysgroup_create.php') {

		// create a new rolesysgroup instance
		$rolesysgroup = new RoleSysgroup($_DB);
	
		// create the rolesysgroup
		$rolesysgroup->setRoleId($_DB->sanitize($_POST['role_id']));
		$rolesysgroup->setSysgroupId($_DB->sanitize($_POST['sysgroup_id']));
		$rolesysgroup->saveRoleSysgroup();
	
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

require_once('../lib/Template.config.php');

$_TEMPLATE->assign('this_page',  'rolesysgroup_create.php');
$_TEMPLATE->assign('this_title', 'ROLESYSGROUP > create');
$_TEMPLATE->assign('menu_header', 'role sysgroup');

require_once('../lib/RoleList.class.php');
$roles = new RoleList($_DB);
$roles->getRoleId(TRUE);
$roles->getRoleNickname();
$_TEMPLATE->assign('role_list', $roles->getKeyList());
require_once('../lib/SystemGroupList.class.php');
$sysgroups = new SystemGroupList($_DB);
$sysgroups->getSysgroupId(TRUE);
$sysgroups->getSysgroupNickname();
$_TEMPLATE->assign('group_list', $sysgroups->getKeyList());
// --------------------------------------------------------------------
// 
// DISPLAY MENU AND HEADER
// 
// --------------------------------------------------------------------

// identify our page to the header
define('PAGE_NAME',  'rolesysgroup_create.php'); 
define('PAGE_TITLE', 'RoleSysgroup: create');

// load header file
require_once('header.php');
require_once('menu.php');


// --------------------------------------------------------------------
// 
// DISPLAY PAGE CONTENT
// 
// --------------------------------------------------------------------

// display the template
$_TEMPLATE->display('rolesysgroup_create.tpl');


// --------------------------------------------------------------------
// 
// DISPLAY FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load footer file
require_once('footer.php');

?>