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
 * @version   $Id: $
 * @package   Fisma_Format
 */

/**
 * A "section" is a rectangular area on an HTML page that has a title bar and a content area
 * 
 * @copyright (c) Endeavor Systems, Inc. 2008 (http://www.endeavorsystems.com)
 * @license   http://www.openfisma.org/mw/index.php?title=License
 * @package   Fisma_Format
 */
class Fisma_Format_Section 
{
    /**
     * Starts a web page section.
     * 
     * This emits a rectangular box on the page with a title bar and a content area.
     * 
     * @param string $title Text that appears in the title bar
     * @param string $editableTarget Set to the id of an editable field (optional)
     * @param string $anchorName An anchor name attribute (optional)
     */
    static function startSection($title, $editableTarget = null, $anchorName = null) 
    {
        $anchorStartTag = isset($anchorName) ? "<a name='$anchorName'>" : '';
        $anchorEndTag = isset($anchorName) ? "</a>" : '';

        $editableText = isset($editableTarget) ? "class='editable' target='$editableTarget'" : '';

        $render = "<div class='sectionHeader'>"
                . "<span $editableText>{$anchorStartTag}{$title}{$anchorEndTag}</span></div>"
                . "<div class='section'>";

        print $render;
    }
    
    /**
     * Ends a web page section
     * 
     * This method must always be called once for each startSection() call in order to close each section correctly
     */
    static function stopSection() 
    {
        print "<div class='clear'></div></div>\n";
    }
}
