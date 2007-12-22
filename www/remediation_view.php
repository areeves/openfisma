<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

require_once('../lib/Config.class.php');
require_once('../lib/Encryption.class.php');
require_once('../lib/Template.config.php');

require_once('../lib/Asset.class.php');
require_once('../lib/Finding.class.php');
require_once('../lib/Poam.class.php');
require_once('../lib/PoamComment.class.php');
require_once('../lib/PoamEvidence.class.php');
require_once('../lib/System.class.php');
require_once('../lib/SystemList.class.php');


// --------------------------------------------------------------------
// 
// FORM HANDLING
// 
// --------------------------------------------------------------------

// redirect on selecting the list
if ($_POST['form_target'] == 'list') { header("Location: ".$_CONFIG->APP_URL()."remediation_list.php");  }

// fake a post to remediation if we just got referred by the list
if ($_POST['referrer'] == 'remediation_list.php') { $_POST['form_target'] = 'remediation'; }


// --------------------------------------------------------------------
// 
// DATA MANIPULATION
// 
// --------------------------------------------------------------------

// create a new POAM item regardless (need it for other classes anyways)
$poam = new Poam($_DB, $_POST['id']);


// --------------------------------------------------------------------
// 
// TEMPLATE POPULATION
// 
// --------------------------------------------------------------------

// assign the template values
$_TEMPLATE->assign('this_title',  'Remediation > view');
$_TEMPLATE->assign('this_page',   'remediation_view.php');
$_TEMPLATE->assign('referrer',    'remediation_view.php');
$_TEMPLATE->assign('menu_header', 'remediation');

// propagate the poam id to the remediation header page
$_TEMPLATE->assign('id', $_POST['id']);

// propagate the previous remediation list filters
// TODO: implement $_FILTER here

