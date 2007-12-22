<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

require_once('../lib/SystemGroup.class.php');
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

// create a systemgroup to view
$systemgroup = new SystemGroup($_DB);

// validate that the systemgroup actually exists

if ($systemgroup->systemgroupExists($_E->decrypt($_POST['sysgroup_id']))) {

	// retrieve the user
	$systemgroup->getSystemGroup($_E->decrypt($_POST['sysgroup_id']));
	
}

// redirect on bum systemgroup
else { header("Location: ".$_CONFIG->APP_URL()."systemgroup_list.php"); }


// --------------------------------------------------------------------
// 
// TEMPLATE POPULATION
// 
// --------------------------------------------------------------------

require_once('../lib/Template.config.php');

// assign the template values
$_TEMPLATE->assign('this_page',  'systemgroup_view.php');
$_TEMPLATE->assign('this_title', 'SYSTEMGROUP > view');
$_TEMPLATE->assign('menu_header', 'system group');


$_TEMPLATE->assign('sysgroup_id', $_POST['sysgroup_id']);

$_TEMPLATE->assign('sysgroup_name',      $systemgroup->getSysgroupName()     );
$_TEMPLATE->assign('sysgroup_nickname',      $systemgroup->getSysgroupNickname()     );
$_TEMPLATE->assign('sysgroup_is_identity',      $systemgroup->getSysgroupIsIdentity()     );

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
$_TEMPLATE->display('systemgroup_view.tpl');


// --------------------------------------------------------------------
// 
// FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load footer file
require_once('footer.php');

?>