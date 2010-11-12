<?php
/**
 * Copyright (c) 2010 Endeavor Systems, Inc.
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
 * Add number column and remove 'Enhancement Supplemental Guidance'
 * Update the enhancement nubmer to number column in security control enhancement table
 * 
 * @author     Ben Zheng <ben.zheng@reyosoft.com>
 * @copyright  (c) Endeavor Systems, Inc. 2010 {@link http://www.endeavorsystems.com}
 * @license    http://www.openfisma.org/content/license GPLv3
 * @package    Migration
 */
class Version81 extends Doctrine_Migration_Base
{
    /**
     * All the records whose enhancement numbers are withdrawn are listed so that 
     * they can be assigned correct enhancement number.
     * 
     * @var array
     */
    private static $_specialNumbers = array(
        'AC-03' => array(1),
        'AU-02' => array(1, 2),
        'AU-06' => array(2),
        'CP-09' => array(4),
        'CP-10' => array(1),
        'MP-05' => array(1),
        'SA-12' => array(1),
    );

    /**
     * A few of missing records of security control enhancement
     * 
     * @var array
     */
    private static $_missingNumbers = array(
        array('AC-07', 'NIST SP 800-53 Rev. 0'),
        array('AU-02', 'NIST SP 800-53 Rev. 0'),
        array('AC-07', 'NIST SP 800-53 Rev. 1'),
        array('SC-08', 'NIST SP 800-53 Rev. 1'),
        array('AC-07', 'NIST SP 800-53 Rev. 2'),
        array('MP-06', 'NIST SP 800-53 Rev. 2'),
        array('SA-12', 'NIST SP 800-53 Rev. 3'),
    );

    /**
     * Add number column and remove 'Enhancement Supplemental Guidance'
     */
    public function up()
    {
        $this->addColumn(
            'security_control_enhancement',
            'number',
            'integer',
            '2',
            array(
                'default' => NULL,
                'comment' => 'Enhancement number'
            )
        );

        $conn = Doctrine_Manager::connection();

        // Remove the "Enhancement Supplemental Guidance" from the description field
        $updateSql = "UPDATE `security_control_enhancement` SET `description` = LEFT(`description`,"
                   . "LOCATE('<p><u>Enhancement Supplemental Guidance',`description`)-1) where `description` like "
                   . "'%Enhancement Supplemental Guidance%'";
        $conn->exec($updateSql);
    }

    /**
     * Use postUp to update enhancement number and add the missing record
     */
    public function postUp()
    {
        // Get securityControlId
        $securityControlIds = Doctrine_Query::CREATE()
                              ->select('securityControlId')
                              ->from('SecurityControlEnhancement')
                              ->groupBy('securityControlId')
                              ->setHydrationMode(Doctrine::HYDRATE_ARRAY)
                              ->execute();

        // Loop through the SeurityControlEnhancement records gotten by securityControlId to update number column.
        $this->_updateEnhancementNumber($securityControlIds);

        // Add the missing records of security control enhancement
        $this->_addMissingEnhancement();
    }

    /**
     * Update the enhancement nubmer to number column
     * 
     * @param array $ids
     */
    private function _updateEnhancementNumber($ids)
    {
        if (count($ids) > 0) {
            foreach ($ids as $id) {
                $number = 1;
                $enhancements = Doctrine::getTable('SecurityControlEnhancement')
                                ->findBySecurityControlId($id['securityControlId']);
                foreach ($enhancements as $enhancement) {
                    $this->_setNumber($enhancement->Control->code, $enhancement->Control->Catalog->name, &$number);
                    $enhancement->number = $number;
                    $enhancement->save();
                    $number++;
                }
            }
        }
    }

    /**
     * To assign correct enhancement number
     * 
     * @param string $code The code of security control
     * @param string $catalogName The name of security control catalog
     * @param integer $number The number of security control enhancement
     * 
     * @return integer
     */
    private function _setNumber($code, $catalogName, &$number)
    {
        foreach (self::$_specialNumbers as $key => $value) {
            if ($code == $key && $catalogName == 'NIST SP 800-53 Rev. 3' && in_array($number, $value)) {
                //skip the numbers that have been withdrawn
                $number = max($value) + 1;
            }
        }
    }

