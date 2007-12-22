<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

require_once('../lib/Config.class.php');
require_once('../lib/System.class.php');
require_once('../lib/Database.class.php');
//require_once('../lib/Encryption.class.php');	


// --------------------------------------------------------------------
// 
// FORM HANDLING - NO OUTPUT SHOULD OCCUR HERE!
// 
// --------------------------------------------------------------------

// handle the form action
switch ($_POST['form_action']) {
case 'Cancel': 

	// action cancelled
	header("Location: ".$_CONFIG->APP_URL()."system_list.php");
	break;

case 'Create':

	// create system if we are referrer
	if ($_POST['referrer'] == 'system_create.php') {

		// create a new system instance
		$system = new System($_DB);
	
		// create the system
        $system->setSystemName($_DB->sanitize($_POST['system_name']));
        $system->setSystemNickname($_DB->sanitize($_POST['system_nickname']));
        $system->setSystemDesc($_DB->sanitize($_POST['system_desc']));
        $system->setSystemType($_DB->sanitize($_POST['system_type']));
        $system->setSystemPrimaryOffice($_DB->sanitize($_POST['system_primary_office']));
        $system->setSystemAvailability($_DB->sanitize($_POST['system_availability']));
        $system->setSystemIntegrity($_DB->sanitize($_POST['system_integrity']));
        $system->setSystemConfidentiality($_DB->sanitize($_POST['system_confidentiality']));
        $system->setSystemTier($_DB->sanitize($_POST['system_tier']));
        $system->setSystemCriticalityJustification($_DB->sanitize($_POST['system_criticality_justification']));
        $system->setSystemSensitivityJustification($_DB->sanitize($_POST['system_sensitivity_justification']));
        
        $system->saveSystem();
        
		// return to the list
		header("Location: ".$_CONFIG->APP_URL()."system_list.php");
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

$_TEMPLATE->assign('this_page',  'system_create.php');
$_TEMPLATE->assign('this_title', 'SYSTEM > create');
$_TEMPLATE->assign('menu_header', 'system');

$_TEMPLATE->assign('system_type_list', array('GENERAL SUPPORT SYSTEM', 'MAJOR APPLICATION'));
$_TEMPLATE->assign('level_list', array('NONE', 'LOW', 'MODERATE', 'HIGH'));
// --------------------------------------------------------------------
// 
// DISPLAY MENU AND HEADER
// 
// --------------------------------------------------------------------

// identify our page to the header
define('PAGE_NAME',  'system_create.php'); 
define('PAGE_TITLE', 'System: create');

// load header file
require_once('header.php');
require_once('menu.php');


// --------------------------------------------------------------------
// 
// DISPLAY PAGE CONTENT
// 
// --------------------------------------------------------------------

// display the template
$_TEMPLATE->display('system_create.tpl');


// --------------------------------------------------------------------
// 
// DISPLAY FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load footer file
require_once('footer.php');

?>