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
 * {@link http://www.gnu.org/licenses/}.
 */

require_once(realpath(dirname(__FILE__) . '/../../FismaUnitTest.php'));

/**
 * Unit tests for the finding model
 * 
 * @author     Mark E. Haase
 * @copyright  (c) Endeavor Systems, Inc. 2009 {@link http://www.endeavorsystems.com}
 * @license    http://www.openfisma.org/content/license GPLv3
 * @package    Test
 * @subpackage Test_Model
 * @version    $Id$
 */
class Test_Application_Models_Finding extends Test_FismaUnitTest
{
    /**
     * Cannot set next due date directly
     * 
     * @return void
     * @expectedException Fisma_Exception
     */
    public function testCannotSetDueDateDirectly()
    {
        $finding = new Finding();
        
        $finding->nextDueDate = Zend_Date::now();
    }
    
    /**
     * New findings should be NEW status and setting the type should move the finding to DRAFT
     * 
     * @return void
     * @throws PHPUnit_Framework_ExpectationFailedException if assertion fails
     */
    public function testFindingStatus()
    {
        $finding = new Finding();
        
        $this->assertEquals('NEW', $finding->status);
        
        $finding->type = 'CAP';
        
        $this->assertEquals('DRAFT', $finding->status);
    }
    
    /**
     * Test due dates being set automatically based on status
     * 
     * @return void
     * @throws PHPUnit_Framework_ExpectationFailedException if assertion fails
     */
    public function testDueDates()
    {
        $finding = new Finding();
        
        // The due date for a NEW finding is not null
        $this->assertNotNull($finding->nextDueDate);
        
        // If the finding is set to PEND, the date should be null
        $finding->status = 'PEND';
        $this->assertNull($finding->nextDueDate);
        
        // If the finding is set to CLOSED, the date should be null
        $finding->status = 'CLOSED';
        $this->assertNull($finding->nextDueDate);
    }
    
    /**
     * Original ECD cannot be set directly
     * 
     * @return void
     * @expectedException Fisma_Exception
     */
    public function testOriginalEcdCannotBeSet()
    {
        $finding = new Finding();
        
        $finding->originalEcd = Zend_Date::now();
    }
    
    /**
     * Test if the ECD lock affects synchronization of the original and current ECD correctly
     * 
     * @return void
     * @throws PHPUnit_Framework_ExpectationFailedException if assertion fails
     */
    public function testEcdLockSyncsEcds()
    {
        $finding = new Finding();
                
        // Setting the current ECD should synchronize the original ECD
        $date1 = Zend_Date::now();
        $finding->currentEcd = $date1;

        $this->assertEquals($date1, $finding->currentEcd);
        $this->assertEquals($date1, $finding->originalEcd);
        
        // When locked, the original ECD does not synchronize with the current ECD
        $date2 = clone $date1;
        $date2->addDay(1);

        $finding->ecdLocked = true;
        $finding->currentEcd = $date2;
         
        $this->assertEquals($date2, $finding->currentEcd);
        $this->assertNotEquals($date2, $finding->originalEcd);
    }
    
    /**
     * When the status goes to EN, the ecd should be locked
     * 
     * @return void
     * @throws PHPUnit_Framework_ExpectationFailedException if assertion fails
     */
    public function testEcdLockEnStatus()
    {
        $finding = new Finding();

        // Unlocked by default
        $this->assertFalse($finding->ecdLocked);

        // Should be locked when the status is EN
        $finding->currentEcd = Fisma::now();
        $finding->status = 'EN';
        $this->assertTrue($finding->ecdLocked);
        
        // Even setting the status back to DRAFT should not unlock it
        $finding->status = 'DRAFT';
        $this->assertTrue($finding->ecdLocked);
    }
}
