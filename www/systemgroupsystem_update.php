<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

// class includes
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

case 'Update':

	// only update if we sent the post
	if ($_POST['referrer'] == 'systemgroupsystem_update.php') {
	
		// create a new systemgroupsystem instance
		$systemgroupsystem = new SystemGroupSystem($_DB, unserialize($_E->decrypt($_POST['systemgroupsystem_id'])));
	
		// update the newly created systemgroupsystem with sanitized input	
		$systemgroupsystem->setSysgroupId($_DB->sanitize($_POST['sysgroup_id']));
		$systemgroupsystem->setSystemId($_DB->sanitize($_POST['system_id']));
	
		$systemgroupsystem->saveSystemGroupSystem(unserialize($_E->decrypt($_POST['systemgroupsystem_id'])));
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

// load the template
require_once('../lib/Template.config.php');

// retrieve the existing systemgroupsystem's values
$systemgroupsystem = new SystemGroupSystem($_DB, unserialize($_E->decrypt($_POST['systemgroupsystem_id'])));

// assign the template values
$_TEMPLATE->assign('this_page',  'systemgroupsystem_update.php');
$_TEMPLATE->assign('this_title', 'SYSTEMGROUPSYSTEM > update');
$_TEMPLATE->assign('menu_header', 'system group system');


$_TEMPLATE->assign('systemgroupsystem_id',              $_POST['systemgroupsystem_id']);
$_TEMPLATE->assign('system_id',      $systemgroupsystem->getSystemId()     );
$_TEMPLATE->assign('sysgroup_id',      $systemgroupsystem->getSysgroupId()     );

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
$_TEMPLATE->display('systemgroupsystem_update.tpl');


// --------------------------------------------------------------------
// 
// DISPLAY FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load footer file
require_once('footer.php');

?>