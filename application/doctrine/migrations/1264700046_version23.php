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

/**
 * Drop the current IP address and failure count columns from the user model
 * 
 * @package Migration
 * @version $Id$
 * @copyright (c) Endeavor Systems, Inc. 2010 {@link http://www.endeavorsystems.com}
 * @author Mark E. Haase <mhaase@endeavorsystems.com>
 * @license http://www.openfisma.org/content/license GPLv3
 */
class Version23 extends Doctrine_Migration_Base
{
    /**
     * Drop columns
     */
    public function up()
    {
        $this->removeColumn('user', 'currentloginip');
        $this->removeColumn('user', 'oldfailurecount');

        Doctrine::generateModelsFromYaml(Fisma::getPath('schema'), Fisma::getPath('model'));
    }

    /**
     * Re-add columns
     */
    public function down()
    {
        $this->addColumn('user', 'currentloginip', 'string', array('length' => 15));
        $this->addColumn('user', 'oldfailurecount', 'integer');
    }
}
