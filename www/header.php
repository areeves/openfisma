<?php
/**********************************************************************
* FILE    : header.php
* PURPOSE : 
* 
* 1. performs necessary library configuration
* 2. handles user authentication and redirects when necessary
* 3. assigns necessary variables to header instance 
* 3. calls the header template   
*
* NOTES :
* 
* This header should not produce any outputs until the TEMPLATE section
*  
**********************************************************************/

// --------------------------------------------------------------------
// 
// INCLUDES
//
// --------------------------------------------------------------------

// application and template configurations
require_once('../lib/Config.class.php');
require_once('../lib/Template.config.php');

// session support
require_once('../lib/Session.class.php');


// global time variable
$_NOW      = date('Y-m-d');
$_NOW_NICE = date('l F j, Y');


// --------------------------------------------------------------------
// 
// OUTPUT BUFFERING
// 
// --------------------------------------------------------------------

if ($_CONFIG->BUFFER_OUTPUT()) { ob_start(); }


// --------------------------------------------------------------------
// 
// SESSION VALIDATION 
// 
// --------------------------------------------------------------------

//
// SESSION_ACTION requested, perform the action
//  
if (isset($_POST['SESSION_ACTION'])) {
	
	// handle the session action
	switch ($_POST['SESSION_ACTION']) {

		case 'LOGIN'  :

			// try to retrieve a valid user_id 
			$user_id = $_SESSION_USER->validLogin($_POST['SESSION_USERNAME'], $_POST['SESSION_PASSWORD']);

			// continue if we have a user
			if ($user_id) { 
				
				// start the session
				$_SESSION->startSession($user_id);
				
				// update the user's last login
				$_SESSION_USER->getUser($user_id);
				$_SESSION_USER->updateUserDateLastLogin();
				
			}				

			break;

		case 'LOGOUT' : $_SESSION->endSession(); break;
		
	}  // switch $_POST['SESSION_ACTION']
	
	// redirect to the dashboard page
	header('Location: dashboard.php');
	exit;
	
} // if isset($_POST['SESSION_ACTION'])

//
// SESSION_ACTION not specified, validate user and session
// 
else {

	// ACTIVE SESSION, grab the user's id
	if ($_SESSION->sessionIsActive()) { 
	
		// grab the session's user_id 
		$user_id = $_SESSION->getSessionUserId();
		
		// retrieve the user, if they exist
		if ($_SESSION_USER->userExists($user_id)) { $_SESSION_USER->getUser($user_id); }
		
		// otherwise end the session and redirect
		else { 
			
			// end the session
			$_SESSION->endSession();
			
			// redirect to the dashboard page
			header('Location: index.php');
			exit;
			
		} // userExists
		
	} // sessionIsActive()
	
	// session is not active, redirect to front page
	else {
		
		// if we are not already there, redirect to the dashboard page
		if ($_TEMPLATE->get_template_vars('this_page') != 'index.php') {
					
			header('Location: index.php');
			exit;
			
		} // if $_TEMPLATE->get_template_vars('this_page') 
		
	} // sessionActive() is false
	
} // SESSION_ACTION not set


// send Cache-Control headers if we made it this far
header('Cache-Control: public, no-cache, must-revalidate');

// --------------------------------------------------------------------
// 
// TEMPLATE 
// 
// --------------------------------------------------------------------

// assign session variables
$_TEMPLATE->assign('SESSION_ACTIVE',   $_SESSION->sessionIsActive());
$_TEMPLATE->assign('SESSION_USERNAME', $_SESSION_USER->getUserName());

$_TEMPLATE->assign('DATE', $_NOW_NICE);

// display header template
$_TEMPLATE->display('header.tpl');

?>
