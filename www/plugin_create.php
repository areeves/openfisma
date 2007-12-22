<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

require_once('../lib/Config.class.php');
require_once('../lib/Plugin.class.php');
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
	header("Location: ".$_CONFIG->APP_URL()."plugin_list.php");
	break;

case 'Create':

	// create plugin if we are referrer
	if ($_POST['referrer'] == 'plugin_create.php') {

		// create a new plugin instance
		$plugin = new Plugin($_DB);
	
		// create the plugin
        $plugin->setPluginName($_DB->sanitize($_POST['plugin_name']));
        $plugin->setPluginNickname($_DB->sanitize($_POST['plugin_nickname']));
        $plugin->setPluginAbbreviation($_DB->sanitize($_POST['plugin_abbreviation']));
        $plugin->setPluginDesc($_DB->sanitize($_POST['plugin_desc']));

        $plugin->savePlugin();
	
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

require_once('../lib/Template.config.php');

$_TEMPLATE->assign('this_page',  'plugin_create.php');
$_TEMPLATE->assign('this_title', 'PLUGIN > create');
$_TEMPLATE->assign('menu_header', 'plugin');


// --------------------------------------------------------------------
// 
// DISPLAY MENU AND HEADER
// 
// --------------------------------------------------------------------

// identify our page to the header
define('PAGE_NAME',  'plugin_create.php'); 
define('PAGE_TITLE', 'Plugin: create');

// load header file
require_once('header.php');
require_once('menu.php');


// --------------------------------------------------------------------
// 
// DISPLAY PAGE CONTENT
// 
// --------------------------------------------------------------------

// display the template
$_TEMPLATE->display('plugin_create.tpl');


// --------------------------------------------------------------------
// 
// DISPLAY FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load footer file
require_once('footer.php');

?>