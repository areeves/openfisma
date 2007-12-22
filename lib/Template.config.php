<?php

// include the configuration
require_once('Config.class.php');

// include the smarty class
require_once($_CONFIG->SMARTY_LIB_DIR().'Smarty.class.php');

// create a template instnace
$_TEMPLATE               = new Smarty;
$_TEMPLATE->template_dir = $_CONFIG->SMARTY_TEMPLATE_DIR();
$_TEMPLATE->compile_dir  = $_CONFIG->SMARTY_COMPILE_DIR();
$_TEMPLATE->cache_dir    = $_CONFIG->SMARTY_CACHE_DIR();
$_TEMPLATE->configs_dir  = $_CONFIG->SMARTY_CONFIGS_DIR();
$_TEMPLATE->debugging    = $_CONFIG->SMARTY_DEBUGGING();

?>