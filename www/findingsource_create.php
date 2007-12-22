<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

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

case 'Create':

	// create findingsource if we are referrer
	if ($_POST['referrer'] == 'findingsource_create.php') {

		// create a new findingsource instance
		$findingsource = new Findingsource($_DB);
	
		// create the findingsource
		$findingsource->setSourceName($_DB->sanitize($_POST['source_name']));
		$findingsource->setSourceNickname($_DB->sanitize($_POST['source_nickname']));
		$findingsource->setSourceDesc($_DB->sanitize($_POST['source_desc']));
		
		$findingsource->saveFindingSource();
	
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

require_once('../lib/Template.config.php');

$_TEMPLATE->assign('this_page',  'findingsource_create.php');
$_TEMPLATE->assign('this_title', 'FINDINGSOURCE > create');
$_TEMPLATE->assign('menu_header', 'finding source');


// --------------------------------------------------------------------
// 
// DISPLAY MENU AND HEADER
// 
// --------------------------------------------------------------------

// identify our page to the header
define('PAGE_NAME',  'findingsource_create.php'); 
define('PAGE_TITLE', 'Findingsource: create');

// load header file
require_once('header.php');
require_once('menu.php');


// --------------------------------------------------------------------
// 
// DISPLAY PAGE CONTENT
// 
// --------------------------------------------------------------------

// display the template
$_TEMPLATE->display('findingsource_create.tpl');


// --------------------------------------------------------------------
// 
// DISPLAY FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load footer file
require_once('footer.php');

?>