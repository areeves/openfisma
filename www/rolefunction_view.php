<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

require_once('../lib/RoleFunction.class.php');
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

// create a rolefunction to view
$rolefunction = new RoleFunction($_DB);

// validate that the rolefunction actually exists

if ($rolefunction->rolefunctionExists($_E->decrypt($_POST['role_func_id']))) {

	// retrieve the user
	$rolefunction->getRoleFunction($_E->decrypt($_POST['role_func_id']));
	
}

// redirect on bum rolefunction
else { header("Location: ".$_CONFIG->APP_URL()."rolefunction_list.php"); }


// --------------------------------------------------------------------
// 
// TEMPLATE POPULATION
// 
// --------------------------------------------------------------------

require_once('../lib/Template.config.php');

// assign the template values
$_TEMPLATE->assign('this_page',  'rolefunction_view.php');
$_TEMPLATE->assign('this_title', 'ROLEFUNCTION > view');
$_TEMPLATE->assign('menu_header', 'role function');


$_TEMPLATE->assign('role_func_id', $_POST['role_func_id']);

$_TEMPLATE->assign('role_id',      $rolefunction->getRoleId()     );
$_TEMPLATE->assign('function_id',      $rolefunction->getFunctionId()     );

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
$_TEMPLATE->display('rolefunction_view.tpl');


// --------------------------------------------------------------------
// 
// FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load footer file
require_once('footer.php');

?>