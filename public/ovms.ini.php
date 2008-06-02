<?php
/*
** This is used as a single point of configuration data for scripts
** - both PHP (dblink, finding_upload) and Perl (inject_utils).
*/

    define('DEPLOY_MODE', 'DBG'); //DBG, RLS
    // Database
    // Choose the database to be used
    define('OVMS_DB_TYPE', 'mysql');

    // Database Hostname
    // Hostname of the database server. If you are unsure, 'localhost' works in most cases.
    define('OVMS_DB_HOST', 'localhost');

   //this port is used while connect
    define('OVMS_DB_PORT', '3306');

    // Database Username
    // Your database user account on the host
    define('OVMS_DB_USER', 'sws_live');

    // Database Password
    // Password for your database user account
    define('OVMS_DB_PASS', '123456');

    // Database Name
    // The name of database on the host. The installer will attempt to create the database if not exist
    define('OVMS_DB_NAME', 'targetfisma');

    //this pass_c was used to connect database by new user
    define('OVMS_DB_PASS_C', '123456');

    //this name_c was used to connect database by new user
    define('OVMS_DB_NAME_C', 'sws_live');

    if(!defined('DS') ){
        define("DS", DIRECTORY_SEPARATOR);
    }

    if(!defined('OVMS_ROOT_PATH')){
        define('OVMS_ROOT_PATH', '/opt/reyo/openfisma/zfchris');
        define("OVMS_WEB_PATH", OVMS_ROOT_PATH. DS ."public");
        define("OVMS_WEB_TEMP", OVMS_WEB_PATH. DS ."temp");
        define("OVMS_VENDOR_PATH", OVMS_ROOT_PATH. DS ."vendor");
        define("PDF_FONT_FOLDER", OVMS_VENDOR_PATH. DS ."pdf". DS ."fonts");
        define("OVMS_INJECT_PATH", OVMS_ROOT_PATH. DS ."inject");
        define("OVMS_INCLUDE_PATH", OVMS_ROOT_PATH. DS ."include");
        define('OVMS_LOCAL_PEAR', OVMS_VENDOR_PATH .  DS  . 'Pear');
        define("OVMS_TEMP", ini_get('upload_tmp_dir'));
    }


$OVMS_ROOT = OVMS_ROOT_PATH;
$DB_HOST = OVMS_DB_HOST;
$DB_USER= OVMS_DB_USER;
$DB_PASS= OVMS_DB_PASS;
$DB= OVMS_DB_NAME;

$CUSTOMER_LOGO = "images/customer_logo.jpg";
$LOGIN_WARNING = "This is a United States Government Computer system. We encourage its use by authorized staff, auditors, and contractors. Activity on this system is subject to monitoring in the course of systems administration and to protect the system from unauthorized use. Users are further advised that they have no expectation of privacy while using this system or in any material on this system. Unauthorized use of this system is a violation of Federal Law and will be punished with fines or imprisonment (P.L. 99-474) Anyone using this system expressly consents to such monitoring and acknowledges that unauthorized use may be reported to the proper authorities.";

define("PS", PATH_SEPARATOR);
ini_set('include_path',ini_get('include_path'). PS .OVMS_INCLUDE_PATH . PS . OVMS_LOCAL_PEAR . PS . OVMS_VENDOR_PATH);

require_once(OVMS_ROOT_PATH . DS . 'conf' . DS . 'config.'.strtolower(DEPLOY_MODE).'.php');

?>
