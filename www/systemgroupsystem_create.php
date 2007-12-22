<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

require_once('../lib/Config.class.php');
require_once('../lib/SystemGroupSystem.class.php');
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
	header("Location: ".$_CONFIG->APP_URL()."systemgroupsystem_list.php");
	break;

case 'Create':

	// create systemgroupsystem if we are referrer
	if ($_POST['referrer'] == 'systemgroupsystem_create.php') {

		// create a new systemgroupsystem instance
		$systemgroupsystem = new SystemGroupSystem($_DB);
	
		// update the newly created systemgroupsystem with sanitized input	
		$systemgroupsystem->setSysgroupId($_DB->sanitize($_POST['sysgroup_id']));
		$systemgroupsystem->setSystemId($_DB->sanitize($_POST['system_id']));
	
		$systemgroupsystem->saveSystemGroupSystem();
		// return to the list
		header("Location: ".$_CONFIG->APP_URL()."systemgroupsystem_list.php");
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

$_TEMPLATE->assign('this_page',  'systemgroupsystem_create.php');
$_TEMPLATE->assign('this_title', 'SYSTEMGROUPSYSTEM > create');
$_TEMPLATE->assign('menu_header', 'system group system');


// --------------------------------------------------------------------
// 
// DISPLAY MENU AND HEADER
// 
// --------------------------------------------------------------------

// identify our page to the header
define('PAGE_NAME',  'systemgroupsystem_create.php'); 
define('PAGE_TITLE', 'SystemGroupSystem: create');

// load header file
require_once('header.php');
require_once('menu.php');


// --------------------------------------------------------------------
// 
// DISPLAY PAGE CONTENT
// 
// --------------------------------------------------------------------

// display the template
$_TEMPLATE->display('systemgroupsystem_create.tpl');


// --------------------------------------------------------------------
// 
// DISPLAY FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load footer file
require_once('footer.php');

?>