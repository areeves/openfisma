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

case 'Delete':

	// only update if we sent the post
	if ($_POST['referrer'] == 'systemgroup_delete.php') {
	
		// create a new systemgroup instance
		$systemgroup = new SystemGroup($_DB, $_E->decrypt($_POST['sysgroup_id']));
		
		// delete the bastard
		$systemgroup->deleteSystemGroup();
	
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

// assign the template values
$_TEMPLATE->assign('this_page',  'systemgroup_delete.php');
$_TEMPLATE->assign('this_title', 'SYSTEMGROUP > delete');
$_TEMPLATE->assign('menu_header', 'system group');

// retrieve the existing systemgroup's values
$systemgroup = new SystemGroup($_DB, $_E->decrypt($_POST['sysgroup_id']));

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
$_TEMPLATE->display('systemgroup_delete.tpl');


// --------------------------------------------------------------------
// 
// DISPLAY FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load footer file
require_once('footer.php');

?>