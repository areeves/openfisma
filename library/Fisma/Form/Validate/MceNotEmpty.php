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

/**
 * Validate if the MCE editor has content
 *
 * @author     Jim Chen <xhorse@users.sourceforge.net>
 * @copyright  (c) Endeavor Systems, Inc. 2009 (http://www.endeavorsystems.com)
 * @license    http://www.openfisma.org/content/license
 * @package    Fisma
 * @subpackage Fisma_Form
 * @version    $Id$
 */
class Fisma_Form_Validate_MceNotEmpty extends Fisma_Form_Validate_NotBlank
{
    const NOTEMPTY = "notempty";

    protected $_messageTemplates = array(
        self::NOTEMPTY => "cannot be empty."
    );

    /** 
     * Returns true if the mce editor has none empty value after removing the wrapper tags
     *
     * @param string $value
     * @return true|false
     */
    public function isValid($value)
    {
        // tags don't count as content
        $value = strip_tags($value);
        $value = html_entity_decode($value);

        // remove all non breaking spaces
        $value = trim($value, "\xA0");

        $validator = new Zend_Validate_NotEmpty();

        if ($validator->isValid($value)) {
            return parent::isValid($value);
        }

        $this->_error();
        return false;
    }
}
