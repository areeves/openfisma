<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

require_once('../lib/Blscr.class.php');
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

// create a blscr to view
$blscr = new Blscr($_DB);

// validate that the blscr actually exists

if ($blscr->blscrExists($_E->decrypt($_POST['blscr_number']))) {

	// retrieve the user
	$blscr->getBlscr($_E->decrypt($_POST['blscr_number']));
	
}

// redirect on bum blscr
else { header("Location: ".$_CONFIG->APP_URL()."blscr_list.php"); }


// --------------------------------------------------------------------
// 
// TEMPLATE POPULATION
// 
// --------------------------------------------------------------------

require_once('../lib/Template.config.php');

// assign the template values
$_TEMPLATE->assign('this_page',  'blscr_view.php');
$_TEMPLATE->assign('this_title', 'BLSCR > view');
$_TEMPLATE->assign('menu_header', 'blscr');


$_TEMPLATE->assign('blscr_number',       $_POST['blscr_number']);

$_TEMPLATE->assign('blscr_class',        $blscr->getBlscrClass());
$_TEMPLATE->assign('blscr_subclass',     $blscr->getBlscrSubclass());
$_TEMPLATE->assign('blscr_family',       $blscr->getBlscrFamily());
$_TEMPLATE->assign('blscr_control',      $blscr->getBlscrControl());
$_TEMPLATE->assign('blscr_guidance',     $blscr->getBlscrGuidance());
$_TEMPLATE->assign('blscr_low',          $blscr->getBlscrLow());
$_TEMPLATE->assign('blscr_moderate',     $blscr->getBlscrModerate());
$_TEMPLATE->assign('blscr_high',         $blscr->getBlscrHigh());
$_TEMPLATE->assign('blscr_enhancements', $blscr->getBlscrEnhancements());
$_TEMPLATE->assign('blscr_supplement',   $blscr->getBlscrSupplement());

// set up button display options
$_TEMPLATE->assign('show_cancel', 1);
$_TEMPLATE->assign('show_update', 1);
$_TEMPLATE->assign('show_delete', 1);


// --------------------------------------------------------------------
// 
// PAGE DISPLAY
// 
// --------------------------------------------------------------------

// display header and footer
require_once('header.php');
require_once('menu.php');

// display the content
$_TEMPLATE->display('blscr_view.tpl');

// display the footer
require_once('footer.php');

?>