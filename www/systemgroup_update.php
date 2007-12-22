<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

// class includes
require_once('../lib/Config.class.php');
require_once('../lib/SystemGroup.class.php');
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
	header("Location: ".$_CONFIG->APP_URL()."systemgroup_list.php");
	break;

case 'Update':

	// only update if we sent the post
	if ($_POST['referrer'] == 'systemgroup_update.php') {
	
		// create a new systemgroup instance
		$systemgroup = new SystemGroup($_DB, $_E->decrypt($_POST['sysgroup_id']));
	
		// update the newly created systemgroup with sanitized input	
		$systemgroup->setSysgroupName($_DB->sanitize($_POST['sysgroup_name']));
		$systemgroup->setSysgroupNickname($_DB->sanitize($_POST['sysgroup_nickname']));
		$systemgroup->setSysgroupIsIdentity($_DB->sanitize($_POST['sysgroup_is_identity']));
		
		$systemgroup->saveSystemGroup();

		// return to the list
		header("Location: ".$_CONFIG->APP_URL()."systemgroup_list.php");
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

// retrieve the existing systemgroup's values
$systemgroup = new SystemGroup($_DB, $_E->decrypt($_POST['sysgroup_id']));

// assign the template values
$_TEMPLATE->assign('this_page',  'systemgroup_update.php');
$_TEMPLATE->assign('this_title', 'SYSTEMGROUP > update');
$_TEMPLATE->assign('menu_header', 'system group');


$_TEMPLATE->assign('sysgroup_id',              $_POST['sysgroup_id']);
$_TEMPLATE->assign('sysgroup_name',      $systemgroup->getSysgroupName()     );
$_TEMPLATE->assign('sysgroup_nickname',      $systemgroup->getSysgroupNickname()     );
$_TEMPLATE->assign('sysgroup_is_identity',      $systemgroup->getSysgroupIsIdentity()     );


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
$_TEMPLATE->display('systemgroup_update.tpl');


// --------------------------------------------------------------------
// 
// DISPLAY FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load footer file
require_once('footer.php');

?>