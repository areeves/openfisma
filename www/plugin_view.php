<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

require_once('../lib/Plugin.class.php');
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

// create a plugin to view
$plugin = new Plugin($_DB);

// validate that the plugin actually exists

if ($plugin->pluginExists($_E->decrypt($_POST['plugin_id']))) {

	// retrieve the user
	$plugin->getPlugin($_E->decrypt($_POST['plugin_id']));
	
}

// redirect on bum plugin
else { header("Location: ".$_CONFIG->APP_URL()."plugin_list.php"); }


// --------------------------------------------------------------------
// 
// TEMPLATE POPULATION
// 
// --------------------------------------------------------------------

require_once('../lib/Template.config.php');

// assign the template values
$_TEMPLATE->assign('this_page',  'plugin_view.php');
$_TEMPLATE->assign('this_title', 'PLUGIN > view');
$_TEMPLATE->assign('menu_header', 'plugin');


$_TEMPLATE->assign('plugin_id', $_POST['plugin_id']);

$_TEMPLATE->assign('plugin_name',      $plugin->getPluginName()     );
$_TEMPLATE->assign('plugin_nickname',      $plugin->getPluginNickname()     );
$_TEMPLATE->assign('plugin_abbreviation',      $plugin->getPluginAbbreviation()     );
$_TEMPLATE->assign('plugin_desc',      $plugin->getPluginDesc()     );


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
$_TEMPLATE->display('plugin_view.tpl');


// --------------------------------------------------------------------
// 
// FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load footer file
require_once('footer.php');

?>