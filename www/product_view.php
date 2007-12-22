<?php

// --------------------------------------------------------------------
// 
// INCLUDES
// 
// --------------------------------------------------------------------

require_once('../lib/Product.class.php');
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

// create a product to view
$product = new Product($_DB);

// validate that the product actually exists

if ($product->productExists($_E->decrypt($_POST['prod_id']))) {

	// retrieve the user
	$product->getProduct($_E->decrypt($_POST['prod_id']));
	
}

// redirect on bum product
else { header("Location: ".$_CONFIG->APP_URL()."product_list.php"); }


// --------------------------------------------------------------------
// 
// TEMPLATE POPULATION
// 
// --------------------------------------------------------------------

require_once('../lib/Template.config.php');

// assign the template values
$_TEMPLATE->assign('this_page',  'product_view.php');
$_TEMPLATE->assign('this_title', 'PRODUCT > view');
$_TEMPLATE->assign('menu_header', 'product');


$_TEMPLATE->assign('prod_id', $_POST['prod_id']);

$_TEMPLATE->assign('prod_nvd_defined',      $product->getProdNvdDefined()     );
$_TEMPLATE->assign('prod_meta',      $product->getProdMeta()     );
$_TEMPLATE->assign('prod_vendor',      $product->getProdVendor()     );
$_TEMPLATE->assign('prod_name',      $product->getProdName()     );
$_TEMPLATE->assign('prod_version',      $product->getProdVersion()     );
$_TEMPLATE->assign('prod_desc',      $product->getProdDesc()     );

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
$_TEMPLATE->display('product_view.tpl');


// --------------------------------------------------------------------
// 
// FOOTER SECTION - none for the index page
// 
// --------------------------------------------------------------------

// load footer file
require_once('footer.php');

?>