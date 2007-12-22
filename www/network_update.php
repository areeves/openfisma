<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

// class includes
require_once('../lib/Config.class.php');
require_once('../lib/Network.class.php');
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
	header("Location: ".$_CONFIG->APP_URL()."network_list.php");
	break;

case 'Update':

	// only update if we sent the post
	if ($_POST['referrer'] == 'network_update.php') {
	
		// create a new network instance
		$network = new Network($_DB, $_E->decrypt($_POST['network_id']));
	
		// update the newly created network with sanitized input	
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

// load the template
require_once('../lib/Template.config.php');

// retrieve the existing network's values
$network = new Network($_DB, $_E->decrypt($_POST['network_id']));

// assign the template values
$_TEMPLATE->assign('this_page',  'network_update.php');
$_TEMPLATE->assign('this_title', 'NETWORK > update');
$_TEMPLATE->assign('menu_header', 'network');


$_TEMPLATE->assign('network_id',              $_POST['network_id']);

$_TEMPLATE->assign('network_name',      $network->getNetworkName()     );
$_TEMPLATE->assign('network_nickname',      $network->getNetworkNickname()     );
$_TEMPLATE->assign('network_desc',      $network->getNetworkDesc()     );


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
$_TEMPLATE->display('network_update.tpl');


// --------------------------------------------------------------------
// 
// DISPLAY FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load footer file
require_once('footer.php');

?>