// assign the appropriate template values based on the form action
switch ($_POST['form_target']) {

 case 'remediation': 

   // handle the requested form action
   switch ($_POST['form_action']) {

	 //
	 // update the remediation
	 // 
   case 'update':

	 // track if or POAM is being modified
	 $modified = FALSE;
	 $reset_approval = FALSE;


	 $comment_topic = "UPDATE: ";
	 $comment_body  = "";
	 $message  = "<ul>";

	 // LEGACY POAM ID
	 if ($_POST['legacy_poam_id'] != $poam->getLegacyPoamId()) { 

	   // tag as being modified
	   $modified = TRUE; 
	   $message .= "<li>updated legacy POAM ID</li>";

	   // update the field
	   $poam->setLegacyPoamId($_POST['legacy_poam_id']);

	 }

	 // POAM IS REPEAT FINDING
	 if ($_POST['poam_is_repeat'] != $poam->getPoamIsRepeat()) { 

	   // tag as being modified
	   $modified = TRUE;
	   $message .= "<li>updated POAM repeat status</li>";

	   // update the field
	   $poam->setPoamIsRepeat($_POST['poam_is_repeat']);

	 }

	 // POAM IS PART OF REPEAT AUDITS
	 if ($_POST['poam_previous_audits'] != $poam->getPoamPreviousAudits()) { 

	   // tag as being modified
	   $modified = TRUE;
	   $reset_approval = TRUE;
	   $message .= "<li>updated POAM previous audits</li>";

	   // update the field
	   $poam->setPoamPreviousAudits($_POST['poam_previous_audits']);

	 }

	 // POAM TYPE
	 if ($_POST['poam_type'] != $poam->getPoamType()) { 

	   // tag as being modified
	   $modified = TRUE;
	   $reset_approval = TRUE;
	   $message .= "<li>updated POAM type</li>";

	   // update the field
	   $poam->setPoamType($_POST['poam_type']);

	 }

	 // ACTION OWNER
	 if ($_POST['poam_action_owner'] != $poam->getPoamActionOwner()) {

	   // tag as being modified
	   $modified = TRUE; 
	   $reset_approval = TRUE;
	   $message .= "<li>updated action owner</li>";

	   // update the field
	   $poam->setPoamActionOwner($_POST['poam_action_owner']);

	 }

	 // ACTION SUGGESTED
	 if ($_POST['poam_action_suggested'] != $poam->getPoamActionSuggested()) { 
	   
	   // 
	   $modified = TRUE;
	   $reset_approval = TRUE;
	   $message .= "<li>updated action suggested</li>";

	   // 
	   $poam->setPoamActionSuggested($_POST['poam_action_suggested']);

	 }

	 // ACTION PLANNED
	 if ($_POST['poam_action_planned'] != $poam->getPoamActionPlanned()){ 

	   // 
	   $modified = TRUE;
	   $reset_approval = TRUE;
	   $message .= "<li>updated action planned</li>";

	   // 
	   $poam->setPoamActionPlanned($_POST['poam_action_planned']);

	 }

	 // ACTION RESOURCES
	 if ($_POST['poam_action_resources'] != $poam->getPoamActionResources()) { 

	   // 
	   $modified = TRUE; 
	   $reset_approval = TRUE;
	   $message .= "<li>updated action resources</li>";

	   // 
	   $poam->setPoamActionResources($_POST['poam_action_resources']);

	 }

	 // THREAT SOURCE
	 if ($_POST['poam_threat_source'] != $poam->getPoamThreatSource()) { 

	   // 
	   $modified = TRUE;
	   $reset_approval = TRUE;
	   $message .= "<li>updated threat source</li>";

	   // 
	   $poam->setPoamThreatSource($_POST['poam_threat_source']);

	 }

	 // THREAT LEVEL
	 if ($_POST['poam_threat_level'] != $poam->getPoamThreatLevel()) {

	   // 
	   $modified = TRUE;
	   $reset_approval = TRUE;
	   $message .= "<li>updated threat level</li>";

	   // 
	   $poam->setPoamThreatLevel($_POST['poam_threat_level']);

	 }

	 // THREAT JUSTIFICATION
	 if ($_POST['poam_threat_justification'] != $poam->getPoamThreatJustification()) { 

	   // 
	   $modified = TRUE; 
	   $reset_approval = TRUE;
	   $message .= "<li>updated threat justification</li>";

	   // 
	   $poam->setPoamThreatJustification($_POST['poam_threat_justification']); 

	 }

	 // COUNTERMEASURE
	 if ($_POST['poam_cmeasure'] != $poam->getPoamCmeasure()) { 

	   // 
	   $modified = TRUE; 
	   $reset_approval = TRUE;
	   $message .= "<li>updated countermeasure</li>";

	   // 
	   $poam->setPoamCmeasure($_POST['poam_cmeasure']);

	 }

	 // COUNTERMEASURE EFFECTIVENESS
	 if ($_POST['poam_cmeasure_effectiveness'] != $poam->getPoamCmeasureEffectiveness()) { 

	   // 
	   $modified = TRUE; 
	   $reset_approval = TRUE;
	   $message .= "<li>updated countermeasure effectiveness</li>";

	   // 
	   $poam->setPoamCmeasureEffectiveness($_POST['poam_cmeasure_effectiveness']);

	 }

	 // COUNTERMEASURE JUSTIFICATION
	 if ($_POST['poam_cmeasure_justification'] != $poam->getPoamCmeasureJustification()) { 

	   // 
	   $modified = TRUE; 
	   $reset_approval = TRUE;
	   $message .= "<li>updated countermeasure justification</li>";

	   // 
	   $poam->setPoamCmeasureJustification($_POST['poam_cmeasure_justification']);

	 }

	 // ACTION STATUS 
	 if ($reset_approval || ($_POST['poam_action_status'] != $poam->getPoamActionStatus())) { 

	   // 
	   $modified = TRUE; 

	   // reset the approval to NONE (items were updated)
	   if ($reset_approval && ($poam->getPoamActionStatus() != 'NONE')) { 
		 
		 $poam->setPoamActionStatus('NONE');
		 $message .= "<li>reset action approval to ".$poam->getPoamActionStatus()."</li>";

	   }

	   // handle the 
	   else { 

		 // 
		 $poam->setPoamActionStatus($_POST['poam_action_status']);

		 // update the POAM status to EN on approval
		 if ($_POST['poam_action_status'] == 'APPROVED') { $poam->setPoamStatus('EN');   }
		 if ($_POST['poam_action_status'] == 'DENIED')   { $poam->setPoamStatus('OPEN'); }
		 if ($_POST['poam_action_status'] == 'NONE')     { $poam->setPoamStatus('OPEN'); }

		 // update our message
		 $message .= "<li>updated action approval to ".$poam->getPoamActionStatus()."</li>";


	   }

	 }

	 $message .= "</ul>";

	 // tag the changes
	 if ($modified) { 

	   // update the date modified and modifying user
	   $poam->setPoamDateModified();
	   //	   $poam->setPoamModifiedBy($_SESSION->getSessionUserId());

	   // save the changes to the POAM
	   $poam->savePoam();

	   // use the comment body as our message
	   $_TEMPLATE->assign('message', $message);

	 } // if modified
	 
	 break;

   } //  switch form_action

   // create poam_type_list, level_list, yesno_list arrays
   $poam_type_list = Array('NONE' => 'NONE', 'CAP' => 'CAP', 'FP' => 'FP', 'AR' => 'AR');
   $level_list     = Array('NONE' => 'NONE', 'LOW' => 'LOW', 'MODERATE' => 'MODERATE', 'HIGH' => 'HIGH');
   $approval_list  = Array('NONE' => 'NONE', 'DENIED' => 'DENIED', 'APPROVED' => 'APPROVED');
   $yesno_list     = Array(0 => 'NO', 1 => 'YES');

   // create keylist on SYSTEMS
   $sl = new SystemList($_DB);
   $sl->getSystemId(TRUE);
   $sl->getSystemNickname();
   $system_list = $sl->getKeyList();

   $_TEMPLATE->assign('poam_type_list',              $poam_type_list);
   $_TEMPLATE->assign('level_list',                  $level_list);
   $_TEMPLATE->assign('system_list',                 $system_list);
   $_TEMPLATE->assign('yesno_list',                  $yesno_list);
   $_TEMPLATE->assign('approval_list',               $approval_list);

   // populate the template fields
   $_TEMPLATE->assign('finding_id',                  $poam->getFindingId());
   $_TEMPLATE->assign('legacy_poam_id',              $poam->getLegacyPoamId());
   $_TEMPLATE->assign('poam_is_repeat',              $poam->getPoamIsRepeat());
   $_TEMPLATE->assign('poam_previous_audits',        $poam->getPoamPreviousAudits());
   $_TEMPLATE->assign('poam_type',                   $poam->getPoamType());
   $_TEMPLATE->assign('poam_status',                 $poam->getPoamStatus());
   $_TEMPLATE->assign('poam_blscr',                  $poam->getPoamBlscr());
   $_TEMPLATE->assign('poam_created_by',             $poam->getPoamCreatedBy());
   $_TEMPLATE->assign('poam_modified_by',            $poam->getPoamModifiedBy());
   $_TEMPLATE->assign('poam_closed_by',              $poam->getPoamClosedBy());
   $_TEMPLATE->assign('poam_date_created',           $poam->getPoamDateCreated());
   $_TEMPLATE->assign('poam_date_modified',          $poam->getPoamDateModified());
   $_TEMPLATE->assign('poam_date_closed',            $poam->getPoamDateClosed());
   $_TEMPLATE->assign('poam_action_owner',           $poam->getPoamActionOwner());
   $_TEMPLATE->assign('poam_action_suggested',       $poam->getPoamActionSuggested());
   $_TEMPLATE->assign('poam_action_planned',         $poam->getPoamActionPlanned());
   $_TEMPLATE->assign('poam_action_status',          $poam->getPoamActionStatus());
   $_TEMPLATE->assign('poam_action_approved_by',     $poam->getPoamActionApprovedBy());
   $_TEMPLATE->assign('poam_action_resources',       $poam->getPoamActionResources());
   $_TEMPLATE->assign('poam_action_date_est',        $poam->getPoamActionDateEst());
   $_TEMPLATE->assign('poam_action_date_actual',     $poam->getPoamActionDateActual());
   $_TEMPLATE->assign('poam_cmeasure',               $poam->getPoamCmeasure());
   $_TEMPLATE->assign('poam_cmeasure_effectiveness', $poam->getPoamCmeasureEffectiveness());
   $_TEMPLATE->assign('poam_cmeasure_justification', $poam->getPoamCmeasureJustification());
   $_TEMPLATE->assign('poam_threat_source',          $poam->getPoamThreatSource());
   $_TEMPLATE->assign('poam_threat_level',           $poam->getPoamThreatLevel());
   $_TEMPLATE->assign('poam_threat_justification',   $poam->getPoamThreatJustification());

   // remediation permissions
   $_TEMPLATE->assign('remediation_update_legacy_poam_id',         1);
   $_TEMPLATE->assign('remediation_update_is_repeat',              1);
   $_TEMPLATE->assign('remediation_update_previous_audits',        1);
   $_TEMPLATE->assign('remediation_update_type',                   1);
   $_TEMPLATE->assign('remediation_update_threat_source',          1);
   $_TEMPLATE->assign('remediation_update_threat_level',           1);
   $_TEMPLATE->assign('remediation_update_threat_justification',   1);
   $_TEMPLATE->assign('remediation_update_cmeasure',               1);
   $_TEMPLATE->assign('remediation_update_cmeasure_effectiveness', 1);
   $_TEMPLATE->assign('remediation_update_cmeasure_justification', 1);
   $_TEMPLATE->assign('remediation_update_action_owner',           1);
   $_TEMPLATE->assign('remediation_update_action_suggested',       1);
   $_TEMPLATE->assign('remediation_update_action_planned',         1);
   $_TEMPLATE->assign('remediation_update_action_resources',       1);
   $_TEMPLATE->assign('remediation_update_action_date_est',        1);
   $_TEMPLATE->assign('remediation_update_action_status',          1);

   // set up button display options
   $_TEMPLATE->assign('show_cancel', 0);
   $_TEMPLATE->assign('show_update', 1);
   $_TEMPLATE->assign('show_delete', 0);

   break;

 case 'finding': 

   // set up button display options
   $_TEMPLATE->assign('show_cancel', 0);
   $_TEMPLATE->assign('show_update', 0);
   $_TEMPLATE->assign('show_delete', 0);

   break;

 case 'evidence': 

   // set up button display options
   $_TEMPLATE->assign('show_cancel', 0);
   $_TEMPLATE->assign('show_update', 0);
   $_TEMPLATE->assign('show_delete', 0);

   break;

 case 'comments': 

   // set up button display options
   $_TEMPLATE->assign('show_cancel', 0);
   $_TEMPLATE->assign('show_update', 0);
   $_TEMPLATE->assign('show_delete', 0);

   break;

 case 'system': 

   // retrieve the system
   $system = new System($_DB);
   $system->getSystem($poam->getPoamActionOwner());

   // assign the template values
   $_TEMPLATE->assign('system_name',            $system->getSystemName());
   $_TEMPLATE->assign('system_nickname',        $system->getSystemNickname());
   $_TEMPLATE->assign('system_type',            $system->getSystemType());
   $_TEMPLATE->assign('system_desc',            $system->getSystemDesc());

   $_TEMPLATE->assign('system_confidentiality', $system->getSystemConfidentiality());
   $_TEMPLATE->assign('system_integrity',       $system->getSystemIntegrity());
   $_TEMPLATE->assign('system_availability',    $system->getSystemAvailability());

   $_TEMPLATE->assign('system_criticality_justification', $system->getSystemCriticalityJustification());
   $_TEMPLATE->assign('system_sensitivity_justification', $system->getSystemSensitivityJustification());
   //   $_TEMPLATE->assign('system_', $system->getSystem());

   // set up button display options
   $_TEMPLATE->assign('show_cancel', 0);
   $_TEMPLATE->assign('show_update', 0);
   $_TEMPLATE->assign('show_delete', 0);

   break;

 case 'asset': 

   // retrieve the asset (via the finding)
   $finding = new Finding($_DB);
   $asset   = new Asset($_DB);

   $finding->getFinding($poam->getFindingId());
   $asset->getAsset($finding->getAssetId());

   // assign the template values
   $_TEMPLATE->assign('asset_name',         $asset->getAssetName());
   $_TEMPLATE->assign('asset_date_created', $asset->getAssetDateCreated());
   $_TEMPLATE->assign('asset_source',       $asset->getAssetSource());
   //   $_TEMPLATE->assign('asset_', $asset->getAsset());

   // set up button display options
   $_TEMPLATE->assign('show_cancel', 0);
   $_TEMPLATE->assign('show_update', 0);
   $_TEMPLATE->assign('show_delete', 0);

   break;

 case 'blscr' : 

   // set up button display options
   $_TEMPLATE->assign('show_cancel', 0);
   $_TEMPLATE->assign('show_update', 0);
   $_TEMPLATE->assign('show_delete', 0);

   break;

 } // switch posted form action

// --------------------------------------------------------------------
// 
// PAGE DISPLAY
// 
// --------------------------------------------------------------------

// display header and menu
require_once('header.php');
require_once('menu.php');

// display the content template
$_TEMPLATE->display('remediation_header.tpl');
$_TEMPLATE->display('message.tpl');

// display the appropriate template based on the form action
switch ($_POST['form_target']) {

 case 'remediation': $_TEMPLATE->display('poam_update.tpl');       break;
 case 'finding'    : $_TEMPLATE->display('finding_view.tpl');      break;
 case 'evidence'   : $_TEMPLATE->display('poamevidence_view.tpl'); break;
 case 'comments'   : $_TEMPLATE->display('poamcomment_view.tpl');  break;
 case 'system'     : $_TEMPLATE->display('system_view.tpl');       break;
 case 'asset'      : $_TEMPLATE->display('asset_view.tpl');        break;
 case 'blscr'      : $_TEMPLATE->display('blscr_view.tpl');        break;

 } // switch posted form action

print "<pre>";
print_r($_POST);
print "</pre>";

// display the footer
require_once('footer.php');

?>