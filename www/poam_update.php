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

case 'Update':

	// only update if we sent the post
	if ($_POST['referrer'] == 'poam_update.php') {
	
		// create a new poam instance
		$poam = new Poam($_DB, $_E->decrypt($_POST['poam_id']));
	
		// update the newly created poam with sanitized input	
        $poam->setFindingId($_DB->sanitize($_POST['finding_id']));
        $poam->setLegacyPoamId($_DB->sanitize($_POST['legacy_poam_id']));
        $poam->setPoamIsRepeat($_DB->sanitize($_POST['poam_is_repeat']));
        $poam->setPoamPreviousAudits($_DB->sanitize($_POST['poam_previous_audits']));
        $poam->setPoamType($_DB->sanitize($_POST['poam_type']));
        $poam->setPoamStatus($_DB->sanitize($_POST['poam_status']));
        $poam->setPoamBlscr($_DB->sanitize($_POST['poam_blscr']));
        $poam->setPoamCreatedBy($_DB->sanitize($_POST['poam_created_by']));
        $poam->setPoamModifiedBy($_DB->sanitize($_POST['poam_modified_by']));
        $poam->setPoamClosedBy($_DB->sanitize($_POST['poam_closed_by']));
        $poam->setPoamDateCreated($_DB->sanitize($_POST['poam_date_created_Year'].'-'.$_POST['poam_date_created_Month'].'-'.$_POST['poam_date_created_Day'].' '.$_POST['poam_date_created_Hour'].':'.$_POST['poam_date_created_Minute'].':'.$_POST['poam_date_created_Second']));
        $poam->setPoamDateModified($_DB->sanitize($_POST['poam_date_modified_Year'].'-'.$_POST['poam_date_modified_Month'].'-'.$_POST['poam_date_modified_Day'].' '.$_POST['poam_date_modified_Hour'].':'.$_POST['poam_date_modified_Minute'].':'.$_POST['poam_date_modified_Second']));
        $poam->setPoamDateClosed($_DB->sanitize($_POST['poam_date_closed_Year'].'-'.$_POST['poam_date_closed_Month'].'-'.$_POST['poam_date_closed_Day'].' '.$_POST['poam_date_closed_Hour'].':'.$_POST['poam_date_closed_Minute'].':'.$_POST['poam_date_closed_Second']));
        $poam->setPoamActionOwner($_DB->sanitize($_POST['poam_action_owner']));
        $poam->setPoamActionSuggested($_DB->sanitize($_POST['poam_action_suggested']));
        $poam->setPoamActionPlanned($_DB->sanitize($_POST['poam_action_planned']));
        $poam->setPoamActionStatus($_DB->sanitize($_POST['poam_action_status']));
        $poam->setPoamActionApprovedBy($_DB->sanitize($_POST['poam_action_approved_by']));
        $poam->setPoamCmeasure($_DB->sanitize($_POST['poam_cmeasure']));
        $poam->setPoamCmeasureEffectiveness($_DB->sanitize($_POST['poam_cmeasure_effectiveness']));
        $poam->setPoamCmeasureJustification($_DB->sanitize($_POST['poam_cmeasure_justification']));
        $poam->setPoamActionResources($_DB->sanitize($_POST['poam_action_resources']));
        $poam->setPoamActionDateEst($_DB->sanitize($_POST['poam_action_date_est_Year'].'-'.$_POST['poam_action_date_est_Month'].'-'.$_POST['poam_action_date_est_Day'].' '.$_POST['poam_action_date_est_Hour'].':'.$_POST['poam_action_date_est_Minute'].':'.$_POST['poam_action_date_est_Second']));
        $poam->setPoamActionDateActual($_DB->sanitize($_POST['poam_action_date_actual_Year'].'-'.$_POST['poam_action_date_actual_Month'].'-'.$_POST['poam_action_date_actual_Day'].' '.$_POST['poam_action_date_actual_Hour'].':'.$_POST['poam_action_date_actual_Minute'].':'.$_POST['poam_action_date_actual_Second']));
        $poam->setPoamThreatSource($_DB->sanitize($_POST['poam_threat_source']));
        $poam->setPoamThreatLevel($_DB->sanitize($_POST['poam_threat_level']));
        $poam->setPoamThreatJustification($_DB->sanitize($_POST['poam_threat_justification']));
        
        $poam->savePoam();

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

// retrieve the existing poam's values
$poam = new Poam($_DB, $_E->decrypt($_POST['poam_id']));

// assign the template values
$_TEMPLATE->assign('this_page',  'poam_update.php');
$_TEMPLATE->assign('this_title', 'POAM > update');
$_TEMPLATE->assign('menu_header', 'poam');


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

require_once('../lib/FindingList.class.php');
$findings = new FindingList($_DB);
$findings->getFindingId(TRUE);
$findings->getAssetId();
$finding_id_list = $findings->getKeyList();
$finding_list = array();
foreach ($finding_id_list as $k=>$f) {
	$finding_list[$k] = 'finding #'.$k;
}
$_TEMPLATE->assign('finding_list', $finding_list);

require_once('../lib/BlscrList.class.php');
$blscrs = new BlscrList($_DB);
$blscrs->getBlscrNumber(true);
$blscrs->getBlscrSubclass();
$_TEMPLATE->assign('blscr_list', $blscrs->getKeyList());

require_once('../lib/UserList.class.php');
$users = new UserList($_DB, $_E);
$users->getUserId(TRUE);
$users->getUserName();
$_TEMPLATE->assign('user_list', $users->getKeyList());

$_TEMPLATE->assign('level_list', array('NONE', 'LOW', 'MODERATE', 'HIGH'));
$_TEMPLATE->assign('poam_type_list', array('NONE', 'CAP', 'FP', 'AR'));
$_TEMPLATE->assign('poam_status_list', array('OPEN', 'EN', 'EP', 'ES', 'CLOSED'));
$_TEMPLATE->assign('poam_action_status_list', array('NONE', 'APPROVED', 'DENIED'));

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
$_TEMPLATE->display('poam_update.tpl');


// --------------------------------------------------------------------
// 
// DISPLAY FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load footer file
require_once('footer.php');

?>