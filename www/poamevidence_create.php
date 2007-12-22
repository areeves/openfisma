<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

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

case 'Create':

	// create poamevidence if we are referrer
	if ($_POST['referrer'] == 'poamevidence_create.php') {

		// create a new poamevidence instance
		$poamevidence = new PoamEvidence($_DB);
	
		// create the poamevidence
        $poamevidence->setPoamId($_DB->sanitize($_POST['poam_id']));
        $poamevidence->setEvSubmission($_DB->sanitize($_POST['ev_submission']));
        $poamevidence->setEvSubmittedBy($_DB->sanitize($_POST['ev_submitted_by']));
        $poamevidence->setEvDateSubmitted($_DB->sanitize($_POST['ev_date_submitted_Year'].'-'.$_POST['ev_date_submitted_Month'].'-'.$_POST['ev_date_submitted_Day']));
        $poamevidence->setEvSsoEvaluation($_DB->sanitize($_POST['ev_sso_evaluation']));
        $poamevidence->setEvDateSsoEvaluation($_DB->sanitize($_POST['ev_date_sso_evaluation_Year'].'-'.$_POST['ev_date_sso_evaluation_Month'].'-'.$_POST['ev_date_sso_evaluation_Day'].' '.$_POST['ev_date_sso_evaluation_Hour'].':'.$_POST['ev_date_sso_evaluation_Minute'].':'.$_POST['ev_date_sso_evaluation_Second']));
        $poamevidence->setEvFsaEvaluation($_DB->sanitize($_POST['ev_fsa_evaluation']));
        $poamevidence->setEvDateFsaEvaluation($_DB->sanitize($_POST['ev_date_fsa_evaluation_Year'].'-'.$_POST['ev_date_fsa_evaluation_Month'].'-'.$_POST['ev_date_fsa_evaluation_Day'].' '.$_POST['ev_date_fsa_evaluation_Hour'].':'.$_POST['ev_date_fsa_evaluation_Minute'].':'.$_POST['ev_date_fsa_evaluation_Second']));
	    $poamevidence->savePoamEvidence();
	   
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

require_once('../lib/Template.config.php');

$_TEMPLATE->assign('this_page',  'poamevidence_create.php');
$_TEMPLATE->assign('this_title', 'POAMEVIDENCE > create');
$_TEMPLATE->assign('menu_header', 'poam evidence');

require_once('../lib/PoamList.class.php');
$poams = new PoamList($_DB);
$poams->getPoamId(TRUE);
$poams->getPoamType();
$poam_id_list = $poams->getKeyList();
$poam_list = array();
foreach ($poam_id_list as $k=>$f) {
	$poam_list[$k] = 'poam #'.$k;
}
$_TEMPLATE->assign('poam_list', $poam_list);

require_once('../lib/UserList.class.php');
$users = new UserList($_DB, $_E);
$users->getUserId(TRUE);
$users->getUserName();
$_TEMPLATE->assign('user_list', $users->getKeyList());

$_TEMPLATE->assign('evaluation_list', array('NONE', 'APPROVED', 'DENIED', 'EXCLUDED'));
// --------------------------------------------------------------------
// 
// DISPLAY MENU AND HEADER
// 
// --------------------------------------------------------------------

// identify our page to the header
define('PAGE_NAME',  'poamevidence_create.php'); 
define('PAGE_TITLE', 'PoamEvidence: create');

// load header file
require_once('header.php');
require_once('menu.php');


// --------------------------------------------------------------------
// 
// DISPLAY PAGE CONTENT
// 
// --------------------------------------------------------------------

// display the template
$_TEMPLATE->display('poamevidence_create.tpl');


// --------------------------------------------------------------------
// 
// DISPLAY FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load footer file
require_once('footer.php');

?>