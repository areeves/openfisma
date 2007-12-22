<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

// class includes
require_once('../lib/Config.class.php');
require_once('../lib/Vuln.class.php');
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
	header("Location: ".$_CONFIG->APP_URL()."vuln_list.php");
	break;

case 'Delete':

	// only update if we sent the post
	if ($_POST['referrer'] == 'vuln_delete.php') {
	
		// create a new vuln instance
		$vuln = new Vuln($_DB, $_E->decrypt($_POST['vuln_seq']));
		
		// delete the bastard
		$vuln->deleteVuln();
	
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

// load the template
require_once('../lib/Template.config.php');

// assign the template values
$_TEMPLATE->assign('this_page',   'vuln_delete.php');
$_TEMPLATE->assign('this_title',  'VULN > delete');
$_TEMPLATE->assign('menu_header', 'vuln');

// retrieve the existing vuln's values
$vuln = new Vuln($_DB, $_E->decrypt($_POST['vuln_seq']));
$_TEMPLATE->assign('this_page',  'vuln_delete.php');
$_TEMPLATE->assign('this_title', 'VULN > delete');

$_TEMPLATE->assign('vuln_seq',              $_POST['vuln_seq']                  );
$_TEMPLATE->assign('vuln_type',             $vuln->getVulnType()                );
$_TEMPLATE->assign('vuln_desc_primary',     $vuln->getVulnDescPrimary()         );
$_TEMPLATE->assign('vuln_desc_secondary',   $vuln->getVulnDescSecondary()       );
$_TEMPLATE->assign('vuln_date_discovered',  $vuln->getVulnDateDiscovered()      );
$_TEMPLATE->assign('vuln_date_modified',    $vuln->getVulnDateModified()        );
$_TEMPLATE->assign('vuln_date_published',   $vuln->getVulnDatePublished()       );
$_TEMPLATE->assign('vuln_severity',         $vuln->getVulnSeverity()            );
$_TEMPLATE->assign('vuln_loss_availability',$vuln->getVulnLossAvailability()    );
$_TEMPLATE->assign('vuln_loss_confidentiality',$vuln->getVulnLossConfidentiality());
$_TEMPLATE->assign('vuln_loss_integrity',   $vuln->getVulnLossIntegrity()       );
$_TEMPLATE->assign('vuln_loss_security_admin',$vuln->getVulnLossSecurityAdmin() );
$_TEMPLATE->assign('vuln_loss_security_user',$vuln->getVulnLossSecurityUser()   );
$_TEMPLATE->assign('vuln_loss_security_other',$vuln->getVulnLossSecurityOther() );
$_TEMPLATE->assign('vuln_type_access',      $vuln->getVulnTypeAccess()          );
$_TEMPLATE->assign('vuln_type_input',       $vuln->getVulnTypeInput()           );
$_TEMPLATE->assign('vuln_type_input_bound', $vuln->getVulnTypeInputBound()      );
$_TEMPLATE->assign('vuln_type_input_buffer',$vuln->getVulnTypeInputBuffer()     );
$_TEMPLATE->assign('vuln_type_design',      $vuln->getVulnTypeDesign()          );
$_TEMPLATE->assign('vuln_type_exception',   $vuln->getVulnTypeException()       );
$_TEMPLATE->assign('vuln_type_environment', $vuln->getVulnTypeEnvironment()     );
$_TEMPLATE->assign('vuln_type_config',      $vuln->getVulnTypeConfig()          );
$_TEMPLATE->assign('vuln_type_race',        $vuln->getVulnTypeRace()            );
$_TEMPLATE->assign('vuln_type_other',       $vuln->getVulnTypeOther()           );
$_TEMPLATE->assign('vuln_range_local',      $vuln->getVulnRangeLocal()          );
$_TEMPLATE->assign('vuln_range_remote',     $vuln->getVulnRangeRemote()         );
$_TEMPLATE->assign('vuln_range_user',       $vuln->getVulnRangeUser()           );


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
$_TEMPLATE->display('vuln_delete.tpl');


// --------------------------------------------------------------------
// 
// DISPLAY FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load footer file
require_once('footer.php');

?>