<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

// class includes
require_once('../lib/Config.class.php');
require_once('../lib/RoleFunction.class.php');
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
	header("Location: ".$_CONFIG->APP_URL()."rolefunction_list.php");
	break;

case 'Update':

	// only update if we sent the post
	if ($_POST['referrer'] == 'rolefunction_update.php') {
	
		// create a new rolefunction instance
		$rolefunction = new RoleFunction($_DB, $_E->decrypt($_POST['role_func_id']));
	
		// update the newly created rolefunction with sanitized input	
		$rolefunction->setRoleId($_DB->sanitize($_POST['role_id']));
		$rolefunction->setFunctionId($_DB->sanitize($_POST['function_id']));
        $rolefunction->saveRoleFunction();
        
		// return to the list
		header("Location: ".$_CONFIG->APP_URL()."rolefunction_list.php");
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

// retrieve the existing rolefunction's values
$rolefunction = new RoleFunction($_DB, $_E->decrypt($_POST['role_func_id']));

// assign the template values
$_TEMPLATE->assign('this_page',  'rolefunction_update.php');
$_TEMPLATE->assign('this_title', 'ROLEFUNCTION > update');
$_TEMPLATE->assign('menu_header', 'role function');


$_TEMPLATE->assign('role_func_id',              $_POST['role_func_id']);
$_TEMPLATE->assign('role_id',      $rolefunction->getRoleId()     );
$_TEMPLATE->assign('function_id',      $rolefunction->getFunctionId()     );

require_once('../lib/RoleList.class.php');
$roles = new RoleList($_DB);
$roles->getRoleId(TRUE);
$roles->getRoleNickname();
$_TEMPLATE->assign('role_list', $roles->getKeyList());

require_once('../lib/FunctionsList.class.php');
$funcs = new FunctionsList($_DB);
$funcs->getFunctionId(TRUE);
$funcs->getFunctionName();
$_TEMPLATE->assign('func_list', $funcs->getKeyList());

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
$_TEMPLATE->display('rolefunction_update.tpl');


// --------------------------------------------------------------------
// 
// DISPLAY FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load footer file
require_once('footer.php');

?>