<?php
/**
 * Copyright (c) 2008 Endeavor Systems, Inc.
 *
 * This file is part of OpenFISMA.
 *
 * OpenFISMA is free software: you can redistribute it and/or modify it under the terms of the GNU General Public 
 * License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later
 * version.
 *
 * OpenFISMA is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied 
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more 
 * details.
 *
 * You should have received a copy of the GNU General Public License along with OpenFISMA.  If not, see 
 * <http://www.gnu.org/licenses/>.
 */

require_once 'PHPUnit/Extensions/SeleniumTestCase.php';
// Bootstrap the application's CLI mode if it has not already been done
require_once(realpath(dirname(__FILE__) . '/../library/Fisma.php'));

if (Fisma::RUN_MODE_COMMAND_LINE != Fisma::mode()) {
    try {
        Fisma::initialize(Fisma::RUN_MODE_COMMAND_LINE);
        Fisma::connectDb();
        Fisma::setNotificationEnabled(false);
    } catch (Zend_Config_Exception $zce) {
        print "The application is not installed correctly." .
            " If you have not run the installer, you should do that now.";
    } catch (Exception $e) {
        print get_class($e) 
            . "\n" 
            . $e->getMessage() 
            . "\n"
            . $e->getTraceAsString()
            . "\n";
    }
}

/**
 * The selenium config file contains the server address (or name),
 * user name, and password for connecting to the
 * selenium server.
 */
define('SELENIUM_CONFIG_FILE', Fisma::getPath('config') . '/selenium.conf');

/**
 * This is the base class for all selenium tests in OpenFISMA. This base
 * class sets up the server information for accessing Selenium RC.
 * 
 * @author     Mark E. Haase <mhaase@endeavorsystems.com>
 * @copyright  (c) Endeavor Systems, Inc. 2009 (http://www.endeavorsystems.com)
 * @license    http://www.openfisma.org/content/license
 * @package    Test
 * @version    $Id$
 */
abstract class Test_FismaSeleniumTest extends
    PHPUnit_Extensions_SeleniumTestCase
{
    /**
     * Handle for the database connection.
     */
    protected $_db;
    
    /**
     * Directory on the selenium RC server in which to store screenshots
     */
    protected $_remoteScreenshotDir;
    
    /**
     * Used to uniquely name screenshots
     */
    protected $_remoteScreenshotSequence = 0;
    
    const USER_NAME = 'root';
    const PASSWORD = '0p3nfism@';

    /**
     * setUp() - Set up access to the Selenium server based on the contents of
     * the selenium.conf configuration file.
     */
    protected function setUp()
    {
        // Initialize our DB connection
    //    $this->_db = Zend_Db::factory(Zend_Registry::get('datasource'));
     //   Zend_Db_Table::setDefaultAdapter($this->_db);
        
        // Load the selenium configuration and connect to the server
        $seleniumConfig = new Zend_Config_Ini(SELENIUM_CONFIG_FILE, 'selenium');

        $this->_remoteScreenshotDir = $seleniumConfig->screenshotDir;

      //  $this->setHost($seleniumConfig->host);
     //   $this->setPort(intval($seleniumConfig->port));
        $this->setBrowser($seleniumConfig->browser);
        $this->setBrowserUrl($seleniumConfig->browserBaseUrl);
        $this->start();
    }

    /**
     * When a test fails, take a screenshot of the failure during tear down.
     * This greatly helps to diagnose errors in Selenium test cases.
     */
    protected function tearDown()
    {
        if ($this->hasFailed()) {
            $screenshotName = 'ERROR.'.get_class($this);
            $this->screenshot($screenshotName);
            $this->stop();
        }
    }

    /**
     * truncateTables() - Truncate one or more tables.
     *
     * @param string|array $tables The name[s] of the table[s] to truncate
     */
    protected function truncateTables($tables)
    {
        if (is_array($tables)) {
            foreach ($tables as $table) {
                $this->truncateTable($table);
            }
        } else {
            $this->truncateTable($tables);
        }
    }
    
    /**
     * truncateTable() - Truncate a single table.
     *
     * This is a helper function to truncateTables() and is private.
     *
     * @param string $table The name of the table to truncate
     */
    private function truncateTable($table)
    {
        $truncate = $this->_db->prepare("TRUNCATE TABLE $table");
        $truncate->execute();
    }

    /**
     * createDefaultUser() - Create a user with the default name and password
     * (self::USER_NAME and self::PASSWORD) and give that the user the specified
     * role.
     *
     * Notice: this function will truncate the users and user_roles tables, so
     * be sure to call it <i>before</i> creating test data in those tables.
     *
     * @param string $role Nickname of role to assign to this user.
     */
    protected function createDefaultUser($role)
    {
        $this->truncateTables(array('users', 'user_roles'));

        // Create the user
        $userTable = new User($this->_db);
        $userId = $userTable->insert(
            array(
                'account' => self::USER_NAME,
                'password' => $userTable->digest(self::PASSWORD),
                'is_active' => 1,
                'password_ts' => new Zend_Db_Expr('now()'),
                'last_rob' => new Zend_Db_Expr('now()')
            )
        );

        // Give the new user the specified role
        $grantRole = $this->_db->prepare(
            "INSERT INTO user_roles
                  SELECT $userId, r.id
                    FROM roles r
                   WHERE r.nickname like '$role'"
        );
        $grantRole->execute();
    }

    /**
     * login() - Login to OpenFISMA
     */
    protected function login($username = '', $password = '')
    {
        $this->open('/');
        $username = empty($username) ? self::USER_NAME : $username;
        $password = empty($password) ? self::PASSWORD : $password;
        $this->type('username', $username);
        $this->type('userpass', $password);
        $this->click("loginButton-button");
        $this->waitForPageToLoad();
        $this->open('/panel/dashboard');
        $this->waitForPageToLoad();
        sleep(5);
        $this->assertTextPresent(' is currently logged in');
        sleep(5);
    }
    
    /**
     * Take a Selenium RC screenshot.
     *
     * This assumes that the Selenium RC server is running on Windows
     *
     * @param string $name A name for the screenshot
     * (use lower_case_underscore naming format, without file extension)
     */
    public function screenshot($name) 
    {
        $sequenceNumber = sprintf('%03d', $this->_remoteScreenshotSequence++);
        $screenshotPath = $this->_remoteScreenshotDir .
            "\\{$sequenceNumber}_{$name}.png";
        $this->captureEntirePageScreenshot($screenshotPath);
    }
}
