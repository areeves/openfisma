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
 * System
 * 
 * @author     Ryan Yang <ryan@users.sourceforge.net>
 * @copyright  (c) Endeavor Systems, Inc. 2009 (http://www.endeavorsystems.com)
 * @license    http://www.openfisma.org/content/license
 * @package    Model
 * @version    $Id$
 */
class System extends BaseSystem
{
    /**
     * Defines the way counter measure effectiveness and threat level combine to produce the threat likelihood. This
     * array is indexed as: $_threatLikelihoodMatrix[THREAT_LEVEL][COUNTERMEASURE_EFFECTIVENESS] == THREAT_LIKELIHOOD
     *
     * @see _initThreatLikelihoodMatrix()
     */
    private $_threatLikelihoodMatrix;

    /**
     * Declares fields stored in related records that should be indexed along with records in this table
     * 
     * @see Asset.php
     * @todo Doctrine 2.0 might provide a nicer approach for this
     */
    public $relationIndex = array(
        'Organization' => array(
            'name' => array('type' => 'unstored', 'alias' => 'name'),
            'nickname' => array('type' => 'unstored', 'alias' => 'nickname'),
            'description' => array('type' => 'unstored', 'alias' => 'description')
        )
    );

    /**
     * Map the values to Organization table
     */
    public function construct()
    {
        $this->mapValue('organizationId');
        $this->mapValue('name');
        $this->mapValue('nickname');
        $this->mapValue('description');
    }

    /**
     * set the mapping value 'organizationid'
     *
     * @param int $id
     */
    public function setOrganizationId($id)
    {
        $this->set('organizationId', $id);
    }

    /**
     * set the mapping value 'name'
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->set('name', $name);
        // if the object hasn't identity,
        // then we think it is under the insert status.
        // otherwise it is update status
        if (empty($this->Organization->id)) {
            $this->state(Doctrine_Record::STATE_TDIRTY);
        } else {
            $this->state(Doctrine_Record::STATE_DIRTY);
        }
    }
    
    /**
     * set the mapping value 'nickname'
     *
     * @param string $nickname
     */
    public function setNickname($nickname)
    {
        $this->set('nickname', $nickname);
        // if the object hasn't identity,
        // then we think it is under the insert status.
        // otherwise it is update status
        if (empty($this->Organization->id)) {
            $this->state(Doctrine_Record::STATE_TDIRTY);
        } else {
            $this->state(Doctrine_Record::STATE_DIRTY);
        }
    }
    
    /**
     * set the map value 'description'
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->set('description', $description);
        // if the object hasn't identity,
        // then we think it is under the insert status.
        // otherwise it is update status
        if (empty($this->Organization->id)) {
            $this->state(Doctrine_Record::STATE_TDIRTY);
        } else {
            $this->state(Doctrine_Record::STATE_DIRTY);
        }
    }

    /**
     * Confidentiality, Integrity, Availability
     */
    const CIA_HIGH = 'high';
    
    /**
     * Confidentiality, Integrity, Availability
     */
    const CIA_MODERATE = 'moderate';
    
    /**
     * Confidentiality, Integrity, Availability
     */
    const CIA_LOW = 'low';
    
    /**
     * Only confidentiality can have 'NA'
     */
    const CIA_NA = 'na';

    /**
     * A mapping from the physical system types to proper English terms
     */
    private $_typeMap = array(
        'gss' => 'General Support System',
        'major' => 'Major Application',
        'minor' => 'Minor Application'
    );
    
    /**
     * Return the English version of the orgType field
     */
    public function getTypeLabel() 
    {
        return $this->_typeMap[$this->type];
    }
    
    /**
     * Calculate FIPS-199 Security categorization.
     *
     * The calculation over enumeration fields {LOW, MODERATE, HIGH} is tricky here. The algorithm 
     * is up to their mapping value, which is decided by the appear consequence in TABLE definition.
     * For example, in case `confidentiality` ENUM('NA','LOW','MODERATE','HIGH') it turns out the 
     * mapping value: LOW=0, MODERATE=1, HIGH=2. The value calculated is the maximum of C, I, A. And 
     * is transferred back to enumeration name again.
     * 
     * @return string
     */
    public function fipsSecurityCategory()
    {
        $confidentiality = $this->confidentiality;
        $integrity = $this->integrity;
        $availability = $this->availability;
        
        $array = $this->getTable()->getEnumValues('confidentiality');
        $confidentiality = array_search($confidentiality, $array) - 1;
        
        $array = $this->getTable()->getEnumValues('integrity');
        $integrity = array_search($integrity, $array);
        
        $array = $this->getTable()->getEnumValues('availability');
        $availability = array_search($availability, $array);

        $index = max((int)$confidentiality, (int)$integrity, (int)$availability);
        return $array[$index];
    }

    /**
     * Calculate min level
     *
     * @see calcSecurityCategory
     *
     * @param string $levelA
     * @param string $levelB
     * @param return string min of $levelA and $levelB
     */
    public function calcMin($levelA, $levelB)
    {
        $cloumns = $this->getTable()->getEnumValues('availability');
        assert(in_array($levelA, $cloumns));
        assert(in_array($levelB, $cloumns));
        $senseMap = array_flip($cloumns);
        $ret = min($senseMap[$levelA], $senseMap[$levelB]);
        return $cloumns[$ret];
    }
    
    /**
     * Calcuate overall threat level
     *
     * @see calcSecurityCategory
     *
     * @param string $threat threat level
     * @param string $countermeasure countermeasure level
     * @return string overall threat
     */
    public function calculateThreatLikelihood($threat, $countermeasure)
    {
        // Initialize the threat likelihood matrix if necessary
        if (!$this->_threatLikelihoodMatrix) {
            $this->_initThreatLikelihoodMatrix();
        }
        
        // Map the parameters into the matrix and return the mapped value
        return $this->_threatLikelihoodMatrix[$threat][$countermeasure];
    }
    
    /**
     * Initializes the threat likelihood matrix. This is hardcoded because these values are defined in NIST SP 800-30
     * and are not likely to change very often.
     *
     * @link http://csrc.nist.gov/publications/nistpubs/800-30/sp800-30.pdf
     */
    private function _initThreatLikelihoodMatrix()
    {
        $this->_threatLikelihoodMatrix['HIGH']['LOW']      = 'HIGH';
        $this->_threatLikelihoodMatrix['HIGH']['MODERATE'] = 'MODERATE';
        $this->_threatLikelihoodMatrix['HIGH']['HIGH']     = 'LOW';
        
        $this->_threatLikelihoodMatrix['MODERATE']['LOW']      = 'MODERATE';
        $this->_threatLikelihoodMatrix['MODERATE']['MODERATE'] = 'MODERATE';
        $this->_threatLikelihoodMatrix['MODERATE']['HIGH']     = 'LOW';

        $this->_threatLikelihoodMatrix['LOW']['LOW']      = 'LOW';
        $this->_threatLikelihoodMatrix['LOW']['MODERATE'] = 'LOW';
        $this->_threatLikelihoodMatrix['LOW']['HIGH']     = 'LOW';
    }

    /**
     * Delegate the delete to organization delete
     */
    public function delete(Doctrine_Connection $conn = null)
    {
        $org = $this->Organization;
        return $org->delete($conn);
    }
    
    /**
     * Return system name with proper formatting
     */
    public function getName() 
    {
        return $this->Organization->nickname . ' - ' . $this->Organization->name;
    }
    
    /**
     * A post-update hook to send notifications
     * 
     * @param Doctrine_Event $event
     */
    public function postUpdate($event)
    {
        Notification::notify('SYSTEM_UPDATED', $this->Organization, User::currentUser(), $this->Organization->id);
    }
}
