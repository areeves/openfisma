<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

require_once('../lib/UserGroup.class.php');
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

// create a usergroup to view
$usergroup = new UserGroup($_DB);

// validate that the usergroup actually exists

if ($usergroup->usergroupExists($_E->decrypt($_POST['user_group_id']))) {

	// retrieve the user
	$usergroup->getUserGroup($_E->decrypt($_POST['user_group_id']));
	
}

// redirect on bum usergroup
else { header("Location: ".$_CONFIG->APP_URL()."usergroup_list.php"); }


// --------------------------------------------------------------------
// 
// TEMPLATE POPULATION
// 
// --------------------------------------------------------------------

require_once('../lib/Template.config.php');

// assign the template values
$_TEMPLATE->assign('this_page',  'usergroup_view.php');
$_TEMPLATE->assign('this_title', 'USERGROUP > view');
$_TEMPLATE->assign('menu_header', 'user sysgroup');


$_TEMPLATE->assign('user_group_id', $_POST['user_group_id']);

$_TEMPLATE->assign('user_id',      $usergroup->getUserId()     );
$_TEMPLATE->assign('sysgroup_id',      $usergroup->getSysgroupId()     );


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
$_TEMPLATE->display('usergroup_view.tpl');


// --------------------------------------------------------------------
// 
// FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load footer file
require_once('footer.php');

?>