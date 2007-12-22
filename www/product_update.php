<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

// class includes
require_once('../lib/Config.class.php');
require_once('../lib/Product.class.php');
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
	header("Location: ".$_CONFIG->APP_URL()."product_list.php");
	break;

case 'Update':

	// only update if we sent the post
	if ($_POST['referrer'] == 'product_update.php') {
	
		// create a new product instance
		$product = new Product($_DB, $_E->decrypt($_POST['prod_id']));
	
		// update the newly created product with sanitized input	
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

// load the template
require_once('../lib/Template.config.php');

// retrieve the existing product's values
$product = new Product($_DB, $_E->decrypt($_POST['prod_id']));

// assign the template values
$_TEMPLATE->assign('this_page',  'product_update.php');
$_TEMPLATE->assign('this_title', 'PRODUCT > update');
$_TEMPLATE->assign('menu_header', 'product');


$_TEMPLATE->assign('prod_id',              $_POST['prod_id']);
$_TEMPLATE->assign('prod_nvd_defined',      $product->getProdNvdDefined()     );
$_TEMPLATE->assign('prod_meta',      $product->getProdMeta()     );
$_TEMPLATE->assign('prod_vendor',      $product->getProdVendor()     );
$_TEMPLATE->assign('prod_name',      $product->getProdName()     );
$_TEMPLATE->assign('prod_version',      $product->getProdVersion()     );
$_TEMPLATE->assign('prod_desc',      $product->getProdDesc()     );



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
$_TEMPLATE->display('product_update.tpl');


// --------------------------------------------------------------------
// 
// DISPLAY FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load footer file
require_once('footer.php');

?>