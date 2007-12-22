<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

require_once('../lib/Config.class.php');
require_once('../lib/Vuln.class.php');
require_once('../lib/Database.class.php');
//require_once('../lib/Encryption.class.php');	


// --------------------------------------------------------------------
// 
// FORM HANDLING - NO OUTPUT SHOULD OCCUR HERE!
// 
// --------------------------------------------------------------------

// handle the form action
switch ($_POST['form_action']) {
case 'Cancel': 

	// action cancelled
	header("Location: ".$_CONFIG->APP_URL()."vuln_list.php");
	break;

case 'Create':

	// create vuln if we are referrer
	if ($_POST['referrer'] == 'vuln_create.php') {

		// create a new vuln instance
		$vuln = new Vuln($_DB);
	
		// create the vuln
        $vuln->setVulnType($_DB->sanitize($_POST['vuln_type']));
        $vuln->setVulnDescPrimary($_DB->sanitize($_POST['vuln_desc_primary']));
        $vuln->setVulnDescSecondary($_DB->sanitize($_POST['vuln_desc_secondary']));
        $vuln->setVulnDateDiscovered($_DB->sanitize($_POST['vuln_date_discovered_Year'].'-'.$_POST['vuln_date_discovered_Month'].'-'.$_POST['vuln_date_discovered_Day']));
        $vuln->setVulnDateModified($_DB->sanitize($_POST['vuln_date_modified_Year'].'-'.$_POST['vuln_date_modified_Month'].'-'.$_POST['vuln_date_modified_Day']));
        $vuln->setVulnDatePublished($_DB->sanitize($_POST['vuln_date_published_Year'].'-'.$_POST['vuln_date_published_Month'].'-'.$_POST['vuln_date_published_Day']));
        $vuln->setVulnSeverity($_DB->sanitize($_POST['vuln_severity']));
        $vuln->setVulnLossAvailability($_DB->sanitize($_POST['vuln_loss_availability']));
        $vuln->setVulnLossConfidentiality($_DB->sanitize($_POST['vuln_loss_confidentiality']));
        $vuln->setVulnLossIntegrity($_DB->sanitize($_POST['vuln_loss_integrity']));
        $vuln->setVulnLossSecurityAdmin($_DB->sanitize($_POST['vuln_loss_security_admin']));
        $vuln->setVulnLossSecurityUser($_DB->sanitize($_POST['vuln_loss_security_user']));
        $vuln->setVulnLossSecurityOther($_DB->sanitize($_POST['vuln_loss_security_other']));
        $vuln->setVulnTypeAccess($_DB->sanitize($_POST['vuln_type_access']));
        $vuln->setVulnTypeInput($_DB->sanitize($_POST['vuln_type_input']));
        $vuln->setVulnTypeInputBound($_DB->sanitize($_POST['vuln_type_input_bound']));
        $vuln->setVulnTypeInputBuffer($_DB->sanitize($_POST['vuln_type_input_buffer']));
        $vuln->setVulnTypeDesign($_DB->sanitize($_POST['vuln_type_design']));
        $vuln->setVulnTypeException($_DB->sanitize($_POST['vuln_type_exception']));
        $vuln->setVulnTypeEnvironment($_DB->sanitize($_POST['vuln_type_environment']));
        $vuln->setVulnTypeConfig($_DB->sanitize($_POST['vuln_type_config']));
        $vuln->setVulnTypeRace($_DB->sanitize($_POST['vuln_type_race']));
        $vuln->setVulnTypeOther($_DB->sanitize($_POST['vuln_type_other']));
        $vuln->setVulnRangeLocal($_DB->sanitize($_POST['vuln_range_local']));
        $vuln->setVulnRangeRemote($_DB->sanitize($_POST['vuln_range_remote']));
        $vuln->setVulnRangeUser($_DB->sanitize($_POST['vuln_range_user']));

        $vuln->saveVuln();
	
		// return to the list
		header("Location: ".$_CONFIG->APP_URL()."vuln_list.php");
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

$_TEMPLATE->assign('this_page',   'vuln_create.php');
$_TEMPLATE->assign('this_title',  'VULN > create');
$_TEMPLATE->assign('menu_header', 'vulns');


// --------------------------------------------------------------------
// 
// DISPLAY MENU AND HEADER
// 
// --------------------------------------------------------------------

// identify our page to the header
define('PAGE_NAME',  'vuln_create.php'); 
define('PAGE_TITLE', 'Vuln: create');

// load header file
require_once('header.php');
require_once('menu.php');


// --------------------------------------------------------------------
// 
// DISPLAY PAGE CONTENT
// 
// --------------------------------------------------------------------

// display the template
$_TEMPLATE->display('vuln_create.tpl');


// --------------------------------------------------------------------
// 
// DISPLAY FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load footer file
require_once('footer.php');

?>