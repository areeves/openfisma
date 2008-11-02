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
 * @author    ???
 * @copyright (c) Endeavor Systems, Inc. 2008 (http://www.endeavorsystems.com)
 * @license   http://www.openfisma.org/mw/index.php?title=License
 * @version   $Id: RiskAssessment.class.php 863 2008-09-09 21:17:03Z mehaase $
 *
 * @todo Clean up this file and assign an author. The file is named incorrectly,
 * and isn't really a controller of any sort. The logic here probably needs to
 * be merged into the POA&M model class.
 */
 
/**
 * Risk assessment calculation class.
 *
 * This module takes vulnerability aspects - system confidentiality,
 * availability and integrity, mission criticality, threat and
 * countermeasure effectiveness - and combines these to determine
 * sensitivity, threat likelihood and overall risk posed by the
 * vulnerability to the particular system.
 *
 * The class is instantiated with all aspects, some of which may be NULL
 * depending on the risk assessment desired.
 *
 * Aspects required for each assessment:
 * Data Sensitivity
 *  confidentiality
 *  availability
 *  integrity
 * Impact
 *  confidentiality
 *  availability
 *  integrity
 *  mission criticality
 * Threat Likelihood
 *  countermeasure effectiveness
 *  threat level
 * Overall Risk
 *  confidentiality
 *  availability
 *  integrity
 *  mission criticality
 *  countermeasure effectiveness
 *  threat level
 *
 * Values for the aspects are of the set ["HIGH", "MODERATE", "LOW"].
 * Input values are case-insensitive.
 *
 * Output values are from the set ["HIGH", "MODERATE", "LOW"].
 *
 * @package   Controller
 * @copyright (c) Endeavor Systems, Inc. 2008 (http://www.endeavorsystems.com)
 * @license   http://www.openfisma.org/mw/index.php?title=License
 *
 * @todo This class needs a lot of cleaning up. See the file todo comments.
 */
class RiskAssessment
{
    /*
    ** Private storage of risk attributes.
    */
    var $confidentiality = NULL;
    var $availability = NULL;
    var $integrity = NULL;
    var $criticality = NULL;
    var $threat = NULL;
    var $effectiveness = NULL;

    /**
     * High Risk/Impact Level
     */
    const HIGH = 'HIGH';

    /**
     * Moderate Risk/Impact Level
     */
    const MODERATE = 'MODERATE';

    /**
     * Low Risk/Impact Level
     */
    const LOW = 'LOW';
    
    /*
    ** Instantiate a RiskAssessment class object.
    **
    ** All input values are of the set ["HIGH", "MODERATE", "LOW"].
    ** NULL is a valid value for any input depending on the assessment desired.
    **
    ** Input:
    **  confidentiality - system confidentiality
    **  availability - system availability
    **  integrity - system integrity
    **  criticality - mission criticality
    **  threat_level - POAM threat level
    **  effectiveness - POAM countermeasure effectiveness
    **
    ** Return:
    **  class instantiation
    **
    */
    function __construct($confidentiality,
                         $availability,
                         $integrity,
                         $criticality,
                         $threat_level,
                         $effectiveness)
    {
        // Validate all the values passed in
        if (!is_null($confidentiality)) {
            if ($this->is_valid_value($confidentiality)) {
                //echo "setting confidentiality: $confidentiality<br/>";
                $this->confidentiality = strtoupper($confidentiality);
            } else {
                die("confidentiality value '$confidentiality' not LOW, MODERATE, HIGH or NULL");
            }
        }
        if (!is_null($availability)) {
            if ($this->is_valid_value($availability)) {
                $this->availability = strtoupper($availability);
            } else {
                die("availability value '$availability' not LOW, MODERATE, HIGH or NULL");
            }
        }
        if (!is_null($integrity)) {
            if ($this->is_valid_value($integrity)) {
                $this->integrity = strtoupper($integrity);
            } else {
                die("integrity value '$integrity' not LOW, MODERATE, HIGH or NULL");
            }
        }
        if (!is_null($criticality)) {
            if ($this->is_valid_value($criticality)) {
                $this->criticality = strtoupper($criticality);
            } else {
                die("criticality value '$criticality' not LOW, MODERATE, HIGH or NULL");
            }
        }
        if (!is_null($threat_level)) {
            if ($this->is_valid_value($threat_level)) {
                $this->threat = strtoupper($threat_level);
            } else {
                die("threat value '$threat_level' not LOW, MODERATE, HIGH or NULL");
            }
        }
        if (!is_null($effectiveness)) {
            if ($this->is_valid_value($effectiveness)) {
                $this->effectiveness = strtoupper($effectiveness);
            } else {
                die("effectiveness value '$effectiveness' not LOW, MODERATE, HIGH or NULL");
            }
        }
    }
    
    /*
    ** Calculate data sensitivity.
    ** This returns the high water mark of confidentiality, availability and integrity.
    **
    ** Throws exception if confidentiality, availability or integrity not set (NULL).
    **
    ** Return:
    **  sensitivity: "HIGH", "MODERATE" or "LOW"
    */
    function get_data_sensitivity()
    {
        $sensitivity = NULL;
        //echo "RiskAssessment::HIGH, $this->confidentiality<br/>";
        if (is_null($this->confidentiality) || is_null($this->availability) || is_null($this->integrity)) {
            die("get_data_sensitivity: confidentiality, availability or integrity value(s) NULL;
                unable to determine data sensitivity");
        }
        // Look for high water mark
        if ($this->confidentiality == RiskAssessment::HIGH
            || $this->availability == RiskAssessment::HIGH
            || $this->integrity == RiskAssessment::HIGH) {
            $sensitivity = RiskAssessment::HIGH;
        } else if ($this->confidentiality == RiskAssessment::MODERATE
                   || $this->availability == RiskAssessment::MODERATE
                   || $this->integrity == RiskAssessment::MODERATE) {
            $sensitivity = RiskAssessment::MODERATE;
        } else {
            $sensitivity = RiskAssessment::LOW;
        }
        return ($sensitivity);
    }
    
    /*
    ** Calculate impact.
    ** Combines data sensitivity and mission criticality to determine value.
    **
    ** Throws exception if confidentiality, availability or integrity, mission criticality
    ** not set (NULL).
    **
    ** Return:
    **  sensitivity: "HIGH", "MODERATE" or "LOW"
    */
    function get_impact()
    {
        $impact = NULL;
        //
        // Make sure requisite aspects in place
        //
        if (is_null($this->confidentiality)
            || is_null($this->availability)
            || is_null($this->integrity)
            || is_null($this->criticality)) {
            die("get_impact: confidentiality, availability, integrity or criticality value(s) NULL;
                unable to determine impact");
        }
        //
        // Calculate sensitivity value
        //
        $sensitivity = $this->get_data_sensitivity();
        //
        // Set up a lookup table - first index is data sensitivity, second is mission criticality
        // $impact_lookup[$sensitivity][$criticality] = $impact_assessment
        //
        $impact_lookup[RiskAssessment::HIGH][RiskAssessment::HIGH] = RiskAssessment::HIGH;
        $impact_lookup[RiskAssessment::HIGH][RiskAssessment::MODERATE] = RiskAssessment::MODERATE;
        $impact_lookup[RiskAssessment::HIGH][RiskAssessment::LOW] = RiskAssessment::LOW;
        $impact_lookup[RiskAssessment::MODERATE][RiskAssessment::HIGH] = RiskAssessment::MODERATE;
        $impact_lookup[RiskAssessment::MODERATE][RiskAssessment::MODERATE] = RiskAssessment::MODERATE;
        $impact_lookup[RiskAssessment::MODERATE][RiskAssessment::LOW] = RiskAssessment::LOW;
        $impact_lookup[RiskAssessment::LOW][RiskAssessment::HIGH] = RiskAssessment::LOW;
        $impact_lookup[RiskAssessment::LOW][RiskAssessment::MODERATE] = RiskAssessment::LOW;
        $impact_lookup[RiskAssessment::LOW][RiskAssessment::LOW] = RiskAssessment::LOW;
        $impact = $impact_lookup[$sensitivity][$this->criticality];
        return ($impact);
    }
    
    /*
    ** Calculate threat likelihood.
    ** Combines threat level and countermeasure effectiveness.
    **
    ** Throws exception if threat level or countermeasure effectiveness
    ** not set (NULL).
    **
    ** Return:
    **  likelihood: "HIGH", "MODERATE" or "LOW"
    */
    function get_threat_likelihood()
    {
        $likelihood = NULL;
        //
        // Make sure requisite aspects in place
        //
        if (is_null($this->threat) || is_null($this->effectiveness)) {
            die("get_threat_likelihood: threat level or countermeasure effectiveness value(s) NULL;
                unable to determine threat likelihood");
        }
        //
        // Set up a lookup table - first index is threat level, second is countermeasure effectiveness
        // $threat_lookup[$threat][$effectiveness] = $likelihood_assessment
        //
        $threat_lookup[RiskAssessment::HIGH][RiskAssessment::HIGH] = RiskAssessment::LOW;
        $threat_lookup[RiskAssessment::HIGH][RiskAssessment::MODERATE] = RiskAssessment::MODERATE;
        $threat_lookup[RiskAssessment::HIGH][RiskAssessment::LOW] = RiskAssessment::HIGH;
        $threat_lookup[RiskAssessment::MODERATE][RiskAssessment::HIGH] = RiskAssessment::LOW;
        $threat_lookup[RiskAssessment::MODERATE][RiskAssessment::MODERATE] = RiskAssessment::MODERATE;
        $threat_lookup[RiskAssessment::MODERATE][RiskAssessment::LOW] = RiskAssessment::MODERATE;
        $threat_lookup[RiskAssessment::LOW][RiskAssessment::HIGH] = RiskAssessment::LOW;
        $threat_lookup[RiskAssessment::LOW][RiskAssessment::MODERATE] = RiskAssessment::LOW;
        $threat_lookup[RiskAssessment::LOW][RiskAssessment::LOW] = RiskAssessment::LOW;
        $likelihood = $threat_lookup[$this->threat][$this->effectiveness];
        return ($likelihood);
    }
    
    /*
    ** Calculate overall risk.
    ** Combines threat likelihood and impact, pulling in all risk aspects.
    **
    ** Throws exception if any aspect not set (NULL).
    **
    ** Return:
    **  risk: "HIGH", "MODERATE" or "LOW"
    */
    function get_overall_risk()
    {
        $overall_risk = NULL;
        //
        // Make sure requisite aspects are in place
        //
        if (is_null($this->confidentiality)
            || is_null($this->availability)
            || is_null($this->integrity)
            || is_null($this->criticality)
            || is_null($this->threat)
            || is_null($this->effectiveness)) {
            die("get_overall_risk: confidentiality, availability, integrity, threat level or countermeasure
                effectiveness value(s) NULL; unable to determine overall risk");
        }
        $likelihood = $this->get_threat_likelihood();
        $impact = $this->get_impact();
        //
        // Set up a lookup table - first index is threat likelihood, second is impact
        // $impact_lookup[$likelihood][$impact] = $risk_assessment
        //
        $risk_lookup[RiskAssessment::LOW][RiskAssessment::HIGH] = RiskAssessment::LOW;
        $risk_lookup[RiskAssessment::LOW][RiskAssessment::MODERATE] = RiskAssessment::LOW;
        $risk_lookup[RiskAssessment::LOW][RiskAssessment::LOW] = RiskAssessment::LOW;
        $risk_lookup[RiskAssessment::MODERATE][RiskAssessment::HIGH] = RiskAssessment::MODERATE;
        $risk_lookup[RiskAssessment::MODERATE][RiskAssessment::MODERATE] = RiskAssessment::MODERATE;
        $risk_lookup[RiskAssessment::MODERATE][RiskAssessment::LOW] = RiskAssessment::LOW;
        $risk_lookup[RiskAssessment::HIGH][RiskAssessment::HIGH] = RiskAssessment::HIGH;
        $risk_lookup[RiskAssessment::HIGH][RiskAssessment::MODERATE] = RiskAssessment::MODERATE;
        $risk_lookup[RiskAssessment::HIGH][RiskAssessment::LOW] = RiskAssessment::LOW;
        $overall_risk = $risk_lookup[$likelihood][$impact];
        return ($overall_risk);
    }
    
    /*
    ** Check input value for membership in set of valid values
    ** ["HIGH", "MODERATE", "LOW"].
    **
    ** Input:
    **  input value string
    **
    ** Return:
    **  true if input value is in set, false otherwise
    */
    function is_valid_value($value)
    {
        //
        // Set up list of valid values
        //
        $valid_values[RiskAssessment::LOW] = 1;
        $valid_values[RiskAssessment::MODERATE] = 1;
        $valid_values[RiskAssessment::HIGH] = 1;
        //
        // Convert incoming value to uppercase to match valid array keys
        //
        $ucase_value = strtoupper($value);
        //
        // Return true if uppercase input value matches an entry in valid list,
        // false otherwise.
        //
        return (array_key_exists($ucase_value, $valid_values));
    }
}
