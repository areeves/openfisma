<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

// class includes
require_once('../lib/Config.class.php');
require_once('../lib/Poam.class.php');
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
	header("Location: ".$_CONFIG->APP_URL()."poam_list.php");
	break;

case 'Delete':

	// only update if we sent the post
	if ($_POST['referrer'] == 'poam_delete.php') {
	
		// create a new poam instance
		$poam = new Poam($_DB, $_E->decrypt($_POST['poam_id']));
		
		// delete the bastard
		$poam->deletePoam();
	
		// return to the list
		header("Location: ".$_CONFIG->APP_URL()."poam_list.php");
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
$_TEMPLATE->assign('this_page',  'poam_delete.php');
$_TEMPLATE->assign('this_title', 'POAM > delete');
$_TEMPLATE->assign('menu_header', 'poam');

// retrieve the existing poam's values
$poam = new Poam($_DB, $_E->decrypt($_POST['poam_id']));

$_TEMPLATE->assign('poam_id',              $_POST['poam_id']);
$_TEMPLATE->assign('finding_id',      $poam->getFindingId()     );
$_TEMPLATE->assign('legacy_poam_id',      $poam->getLegacyPoamId()     );
$_TEMPLATE->assign('poam_is_repeat',      $poam->getPoamIsRepeat()     );
$_TEMPLATE->assign('poam_previous_audits',      $poam->getPoamPreviousAudits()     );
$_TEMPLATE->assign('poam_type',      $poam->getPoamType()     );
$_TEMPLATE->assign('poam_status',      $poam->getPoamStatus()     );
$_TEMPLATE->assign('poam_blscr',      $poam->getPoamBlscr()     );
$_TEMPLATE->assign('poam_created_by',      $poam->getPoamCreatedBy()     );
$_TEMPLATE->assign('poam_modified_by',      $poam->getPoamModifiedBy()     );
$_TEMPLATE->assign('poam_closed_by',      $poam->getPoamClosedBy()     );
$_TEMPLATE->assign('poam_date_created',      $poam->getPoamDateCreated()     );
$_TEMPLATE->assign('poam_date_modified',      $poam->getPoamDateModified()     );
$_TEMPLATE->assign('poam_date_closed',      $poam->getPoamDateClosed()     );
$_TEMPLATE->assign('poam_action_owner',      $poam->getPoamActionOwner()     );
$_TEMPLATE->assign('poam_action_suggested',      $poam->getPoamActionSuggested()     );
$_TEMPLATE->assign('poam_action_planned',      $poam->getPoamActionPlanned()     );
$_TEMPLATE->assign('poam_action_status',      $poam->getPoamActionStatus()     );
$_TEMPLATE->assign('poam_action_approved_by',      $poam->getPoamActionApprovedBy()     );
$_TEMPLATE->assign('poam_cmeasure',      $poam->getPoamCmeasure()     );
$_TEMPLATE->assign('poam_cmeasure_effectiveness',      $poam->getPoamCmeasureEffectiveness()     );
$_TEMPLATE->assign('poam_cmeasure_justification',      $poam->getPoamCmeasureJustification()     );
$_TEMPLATE->assign('poam_action_resources',      $poam->getPoamActionResources()     );
$_TEMPLATE->assign('poam_action_date_est',      $poam->getPoamActionDateEst()     );
$_TEMPLATE->assign('poam_action_date_actual',      $poam->getPoamActionDateActual()     );
$_TEMPLATE->assign('poam_threat_source',      $poam->getPoamThreatSource()     );
$_TEMPLATE->assign('poam_threat_level',      $poam->getPoamThreatLevel()     );
$_TEMPLATE->assign('poam_threat_justification',      $poam->getPoamThreatJustification()     );

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
$_TEMPLATE->display('poam_delete.tpl');


// --------------------------------------------------------------------
// 
// DISPLAY FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load footer file
require_once('footer.php');

?>