<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

require_once('../lib/SystemGroupSystem.class.php');
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

// create a systemgroupsystem to view
$systemgroupsystem = new SystemGroupSystem($_DB);

// validate that the systemgroupsystem actually exists

if ($systemgroupsystem->systemgroupsystemExists(unserialize($_E->decrypt($_POST['systemgroupsystem_id'])))) {

	// retrieve the user
	$systemgroupsystem->getSystemGroupSystem(unserialize($_E->decrypt($_POST['systemgroupsystem_id'])));
	
}

// redirect on bum systemgroupsystem
else { header("Location: ".$_CONFIG->APP_URL()."systemgroupsystem_list.php"); }


// --------------------------------------------------------------------
// 
// TEMPLATE POPULATION
// 
// --------------------------------------------------------------------

require_once('../lib/Template.config.php');

// assign the template values
$_TEMPLATE->assign('this_page',  'systemgroupsystem_view.php');
$_TEMPLATE->assign('this_title', 'SYSTEMGROUPSYSTEM > view');
$_TEMPLATE->assign('menu_header', 'system group system');


$_TEMPLATE->assign('systemgroupsystem_id', $_POST['systemgroupsystem_id']);
$_TEMPLATE->assign('system_id',      $systemgroupsystem->getSystemId()     );
$_TEMPLATE->assign('sysgroup_id',      $systemgroupsystem->getSysgroupId()     );


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
$_TEMPLATE->display('systemgroupsystem_view.tpl');


// --------------------------------------------------------------------
// 
// FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load footer file
require_once('footer.php');

?>