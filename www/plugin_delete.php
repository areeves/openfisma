<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

// class includes
require_once('../lib/Config.class.php');
require_once('../lib/Plugin.class.php');
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
	header("Location: ".$_CONFIG->APP_URL()."plugin_list.php");
	break;

case 'Delete':

	// only update if we sent the post
	if ($_POST['referrer'] == 'plugin_delete.php') {
	
		// create a new plugin instance
		$plugin = new Plugin($_DB, $_E->decrypt($_POST['plugin_id']));
		
		// delete the bastard
		$plugin->deletePlugin();
	
		// return to the list
		header("Location: ".$_CONFIG->APP_URL()."plugin_list.php");
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
$_TEMPLATE->assign('this_page',  'plugin_delete.php');
$_TEMPLATE->assign('this_title', 'PLUGIN > delete');
$_TEMPLATE->assign('menu_header', 'plugin');

// retrieve the existing plugin's values
$plugin = new Plugin($_DB, $_E->decrypt($_POST['plugin_id']));

$_TEMPLATE->assign('plugin_id',              $_POST['plugin_id']);
$_TEMPLATE->assign('plugin_name',      $plugin->getPluginName()     );
$_TEMPLATE->assign('plugin_nickname',      $plugin->getPluginNickname()     );
$_TEMPLATE->assign('plugin_abbreviation',      $plugin->getPluginAbbreviation()     );
$_TEMPLATE->assign('plugin_desc',      $plugin->getPluginDesc()     );


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
$_TEMPLATE->display('plugin_delete.tpl');


// --------------------------------------------------------------------
// 
// DISPLAY FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load footer file
require_once('footer.php');

?>