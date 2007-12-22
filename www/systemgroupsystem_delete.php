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

case 'Delete':

	// only update if we sent the post
	if ($_POST['referrer'] == 'systemgroupsystem_delete.php') {
	
		// create a new systemgroupsystem instance
		$systemgroupsystem = new SystemGroupSystem($_DB, unserialize($_E->decrypt($_POST['systemgroupsystem_id'])));
		
		// delete the bastard
		$systemgroupsystem ->deleteSystemGroupSystem(unserialize($_E->decrypt($_POST['systemgroupsystem_id'])));
	
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

// assign the template values
$_TEMPLATE->assign('this_page',  'systemgroupsystem_delete.php');
$_TEMPLATE->assign('this_title', 'SYSTEMGROUPSYSTEM > delete');
$_TEMPLATE->assign('menu_header', 'system group system');

// retrieve the existing systemgroupsystem's values
$systemgroupsystem = new SystemGroupSystem($_DB, unserialize($_E->decrypt($_POST['systemgroupsystem_id'])));

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
$_TEMPLATE->display('systemgroupsystem_delete.tpl');


// --------------------------------------------------------------------
// 
// DISPLAY FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load footer file
require_once('footer.php');

?>