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
 * @copyright (c) Endeavor Systems, Inc. 2008 (http://www.endeavorsystems.com)
 * @license   http://www.openfisma.org/mw/index.php?title=License
 * @package   Fisma_Format
 */
class Fisma_Format_Section {
    /** @yui document this class */
    static function startSection($title, $editableTarget = null) {
        if (isset($editableTarget)) {
            print "<div class='sectionHeader'><span class='editable' target='$editableTarget'>$title</span></div>\n"
                . "<div class='section'>";
        } else {
            print "<div class='sectionHeader'>$title</div>\n<div class='section'>";
        } 
    }
    
    static function stopSection() {
        print "<div class='clear'></div></div>\n";
    }
}
