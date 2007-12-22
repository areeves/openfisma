<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

// class includes
require_once('../lib/Config.class.php');
require_once('../lib/FindingVuln.class.php');
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
	header("Location: ".$_CONFIG->APP_URL()."findingvuln_list.php");
	break;

case 'Update':

	// only update if we sent the post
	if ($_POST['referrer'] == 'findingvuln_update.php') {
	
		// create a new findingvuln instance
		$findingvuln = new FindingVuln($_DB, unserialize($_E->decrypt($_POST['findingvuln_id'])));
	
		// update the newly created findingvuln with sanitized input	
		$findingvuln->setFindingId($_DB->sanitize($_POST['finding_id']));
		$findingvuln->setVulnSeq($_DB->sanitize($_POST['vuln_seq']));
		$findingvuln->setVulnType($_DB->sanitize($_POST['vuln_type']));

		$findingvuln->saveFindingVuln(unserialize($_E->decrypt($_POST['findingvuln_id'])));
		
		// return to the list
		header("Location: ".$_CONFIG->APP_URL()."findingvuln_list.php");
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

// retrieve the existing findingvuln's values
$findingvuln = new FindingVuln($_DB, unserialize($_E->decrypt($_POST['findingvuln_id'])));

// assign the template values
$_TEMPLATE->assign('this_page',  'findingvuln_update.php');
$_TEMPLATE->assign('this_title', 'FINDINGVULN > update');
$_TEMPLATE->assign('menu_header', 'finding vuln');


$_TEMPLATE->assign('findingvuln_id',              $_POST['findingvuln_id']);
$_TEMPLATE->assign('finding_id',      $findingvuln->getFindingId()     );
$_TEMPLATE->assign('vuln_seq',      $findingvuln->getVulnSeq()     );
$_TEMPLATE->assign('vuln_type',      $findingvuln->getVulnType()     );

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
$_TEMPLATE->display('findingvuln_update.tpl');


// --------------------------------------------------------------------
// 
// DISPLAY FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load footer file
require_once('footer.php');

?>