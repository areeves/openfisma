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

case 'Update':

	// only update if we sent the post
	if ($_POST['referrer'] == 'plugin_update.php') {
	
		// create a new plugin instance
		$plugin = new Plugin($_DB, $_E->decrypt($_POST['plugin_id']));
	
		// update the newly created plugin with sanitized input	
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

// load the template
require_once('../lib/Template.config.php');

// retrieve the existing plugin's values
$plugin = new Plugin($_DB, $_E->decrypt($_POST['plugin_id']));

// assign the template values
$_TEMPLATE->assign('this_page',  'plugin_update.php');
$_TEMPLATE->assign('this_title', 'PLUGIN > update');
$_TEMPLATE->assign('menu_header', 'plugin');


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
$_TEMPLATE->display('plugin_update.tpl');


// --------------------------------------------------------------------
// 
// DISPLAY FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load footer file
require_once('footer.php');

?>