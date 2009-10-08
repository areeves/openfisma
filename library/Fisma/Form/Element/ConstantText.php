<?php
/**
 * Copyright (c) 2008 Endeavor Systems, Inc.
 *
 * This file is part of OpenFISMA.
 *
 * OpenFISMA is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OpenFISMA is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OpenFISMA.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author    Mark E. Haase <mhaase@endeavorsystems.com>
 * @copyright (c) Endeavor Systems, Inc. 2008 (http://www.endeavorsystems.com)
 * @license   http://www.openfisma.org/mw/index.php?title=License
 * @version   $Id$
 * @package   Fisma_Form
 */

/**
 * Renders its value as a simple text box, with no actual form control provided.
 *
 * @copyright (c) Endeavor Systems, Inc. 2008 (http://www.endeavorsystems.com)
 * @license   http://www.openfisma.org/mw/index.php?title=License
 * @package   Fisma_Form
 */
class Fisma_Form_Element_ConstantText extends Zend_Form_Element
{   
    /**
     * Render the form element
     *
     * @return string The rendered element
     */
    function render() {
        $label = $this->getLabel();
        
        $render = '<tr><td>'
                . (empty($label) ? '&nbsp;' : "$label:")
                . '</td><td>'
                . $this->getValue() 
                . '</td></tr>';
        
        return $render;
    }
    
    /**
     * Override isValid to prevent the validator from overwriting the value of this constant field
     * 
     * @param mixed $ignored This parameter is not used
     * @return boolean Always returns true
     */
    public function isValid($ignored) {
        return true;
    }
}
