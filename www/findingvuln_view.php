<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

require_once('../lib/FindingVuln.class.php');
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

// create a findingvuln to view
$findingvuln = new FindingVuln($_DB);

// validate that the findingvuln actually exists

if ($findingvuln->findingvulnExists(unserialize($_E->decrypt($_POST['findingvuln_id'])))) {

	// retrieve the user
	$findingvuln->getFindingVuln(unserialize($_E->decrypt($_POST['findingvuln_id'])));
	
}

// redirect on bum findingvuln
else { header("Location: ".$_CONFIG->APP_URL()."findingvuln_list.php"); }


// --------------------------------------------------------------------
// 
// TEMPLATE POPULATION
// 
// --------------------------------------------------------------------

require_once('../lib/Template.config.php');

// assign the template values
$_TEMPLATE->assign('this_page',  'findingvuln_view.php');
$_TEMPLATE->assign('this_title', 'FINDINGVULN > view');
$_TEMPLATE->assign('menu_header', 'finding vuln');


$_TEMPLATE->assign('findingvuln_id', $_POST['findingvuln_id']);

$_TEMPLATE->assign('finding_id',      $findingvuln->getFindingId()     );
$_TEMPLATE->assign('vuln_seq',      $findingvuln->getVulnSeq()     );
$_TEMPLATE->assign('vuln_type',      $findingvuln->getVulnType()     );

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
$_TEMPLATE->display('findingvuln_view.tpl');


// --------------------------------------------------------------------
// 
// FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load footer file
require_once('footer.php');

?>