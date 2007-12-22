<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

require_once('../lib/Config.class.php');
require_once('../lib/Product.class.php');
require_once('../lib/Database.class.php');
//require_once('../lib/Encryption.class.php');	


// --------------------------------------------------------------------
// 
// FORM HANDLING - NO OUTPUT SHOULD OCCUR HERE!
// 
// --------------------------------------------------------------------

// handle the form action
switch ($_POST['form_action']) {
case 'Cancel': 

	// action cancelled
	header("Location: ".$_CONFIG->APP_URL()."product_list.php");
	break;

case 'Create':

	// create product if we are referrer
	if ($_POST['referrer'] == 'product_create.php') {

		// create a new product instance
		$product = new Product($_DB);
	
		// create the product
        $product->setProdNvdDefined($_DB->sanitize($_POST['prod_nvd_defined']));
        $product->setProdMeta($_DB->sanitize($_POST['prod_meta']));
        $product->setProdVendor($_DB->sanitize($_POST['prod_vendor']));
        $product->setProdName($_DB->sanitize($_POST['prod_name']));
        $product->setProdVersion($_DB->sanitize($_POST['prod_version']));
        $product->setProdDesc($_DB->sanitize($_POST['prod_desc']));
        
        $product->saveProduct();
	
		// return to the list
		header("Location: ".$_CONFIG->APP_URL()."product_list.php");
		break;
		
	}
		
default: break;		
	
} // switch form_action


// --------------------------------------------------------------------
//
// TEMPLATE POPULATION
//
// --------------------------------------------------------------------

require_once('../lib/Template.config.php');

$_TEMPLATE->assign('this_page',  'product_create.php');
$_TEMPLATE->assign('this_title', 'PRODUCT > create');
$_TEMPLATE->assign('menu_header', 'product');


// --------------------------------------------------------------------
// 
// DISPLAY MENU AND HEADER
// 
// --------------------------------------------------------------------

// identify our page to the header
define('PAGE_NAME',  'product_create.php'); 
define('PAGE_TITLE', 'Product: create');

// load header file
require_once('header.php');
require_once('menu.php');


// --------------------------------------------------------------------
// 
// DISPLAY PAGE CONTENT
// 
// --------------------------------------------------------------------

// display the template
$_TEMPLATE->display('product_create.tpl');


// --------------------------------------------------------------------
// 
// DISPLAY FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load footer file
require_once('footer.php');

?>