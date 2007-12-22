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

$_TEMPLATE->assign('this_page',   'admin.php');
$_TEMPLATE->assign('this_title',  'Administration');
$_TEMPLATE->assign('menu_header', 'admin');


// --------------------------------------------------------------------
// 
// FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load header file
require_once('header.php');
require_once('menu.php');

print $_CONFIG;


// load footer file
require_once('footer.php');

?>
