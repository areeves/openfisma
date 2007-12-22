<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

require_once('../lib/PoamEvidence.class.php');
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

// create a poamevidence to view
$poamevidence = new PoamEvidence($_DB);

// validate that the poamevidence actually exists

if ($poamevidence->poamevidenceExists($_E->decrypt($_POST['ev_id']))) {

	// retrieve the user
	$poamevidence->getPoamEvidence($_E->decrypt($_POST['ev_id']));
	
}

// redirect on bum poamevidence
else { header("Location: ".$_CONFIG->APP_URL()."poamevidence_list.php"); }


// --------------------------------------------------------------------
// 
// TEMPLATE POPULATION
// 
// --------------------------------------------------------------------

require_once('../lib/Template.config.php');

// assign the template values
$_TEMPLATE->assign('this_page',  'poamevidence_view.php');
$_TEMPLATE->assign('this_title', 'POAMEVIDENCE > view');
$_TEMPLATE->assign('menu_header', 'poam evidence');


$_TEMPLATE->assign('ev_id', $_POST['ev_id']);

$_TEMPLATE->assign('poam_id',      $poamevidence->getPoamId()     );
$_TEMPLATE->assign('ev_submission',      $poamevidence->getEvSubmission()     );
$_TEMPLATE->assign('ev_submitted_by',      $poamevidence->getEvSubmittedBy()     );
$_TEMPLATE->assign('ev_date_submitted',      $poamevidence->getEvDateSubmitted()     );
$_TEMPLATE->assign('ev_sso_evaluation',      $poamevidence->getEvSsoEvaluation()     );
$_TEMPLATE->assign('ev_date_sso_evaluation',      $poamevidence->getEvDateSsoEvaluation()     );
$_TEMPLATE->assign('ev_fsa_evaluation',      $poamevidence->getEvFsaEvaluation()     );
$_TEMPLATE->assign('ev_date_fsa_evaluation',      $poamevidence->getEvDateFsaEvaluation()     );

// set up button display options
$_TEMPLATE->assign('show_cancel', 1);
$_TEMPLATE->assign('show_update', 1);
$_TEMPLATE->assign('show_delete', 1);


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
$_TEMPLATE->display('poamevidence_view.tpl');


// --------------------------------------------------------------------
// 
// FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load footer file
require_once('footer.php');

?>