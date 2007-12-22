<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

require_once('../lib/Findingsource.class.php');
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

// create a findingsource to view
$findingsource = new Findingsource($_DB);

// validate that the findingsource actually exists

if ($findingsource->findingsourceExists($_E->decrypt($_POST['source_id']))) {

	// retrieve the user
	$findingsource->getFindingSource($_E->decrypt($_POST['source_id']));
	
}

// redirect on bum findingsource
else { header("Location: ".$_CONFIG->APP_URL()."findingsource_list.php"); }


// --------------------------------------------------------------------
// 
// TEMPLATE POPULATION
// 
// --------------------------------------------------------------------

require_once('../lib/Template.config.php');

// assign the template values
$_TEMPLATE->assign('this_page',  'findingsource_view.php');
$_TEMPLATE->assign('this_title', 'FINDINGSOURCE > view');
$_TEMPLATE->assign('menu_header', 'finding source');


$_TEMPLATE->assign('source_id', $_POST['source_id']);

$_TEMPLATE->assign('source_name',      $findingsource->getSourceName()     );
$_TEMPLATE->assign('source_nickname',      $findingsource->getSourceNickname()     );
$_TEMPLATE->assign('source_desc',      $findingsource->getSourceDesc()     );

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
$_TEMPLATE->display('findingsource_view.tpl');


// --------------------------------------------------------------------
// 
// FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load footer file
require_once('footer.php');

?>