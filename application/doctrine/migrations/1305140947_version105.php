<?php
// @codingStandardsIgnoreFile
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
 * Add column onto evaluation table
 *
 * @package Migration
 * @copyright (c) Endeavor Systems, Inc. 2011 {@link http://www.endeavorsystems.com}
 * @author Dale Frey <dale.frey@endeavorsystems.com>
 * @license http://www.openfisma.org/content/license GPLv3
 */

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Version105 extends Doctrine_Migration_Base
{

    /**
     * Add daysuntildue column on evaluation
     */
    public function up()
    {
        $this->addColumn('evaluation', 'daysuntildue', 'integer', '8', array(
             'comment' => 'Number of days that a finding can remain in this workflow status before being considered overdue',
             ));
    }

    /**
     * Remove column
     */
    public function down()
    {
        $this->removeColumn('evaluation', 'daysuntildue');
    }
}