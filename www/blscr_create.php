<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

require_once('../lib/Config.class.php');
require_once('../lib/Blscr.class.php');
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
	header("Location: ".$_CONFIG->APP_URL()."blscr_list.php");
	break;

case 'Create':

	// create blscr if we are referrer
	if ($_POST['referrer'] == 'blscr_create.php') {

		// create a new blscr instance
		$blscr = new Blscr($_DB);
	
		// create the blscr
		$blscr->setBlscrNumber($_DB->sanitize($_POST['blscr_number']));
        $blscr->setBlscrClass($_DB->sanitize($_POST['blscr_class']));
        $blscr->setBlscrSubclass($_DB->sanitize($_POST['blscr_subclass']));
        $blscr->setBlscrFamily($_DB->sanitize($_POST['blscr_family']));
        $blscr->setBlscrControl($_DB->sanitize($_POST['blscr_control']));
        $blscr->setBlscrGuidance($_DB->sanitize($_POST['blscr_guidance']));
        $blscr->setBlscrLow($_DB->sanitize($_POST['blscr_low']));
        $blscr->setBlscrModerate($_DB->sanitize($_POST['blscr_moderate']));
        $blscr->setBlscrHigh($_DB->sanitize($_POST['blscr_high']));
        $blscr->setBlscrEnhancements($_DB->sanitize($_POST['blscr_enhancements']));
        $blscr->setBlscrSupplement($_DB->sanitize($_POST['blscr_supplement']));
        
        $blscr->saveBlscr();
	
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

require_once('../lib/Template.config.php');

$_TEMPLATE->assign('this_page',  'blscr_create.php');
$_TEMPLATE->assign('this_title', 'BLSCR > create');
$_TEMPLATE->assign('menu_header', 'blscr');

$_TEMPLATE->assign('rand_key', rand(1,99999));

$_TEMPLATE->assign('blscr_class_list', array('MANAGEMENT', 'OPERATIONAL', 'TECHNICAL'));

// --------------------------------------------------------------------
// 
// DISPLAY MENU AND HEADER
// 
// --------------------------------------------------------------------

// identify our page to the header
define('PAGE_NAME',  'blscr_create.php'); 
define('PAGE_TITLE', 'Blscr: create');

// load header file
require_once('header.php');
require_once('menu.php');


// --------------------------------------------------------------------
// 
// DISPLAY PAGE CONTENT
// 
// --------------------------------------------------------------------

// display the template
$_TEMPLATE->display('blscr_create.tpl');


// --------------------------------------------------------------------
// 
// DISPLAY FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load footer file
require_once('footer.php');

?>