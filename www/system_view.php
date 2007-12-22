<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

require_once('../lib/System.class.php');
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

// create a system to view
$system = new System($_DB);

// validate that the system actually exists

if ($system->systemExists($_E->decrypt($_POST['system_id']))) {

	// retrieve the user
	$system->getSystem($_E->decrypt($_POST['system_id']));
	
}

// redirect on bum system
else { header("Location: ".$_CONFIG->APP_URL()."system_list.php"); }


// --------------------------------------------------------------------
// 
// TEMPLATE POPULATION
// 
// --------------------------------------------------------------------

require_once('../lib/Template.config.php');

// assign the template values
$_TEMPLATE->assign('this_page',  'system_view.php');
$_TEMPLATE->assign('this_title', 'SYSTEM > view');
$_TEMPLATE->assign('menu_header', 'system');


$_TEMPLATE->assign('system_id', $_POST['system_id']);

$_TEMPLATE->assign('system_name',                      $system->getSystemName());
$_TEMPLATE->assign('system_nickname',                  $system->getSystemNickname());
$_TEMPLATE->assign('system_desc',                      $system->getSystemDesc());
$_TEMPLATE->assign('system_type',                      $system->getSystemType());
$_TEMPLATE->assign('system_primary_office',            $system->getSystemPrimaryOffice());
$_TEMPLATE->assign('system_availability',              $system->getSystemAvailability());
$_TEMPLATE->assign('system_integrity',                 $system->getSystemIntegrity());
$_TEMPLATE->assign('system_confidentiality',           $system->getSystemConfidentiality());
$_TEMPLATE->assign('system_tier',                      $system->getSystemTier());
$_TEMPLATE->assign('system_criticality_justification', $system->getSystemCriticalityJustification());
$_TEMPLATE->assign('system_sensitivity_justification', $system->getSystemSensitivityJustification());

// set up button display options
$_TEMPLATE->assign('show_cancel', 1);
$_TEMPLATE->assign('show_update', 1);
$_TEMPLATE->assign('show_delete', 1);


// --------------------------------------------------------------------
// 
// PAGE DISPLAY
// 
// --------------------------------------------------------------------

// display header and menu
require_once('header.php');
require_once('menu.php');

// display the content
$_TEMPLATE->display('system_view.tpl');

// display footer
require_once('footer.php');

?>