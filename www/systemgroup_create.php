<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

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

case 'Create':

	// create systemgroup if we are referrer
	if ($_POST['referrer'] == 'systemgroup_create.php') {

		// create a new systemgroup instance
		$systemgroup = new SystemGroup($_DB);
	
		// create the systemgroup
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

require_once('../lib/Template.config.php');

$_TEMPLATE->assign('this_page',  'systemgroup_create.php');
$_TEMPLATE->assign('this_title', 'SYSTEMGROUP > create');
$_TEMPLATE->assign('menu_header', 'system group');


// --------------------------------------------------------------------
// 
// DISPLAY MENU AND HEADER
// 
// --------------------------------------------------------------------

// identify our page to the header
define('PAGE_NAME',  'systemgroup_create.php'); 
define('PAGE_TITLE', 'SystemGroup: create');

// load header file
require_once('header.php');
require_once('menu.php');


// --------------------------------------------------------------------
// 
// DISPLAY PAGE CONTENT
// 
// --------------------------------------------------------------------

// display the template
$_TEMPLATE->display('systemgroup_create.tpl');


// --------------------------------------------------------------------
// 
// DISPLAY FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load footer file
require_once('footer.php');

?>