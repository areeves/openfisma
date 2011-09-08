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

require_once(realpath(dirname(__FILE__) . '/../../../Case/Unit.php'));

/**
 * Tests for Fisma_String_LoremIpsum
 * 
 * @author     Joshua D. Boyd <joshua.boyd@endeavorsystems.com> 
 * @copyright  (c) Endeavor Systems, Inc. 2009 {@link http://www.endeavorsystems.com}
 * @license    http://www.openfisma.org/content/license GPLv3
 * @package    Test
 * @subpackage Test_Fisma
 */
class Test_Library_Fisma_String_LoremIpsum extends Test_Case_Unit
{
    /**
     * testLoremIpsumDefault 
     */
    public function testLoremIpsumDefault()
    {
        $lorem = new Fisma_String_LoremIpsum();
        $lorem = $lorem->getContent(500);
        $this->assertTrue((boolean) $lorem);
    }

    /**
     * testLoremIpsumPlain
     */
    public function testLoremIpsumPlain()
    {
        $lorem = new Fisma_String_LoremIpsum();
        $lorem = $lorem->getContent(500, 'plain');
        $this->assertTrue((boolean) $lorem);
    }
}
