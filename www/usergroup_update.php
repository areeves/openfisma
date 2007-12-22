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

case 'Update':

	// only update if we sent the post
	if ($_POST['referrer'] == 'usergroup_update.php') {
	
		// create a new usergroup instance
		$usergroup = new UserGroup($_DB, $_E->decrypt($_POST['user_group_id']));
	
		// update the newly created usergroup with sanitized input	
		$usergroup->setUserId($_DB->sanitize($_POST['user_id']));
		$usergroup->setSysgroupId($_DB->sanitize($_POST['sysgroup_id']));
		$usergroup->saveUserGroup();
		
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

// retrieve the existing usergroup's values
$usergroup = new UserGroup($_DB, $_E->decrypt($_POST['user_group_id']));

// assign the template values
$_TEMPLATE->assign('this_page',  'usergroup_update.php');
$_TEMPLATE->assign('this_title', 'USERGROUP > update');
$_TEMPLATE->assign('menu_header', 'user sysgroup');


$_TEMPLATE->assign('user_group_id',              $_POST['user_group_id']);
$_TEMPLATE->assign('user_id',      $usergroup->getUserId()     );
$_TEMPLATE->assign('sysgroup_id',      $usergroup->getSysgroupId()     );

require_once('../lib/UserList.class.php');
$users = new UserList($_DB, $_E);
$users->getUserId(TRUE);
$users->getUserName();
$_TEMPLATE->assign('user_list', $users->getKeyList());

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

// load header file
require_once('header.php');
require_once('menu.php');


// --------------------------------------------------------------------
// 
// DISPLAY PAGE CONTENT
// 
// --------------------------------------------------------------------

// display the template
$_TEMPLATE->display('usergroup_update.tpl');


// --------------------------------------------------------------------
// 
// DISPLAY FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load footer file
require_once('footer.php');

?>