<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

// class includes
require_once('../lib/Config.class.php');
require_once('../lib/System.class.php');
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
	header("Location: ".$_CONFIG->APP_URL()."system_list.php");
	break;

case 'Update':

	// only update if we sent the post
	if ($_POST['referrer'] == 'system_update.php') {
	
		// create a new system instance
		$system = new System($_DB, $_E->decrypt($_POST['system_id']));
	
		// update the newly created system with sanitized input	
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

// load the template
require_once('../lib/Template.config.php');

// retrieve the existing system's values
$system = new System($_DB, $_E->decrypt($_POST['system_id']));

// assign the template values
$_TEMPLATE->assign('this_page',  'system_update.php');
$_TEMPLATE->assign('this_title', 'SYSTEM > update');
$_TEMPLATE->assign('menu_header', 'system');


$_TEMPLATE->assign('system_id',              $_POST['system_id']);
$_TEMPLATE->assign('system_name', $system->getSystemName());
$_TEMPLATE->assign('system_nickname', $system->getSystemNickname());
$_TEMPLATE->assign('system_desc', $system->getSystemDesc());
$_TEMPLATE->assign('system_type', $system->getSystemType());
$_TEMPLATE->assign('system_primary_office', $system->getSystemPrimaryOffice());
$_TEMPLATE->assign('system_availability', $system->getSystemAvailability());
$_TEMPLATE->assign('system_integrity', $system->getSystemIntegrity());
$_TEMPLATE->assign('system_confidentiality', $system->getSystemConfidentiality());
$_TEMPLATE->assign('system_tier', $system->getSystemTier());
$_TEMPLATE->assign('system_criticality_justification', $system->getSystemCriticalityJustification());
$_TEMPLATE->assign('system_sensitivity_justification', $system->getSystemSensitivityJustification());

$_TEMPLATE->assign('system_type_list', array('GENERAL SUPPORT SYSTEM', 'MAJOR APPLICATION'));
$_TEMPLATE->assign('level_list', array('NONE', 'LOW', 'MODERATE', 'HIGH'));
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
$_TEMPLATE->display('system_update.tpl');


// --------------------------------------------------------------------
// 
// DISPLAY FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load footer file
require_once('footer.php');

?>