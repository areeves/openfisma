<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

require_once('../lib/Config.class.php');
require_once('../lib/Template.config.php');

require_once('../lib/Filter.class.php');


// --------------------------------------------------------------------
// 
// FUNCTIONS
// 
// --------------------------------------------------------------------


// --------------------------------------------------------------------
// 
// HEADER VALUES
// 
// --------------------------------------------------------------------

$_TEMPLATE->assign('this_page',   'report_list.php');
$_TEMPLATE->assign('this_title',  'Reports');
$_TEMPLATE->assign('menu_header', 'reporting');


// 
// REPORT OPTIONS
// 


// --------------------------------------------------------------------
// 
// PAGE DISPLAY
// 
// --------------------------------------------------------------------

// load header file
require_once('header.php');
require_once('menu.php');


// load footer file
require_once('footer.php');



?>