    /**
     * Add the missing records of securitycontrol enhancement
     */
    private function _addMissingEnhancement()
    {
        // The mising enhancement in SP800-53-rev0 file
        // Rev0_AC_07_E1
        $enhancement = new SecurityControlEnhancement();
        $enhancement->number = 1;
        $enhancement->level = 'NONE';
        $enhancement->description = 'The information system automatically locks the account/node until released by an '
                                  . 'administrator when the maximum number of unsuccessful attempts is exceeded.';
        $enhancement->Control = $this->_getSecurityControl(self::$_missingNumbers[0]);
        $enhancement->save();

        // Rev0_AU_02_E1
        $enhancement = new SecurityControlEnhancement();
        $enhancement->number = 1;
        $enhancement->level = 'NONE';
        $enhancement->description = 'The information system provides the capability to compile audit records from '
                                  . 'multiple components throughout the system into a systemwide (logical or physical),'
                                  . ' time-correlated audit trail.';
        $enhancement->Control = $this->_getSecurityControl(self::$_missingNumbers[1]);
        $enhancement->save();

        // Rev0_AU_02_E2
        $enhancement = new SecurityControlEnhancement();
        $enhancement->number = 2;
        $enhancement->level = 'NONE';
        $enhancement->description = 'The information system provides the capability to manage the selection of events '
                                  . 'to be audited by individual components of the system.';
        $enhancement->Control = $this->_getSecurityControl(self::$_missingNumbers[1]);
        $enhancement->save();

        // The mising enhancement in SP800-53-rev1 file
        // Rev1_AC_07_E1
        $enhancement = new SecurityControlEnhancement();
        $enhancement->number = 1;
        $enhancement->level = 'NONE';
        $enhancement->description = 'The information system automatically locks the account/node until released by an '
                                  . 'administrator when the maximum number of unsuccessful attempts is exceeded.';
        $enhancement->Control = $this->_getSecurityControl(self::$_missingNumbers[2]);
        $enhancement->save();

        // Rev1_SC_08_E1
        $enhancement = new SecurityControlEnhancement();
        $enhancement->number = 1;
        $enhancement->level = 'HIGH';
        $enhancement->description = 'The organization employs cryptographic mechanisms to recognize changes to '
                                  . 'information during transmission unless otherwise protected by alternative '
                                  . 'physical measures.';
        $enhancement->Control = $this->_getSecurityControl(self::$_missingNumbers[3]);
        $enhancement->save();

        // The mising enhancement in SP800-53-rev2 file
        // Rev2_AC_07_E1
        $enhancement = new SecurityControlEnhancement();
        $enhancement->number = 1;
        $enhancement->level = 'NONE';
        $enhancement->description = 'The information system automatically locks the account/node until released by an '
                                  . 'administrator when the maximum number of unsuccessful attempts is exceeded.';
        $enhancement->Control =  $this->_getSecurityControl(self::$_missingNumbers[4]);
        $enhancement->save();

        // Rev2_MP_06_E1
        $enhancement = new SecurityControlEnhancement();
        $enhancement->number = 1;
        $enhancement->level = 'HIGH';
        $enhancement->description = 'The organization tracks, documents, and verifies media sanitization and disposal '
                                  . 'actions.';
        $enhancement->Control = $this->_getSecurityControl(self::$_missingNumbers[5]);
        $enhancement->save();

        // Rev2_MP_06_E2
        $enhancement = new SecurityControlEnhancement();
        $enhancement->number = 1;
        $enhancement->level = 'HIGH';
        $enhancement->description = 'The organization periodically tests sanitization equipment and procedures to '
                                  . 'verify correct performance.';
        $enhancement->Control = $this->_getSecurityControl(self::$_missingNumbers[5]);
        $enhancement->save();

        // The mising enhancement in SP800-53-rev3 file
        // Rev3_SA_12_E1
        $enhancement = new SecurityControlEnhancement();
        $enhancement->number = 1;
        $enhancement->level = 'NONE';
        $enhancement->description = 'The organization purchases all anticipated information system components and '
                                  . 'spares in the initial acquisition.';
        $enhancement->Control = $this->_getSecurityControl(self::$_missingNumbers[6]);
        $enhancement->save();

    }

    /**
     * Get foreign key to a security control object in security control table
     * 
     * @param array $misingNumber The missing enhancement number of security control enhancement
     * @return Doctrine_Collection
     */
    private function _getSecurityControl($missingNumber)
    {
        $securityControl = Doctrine_Query::create()
                           ->from('SecurityControl s')
                           ->leftJoin('s.Catalog c')
                           ->where('s.code = ? AND c.name = ?', $missingNumber)
                           ->fetchOne();

       return $securityControl;
    }

    /**
     * No reverse migration
     */
    public function down()
    {
        throw new Doctrine_Migration_IrreversibleMigrationException();
    }
}
