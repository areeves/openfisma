<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

// class includes
require_once('../lib/Config.class.php');
require_once('../lib/Findingsource.class.php');
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
	header("Location: ".$_CONFIG->APP_URL()."findingsource_list.php");
	break;

case 'Delete':

	// only update if we sent the post
	if ($_POST['referrer'] == 'findingsource_delete.php') {
	
		// create a new findingsource instance
		$findingsource = new FindingSource($_DB, $_E->decrypt($_POST['source_id']));
		
		// delete the bastard
		$findingsource->deleteFindingSource();
	
		// return to the list
		header("Location: ".$_CONFIG->APP_URL()."findingsource_list.php");
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
$_TEMPLATE->assign('this_page',  'findingsource_delete.php');
$_TEMPLATE->assign('this_title', 'FINDINGSOURCE > delete');
$_TEMPLATE->assign('menu_header', 'finding source');

// retrieve the existing findingsource's values
$findingsource = new Findingsource($_DB, $_E->decrypt($_POST['source_id']));

$_TEMPLATE->assign('source_id',              $_POST['source_id']);
$_TEMPLATE->assign('source_name',      $findingsource->getSourceName()     );
$_TEMPLATE->assign('source_nickname',      $findingsource->getSourceNickname()     );
$_TEMPLATE->assign('source_desc',      $findingsource->getSourceDesc()     );


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
$_TEMPLATE->display('findingsource_delete.tpl');


// --------------------------------------------------------------------
// 
// DISPLAY FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load footer file
require_once('footer.php');

?>