<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

require_once('../lib/Config.class.php');
require_once('../lib/Network.class.php');
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
	header("Location: ".$_CONFIG->APP_URL()."network_list.php");
	break;

case 'Create':

	// create network if we are referrer
	if ($_POST['referrer'] == 'network_create.php') {

		// create a new network instance
		$network = new Network($_DB);
	
		// create the network
        $network->setNetworkName($_DB->sanitize($_POST['network_name']));
        $network->setNetworkNickname($_DB->sanitize($_POST['network_nickname']));
        $network->setNetworkDesc($_DB->sanitize($_POST['network_desc']));
        $network->saveNetwork();
	
		// return to the list
		header("Location: ".$_CONFIG->APP_URL()."network_list.php");
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

$_TEMPLATE->assign('this_page',  'network_create.php');
$_TEMPLATE->assign('this_title', 'NETWORK > create');
$_TEMPLATE->assign('menu_header', 'network');


// --------------------------------------------------------------------
// 
// DISPLAY MENU AND HEADER
// 
// --------------------------------------------------------------------

// identify our page to the header
define('PAGE_NAME',  'network_create.php'); 
define('PAGE_TITLE', 'Network: create');

// load header file
require_once('header.php');
require_once('menu.php');


// --------------------------------------------------------------------
// 
// DISPLAY PAGE CONTENT
// 
// --------------------------------------------------------------------

// display the template
$_TEMPLATE->display('network_create.tpl');


// --------------------------------------------------------------------
// 
// DISPLAY FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load footer file
require_once('footer.php');

?>