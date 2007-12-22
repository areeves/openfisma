<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

// class includes
require_once('../lib/Config.class.php');
require_once('../lib/UserGroup.class.php');
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
	header("Location: ".$_CONFIG->APP_URL()."usergroup_list.php");
	break;

case 'Delete':

	// only update if we sent the post
	if ($_POST['referrer'] == 'usergroup_delete.php') {
	
		// create a new usergroup instance
		$usergroup = new UserGroup($_DB, $_E->decrypt($_POST['user_group_id']));
		
		// delete the bastard
		$usergroup->deleteUserGroup();
	
		// return to the list
		header("Location: ".$_CONFIG->APP_URL()."usergroup_list.php");
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
$_TEMPLATE->assign('this_page',  'usergroup_delete.php');
$_TEMPLATE->assign('this_title', 'USERGROUP > delete');
$_TEMPLATE->assign('menu_header', 'user sysgroup');

// retrieve the existing usergroup's values
$usergroup = new UserGroup($_DB, $_E->decrypt($_POST['user_group_id']));

$_TEMPLATE->assign('user_group_id',              $_POST['user_group_id']);
$_TEMPLATE->assign('user_id',      $usergroup->getUserId()     );
$_TEMPLATE->assign('sysgroup_id',      $usergroup->getSysgroupId()     );


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
$_TEMPLATE->display('usergroup_delete.tpl');


// --------------------------------------------------------------------
// 
// DISPLAY FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load footer file
require_once('footer.php');

?>