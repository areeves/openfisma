<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

// class includes
require_once('../lib/Config.class.php');
require_once('../lib/PoamEvidence.class.php');
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
	header("Location: ".$_CONFIG->APP_URL()."poamevidence_list.php");
	break;

case 'Delete':

	// only update if we sent the post
	if ($_POST['referrer'] == 'poamevidence_delete.php') {
	
		// create a new poamevidence instance
		$poamevidence = new PoamEvidence($_DB, $_E->decrypt($_POST['ev_id']));
		
		// delete the bastard
		$poamevidence->deletePoamEvidence();
	
		// return to the list
		header("Location: ".$_CONFIG->APP_URL()."poamevidence_list.php");
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
$_TEMPLATE->assign('this_page',  'poamevidence_delete.php');
$_TEMPLATE->assign('this_title', 'POAMEVIDENCE > delete');
$_TEMPLATE->assign('menu_header', 'poam evidence');

// retrieve the existing poamevidence's values
$poamevidence = new PoamEvidence($_DB, $_E->decrypt($_POST['ev_id']));

$_TEMPLATE->assign('ev_id',              $_POST['ev_id']);

$_TEMPLATE->assign('poam_id',      $poamevidence->getPoamId()     );
$_TEMPLATE->assign('ev_submission',      $poamevidence->getEvSubmission()     );
$_TEMPLATE->assign('ev_submitted_by',      $poamevidence->getEvSubmittedBy()     );
$_TEMPLATE->assign('ev_date_submitted',      $poamevidence->getEvDateSubmitted()     );
$_TEMPLATE->assign('ev_sso_evaluation',      $poamevidence->getEvSsoEvaluation()     );
$_TEMPLATE->assign('ev_date_sso_evaluation',      $poamevidence->getEvDateSsoEvaluation()     );
$_TEMPLATE->assign('ev_fsa_evaluation',      $poamevidence->getEvFsaEvaluation()     );
$_TEMPLATE->assign('ev_date_fsa_evaluation',      $poamevidence->getEvDateFsaEvaluation()     );

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
$_TEMPLATE->display('poamevidence_delete.tpl');


// --------------------------------------------------------------------
// 
// DISPLAY FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load footer file
require_once('footer.php');

?>