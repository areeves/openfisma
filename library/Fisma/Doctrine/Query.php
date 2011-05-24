<?php
/**
 * Copyright (c) 2011 Endeavor Systems, Inc.
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

/**
 * Fisma_Doctrine_Query 
 * 
 * @uses Doctrine_Query
 * @package Fisma_Doctrine 
 * @copyright (c) Endeavor Systems, Inc. 2011 {@link http://www.endeavorsystems.com}
 * @author Josh Boyd <joshua.boyd@endeavorsystems.com> 
 * @license http://www.openfisma.org/content/license GPLv3
 */
class Fisma_Doctrine_Query extends Doctrine_Query
{
    /**
     * Perform input validation on the order by clause before continuing 
     * 
     * @param mixed $orderBy 
     * @access public
     * @return Doctrine_Query 
     */
    public function orderBy($orderBy)
    {
        /**
         * Matches the following examples as valid clauses:
         *
         *   alias.field desc
         *   alias.field asc
         *   alias.field
         *   field
         *
         * The pattern is case insensitive.
         * @TODO Modify the pattern so that multiple clauses are valid, for example:
         * alias.field desc, alias2.field2 asc
         * We don't use this format anywhere in OpenFISMA presently, but we probably will at some point.
         */
        $pattern = '(^\w*?\.?\w*(\ )*(desc|asc/i)*$)';

        if (!preg_match($pattern, $orderBy)) {
            throw new Doctrine_Exception('syntax error');
        }

        return parent::orderBy($orderBy);
    }
}
