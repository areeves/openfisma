<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

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

case 'Create':

	// create findingvuln if we are referrer
	if ($_POST['referrer'] == 'findingvuln_create.php') {

		// create a new findingvuln instance
		$findingvuln = new FindingVuln($_DB);
	
		// update the newly created findingvuln with sanitized input	
		$findingvuln->setFindingId($_DB->sanitize($_POST['finding_id']));
		$findingvuln->setVulnSeq($_DB->sanitize($_POST['vuln_seq']));
		$findingvuln->setVulnType($_DB->sanitize($_POST['vuln_type']));

		$findingvuln->saveFindingVuln();
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

require_once('../lib/Template.config.php');

$_TEMPLATE->assign('this_page',  'findingvuln_create.php');
$_TEMPLATE->assign('this_title', 'FINDINGVULN > create');
$_TEMPLATE->assign('menu_header', 'finding vuln');


// --------------------------------------------------------------------
// 
// DISPLAY MENU AND HEADER
// 
// --------------------------------------------------------------------

// identify our page to the header
define('PAGE_NAME',  'findingvuln_create.php'); 
define('PAGE_TITLE', 'FindingVuln: create');

// load header file
require_once('header.php');
require_once('menu.php');


// --------------------------------------------------------------------
// 
// DISPLAY PAGE CONTENT
// 
// --------------------------------------------------------------------

// display the template
$_TEMPLATE->display('findingvuln_create.tpl');


// --------------------------------------------------------------------
// 
// DISPLAY FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load footer file
require_once('footer.php');

?>