<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

// class includes
require_once('../lib/Config.class.php');
require_once('../lib/Blscr.class.php');
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
	header("Location: ".$_CONFIG->APP_URL()."blscr_list.php");
	break;

case 'Delete':

	// only update if we sent the post
	if ($_POST['referrer'] == 'blscr_delete.php') {
	
		// create a new blscr instance
		$blscr = new Blscr($_DB, $_E->decrypt($_POST['blscr_number']));
		
		// delete the bastard
		$blscr->deleteBlscr();
	
		// return to the list
		header("Location: ".$_CONFIG->APP_URL()."blscr_list.php");
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
$_TEMPLATE->assign('this_page',  'blscr_delete.php');
$_TEMPLATE->assign('this_title', 'BLSCR > delete');
$_TEMPLATE->assign('menu_header', 'blscr');

// retrieve the existing blscr's values
$blscr = new Blscr($_DB, $_E->decrypt($_POST['blscr_number']));

$_TEMPLATE->assign('blscr_number',              $_POST['blscr_number']);
$_TEMPLATE->assign('blscr_class',      $blscr->getBlscrClass()     );
$_TEMPLATE->assign('blscr_subclass',      $blscr->getBlscrSubclass()     );
$_TEMPLATE->assign('blscr_family',      $blscr->getBlscrFamily()     );
$_TEMPLATE->assign('blscr_control',      $blscr->getBlscrControl()     );
$_TEMPLATE->assign('blscr_guidance',      $blscr->getBlscrGuidance()     );
$_TEMPLATE->assign('blscr_low',      $blscr->getBlscrLow()     );
$_TEMPLATE->assign('blscr_moderate',      $blscr->getBlscrModerate()     );
$_TEMPLATE->assign('blscr_high',      $blscr->getBlscrHigh()     );
$_TEMPLATE->assign('blscr_enhancements',      $blscr->getBlscrEnhancements()     );
$_TEMPLATE->assign('blscr_supplement',      $blscr->getBlscrSupplement()     );


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
$_TEMPLATE->display('blscr_delete.tpl');


// --------------------------------------------------------------------
// 
// DISPLAY FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load footer file
require_once('footer.php');

?>