<?php
/**
 * Copyright (c) 2008 Endeavor Systems, Inc.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @author    Woody lee <woody.li@reyosoft.com>
 * @copyright (c) Endeavor Systems, Inc. 2008 (http://www.endeavorsystems.com)
 * @license   http://www.openfisma.org/mw/index.php?title=License
 * @version   $Id$
 * @package   Test_System
 */

/**
 * Test_FismaUnitTest
 */
require_once(realpath(dirname(__FILE__) . '/../FismaUnitTest.php'));

/**
 * Unit tests for the System model
 *
 * @package Test_model
 */
class Test_Model_System extends Test_FismaUnitTest
{
    /**
     * Test the method of getting security category level
     * 
     * The security category will be the highest level 
     * among the confidentiality, integrity and availability
     * 
     */
    public function testGetSecurityCategory()
    {
        $system = new System();
        
        $system->confidentiality = System::MODERATE_LEVEL;
        $system->integrity = System::MODERATE_LEVEL;
        $system->availability = System::LOW_LEVEL;
        $this->assertEquals($system->getSecurityCategory(), System::MODERATE_LEVEL);
        
        $system->confidentiality = System::HIGH_LEVEL;
        $system->integrity = System::MODERATE_LEVEL;
        $system->availability = System::LOW_LEVEL;
        $this->assertEquals($system->getSecurityCategory(), System::HIGH_LEVEL);
        
        $system->confidentiality = System::LOW_LEVEL;
        $system->integrity = System::LOW_LEVEL;
        $system->availability = System::LOW_LEVEL;
        $this->assertEquals($system->getSecurityCategory(), System::LOW_LEVEL);
        
        $system->confidentiality = System::NA;
        $system->integrity = System::LOW_LEVEL;
        $system->availability = System::LOW_LEVEL;
        $this->assertEquals($system->getSecurityCategory(), null);
        
    }
}
