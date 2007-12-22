<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

require_once('../lib/Poam.class.php');
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

// create a poam to view
$poam = new Poam($_DB);

// validate that the poam actually exists

if ($poam->poamExists($_E->decrypt($_POST['poam_id']))) {

	// retrieve the user
	$poam->getPoam($_E->decrypt($_POST['poam_id']));
	
}

// redirect on bum poam
else { header("Location: ".$_CONFIG->APP_URL()."poam_list.php"); }


// --------------------------------------------------------------------
// 
// TEMPLATE POPULATION
// 
// --------------------------------------------------------------------

require_once('../lib/Template.config.php');

// assign the template values
$_TEMPLATE->assign('this_page',  'poam_view.php');
$_TEMPLATE->assign('this_title', 'POAM > view');
$_TEMPLATE->assign('menu_header', 'poam');


$_TEMPLATE->assign('poam_id', $_POST['poam_id']);

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

// button display
$_TEMPLATE->assign('show_cancel', 1);
$_TEMPLATE->assign('show_update', 1);
$_TEMPLATE->assign('show_delete', 1);


// --------------------------------------------------------------------
// 
// PAGE DISPLAY
// 
// --------------------------------------------------------------------

// show hdeader and menu
require_once('header.php');
require_once('menu.php');

// display the content
$_TEMPLATE->display('poam_view.tpl');

// display the footer
require_once('footer.php');

?>