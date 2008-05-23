<?php

define('TEST_ROOT', dirname(__FILE__));
define('ROOT', realpath(TEST_ROOT . '/../..') );

require_once('simpletest/unit_tester.php');
require_once('simpletest/reporter.php');

if(!defined('DS') ) {
    define('DS', DIRECTORY_SEPARATOR);
}

require_once(ROOT . DS . 'paths.php');
require_once( APPS . DS . 'basic.php');
import(LIBS, VENDORS, VENDORS.DS.'Pear', ROOT . DS . 'include');

require_once 'Zend/Registry.php';
require_once 'Zend/Db.php';
require_once 'Zend/Config.php';
require_once 'Zend/Db/Table.php';
require_once MODELS . DS . 'Abstract.php';
require_once ( CONFIGS . DS . 'database.php');
require_once 'Zend/Controller/Plugin/ErrorHandler.php';

$db = Zend_DB::factory(Zend_Registry::get('datasource')->default);
Zend_Registry::set('db',$db);
Zend_Db_Table::setDefaultAdapter($db);



$test = new TestSuite('All classes tests');
$test->addTestFile('acl_t.php');
$test->addTestFile('search_t.php');

$test->run(new TextReporter());

