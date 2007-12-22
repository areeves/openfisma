<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

require_once('../lib/Template.config.php');

// --------------------------------------------------------------------
// 
// TEMPLATE POPULATION
// 
// --------------------------------------------------------------------

$_TEMPLATE->assign('this_page',  'index.php');
$_TEMPLATE->assign('this_title', 'Login');


// --------------------------------------------------------------------
// 
// FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load header file
require_once('header.php');

$_TEMPLATE->display('index.tpl');

// load footer file
require_once('footer.php');

?>
