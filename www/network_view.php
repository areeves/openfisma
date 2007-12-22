<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

require_once('../lib/Network.class.php');
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

// create a network to view
$network = new Network($_DB);

// validate that the network actually exists

if ($network->networkExists($_E->decrypt($_POST['network_id']))) {

	// retrieve the user
	$network->getNetwork($_E->decrypt($_POST['network_id']));
	
}

// redirect on bum network
else { header("Location: ".$_CONFIG->APP_URL()."network_list.php"); }


// --------------------------------------------------------------------
// 
// TEMPLATE POPULATION
// 
// --------------------------------------------------------------------

require_once('../lib/Template.config.php');

// assign the template values
$_TEMPLATE->assign('this_page',  'network_view.php');
$_TEMPLATE->assign('this_title', 'NETWORK > view');
$_TEMPLATE->assign('menu_header', 'network');


$_TEMPLATE->assign('network_id', $_POST['network_id']);

$_TEMPLATE->assign('network_name',      $network->getNetworkName()     );
$_TEMPLATE->assign('network_nickname',      $network->getNetworkNickname()     );
$_TEMPLATE->assign('network_desc',      $network->getNetworkDesc()     );


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
$_TEMPLATE->display('network_view.tpl');


// --------------------------------------------------------------------
// 
// FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load footer file
require_once('footer.php');

?>