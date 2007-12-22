<?PHP

// 
// INCLUDES
// 
require_once('Database.class.php');
require_once('BasicList.class.php');


//
// CLASS DEFINITION
// 

class PoamList extends BasicList {

  // -----------------------------------------------------------------------
  // 
  // CLASS METHODS
  // 
  // -----------------------------------------------------------------------
  
  public function __construct($db = NULL) { 

	// call the parent constructor with the db connection and table name
	parent::__construct($db, 'POAMS'); 

  } // __construct()
  

  public function __destruct() {

	// call the parent destructor
	parent::__destruct();

  } // __destruct()
  
  public function __ToString() {} // __ToString()
 	

  // -----------------------------------------------------------------------
  // 
  // PARAMETERS
  // 
  // -----------------------------------------------------------------------
  
  public function getPoamId($isKey = FALSE)                    { array_push($this->params, 'poam_id');                     if ($isKey) { $this->key = 'poam_id';                     } }
  public function getFindingId($isKey = FALSE)                 { array_push($this->params, 'finding_id');                  if ($isKey) { $this->key = 'finding_id';                  } }
  public function getLegacyPoamId($isKey = FALSE)              { array_push($this->params, 'legacy_poam_id');              if ($isKey) { $this->key = 'legacy_poam_id';              } }
  public function getPoamIsRepeat($isKey = FALSE)              { array_push($this->params, 'poam_is_repeat');              if ($isKey) { $this->key = 'poam_is_repeat';              } }
  public function getPoamPreviousAudits($isKey = FALSE)        { array_push($this->params, 'poam_previous_audits');        if ($isKey) { $this->key = 'poam_previous_audits';        } }
  public function getPoamType($isKey = FALSE)                  { array_push($this->params, 'poam_type');                   if ($isKey) { $this->key = 'poam_type';                   } }
  public function getPoamStatus($isKey = FALSE)                { array_push($this->params, 'poam_status');                 if ($isKey) { $this->key = 'poam_status';                 } }
  public function getPoamBlscr($isKey = FALSE)                 { array_push($this->params, 'poam_blscr');                  if ($isKey) { $this->key = 'poam_blscr';                  } }
  public function getPoamCreatedBy($isKey = FALSE)             { array_push($this->params, 'poam_created_by');             if ($isKey) { $this->key = 'poam_created_by';             } }
  public function getPoamModifiedBy($isKey = FALSE)            { array_push($this->params, 'poam_modified_by');            if ($isKey) { $this->key = 'poam_modified_by';            } }
  public function getPoamClosedBy($isKey = FALSE)              { array_push($this->params, 'poam_closed_by');              if ($isKey) { $this->key = 'poam_closed_by';              } }
  public function getPoamDateCreated($isKey = FALSE)           { array_push($this->params, 'poam_date_created');           if ($isKey) { $this->key = 'poam_date_created';           } }
  public function getPoamDateModified($isKey = FALSE)          { array_push($this->params, 'poam_date_modified');          if ($isKey) { $this->key = 'poam_date_modified';          } }
  public function getPoamDateClosed($isKey = FALSE)            { array_push($this->params, 'poam_date_closed');            if ($isKey) { $this->key = 'poam_date_closed';            } }
  public function getPoamActionOwner($isKey = FALSE)           { array_push($this->params, 'poam_action_owner');           if ($isKey) { $this->key = 'poam_action_owner';           } }
  public function getPoamActionSuggested($isKey = FALSE)       { array_push($this->params, 'poam_action_suggested');       if ($isKey) { $this->key = 'poam_action_suggested';       } }
  public function getPoamActionPlanned($isKey = FALSE)         { array_push($this->params, 'poam_action_planned');         if ($isKey) { $this->key = 'poam_action_planned';         } }
  public function getPoamActionStatus($isKey = FALSE)          { array_push($this->params, 'poam_action_status');          if ($isKey) { $this->key = 'poam_action_status';          } }
  public function getPoamActionApprovedBy($isKey = FALSE)      { array_push($this->params, 'poam_action_approved_by');     if ($isKey) { $this->key = 'poam_action_approved_by';     } }
  public function getPoamCmeasure($isKey = FALSE)              { array_push($this->params, 'poam_cmeasure');               if ($isKey) { $this->key = 'poam_cmeasure';               } }
  public function getPoamCmeasureEffectiveness($isKey = FALSE) { array_push($this->params, 'poam_cmeasure_effectiveness'); if ($isKey) { $this->key = 'poam_cmeasure_effectiveness'; } }
  public function getPoamCmeasureJustification($isKey = FALSE) { array_push($this->params, 'poam_cmeasure_justification'); if ($isKey) { $this->key = 'poam_cmeasure_justification'; } }
  public function getPoamActionResources($isKey = FALSE)       { array_push($this->params, 'poam_action_resources');       if ($isKey) { $this->key = 'poam_action_resources';       } }
  public function getPoamActionDateEst($isKey = FALSE)         { array_push($this->params, 'poam_action_date_est');        if ($isKey) { $this->key = 'poam_action_date_est';        } }
  public function getPoamActionDateActual($isKey = FALSE)      { array_push($this->params, 'poam_action_date_actual');     if ($isKey) { $this->key = 'poam_action_date_actual';     } }
  public function getPoamThreatSource($isKey = FALSE)          { array_push($this->params, 'poam_threat_source');          if ($isKey) { $this->key = 'poam_threat_source';          } }
  public function getPoamThreatLevel($isKey = FALSE)           { array_push($this->params, 'poam_threat_level');           if ($isKey) { $this->key = 'poam_threat_level';           } }
  public function getPoamThreatJustification($isKey = FALSE)   { array_push($this->params, 'poam_threat_justification');   if ($isKey) { $this->key = 'poam_threat_justification';   } }  

  // -----------------------------------------------------------------------
  // 
  // FILTERS
  // 
  // -----------------------------------------------------------------------
  
  public function filterPoamId($value = NULL, $bool = TRUE)                    { $this->filters['poam_id']                     = array($value, $bool); }
  public function filterFindingId($value = NULL, $bool = TRUE)                 { $this->filters['finding_id']                  = array($value, $bool); }
  public function filterLegacyPoamId($value = NULL, $bool = TRUE)              { $this->filters['legacy_poam_id']              = array($value, $bool); }
  public function filterPoamIsRepeat($value = NULL, $bool = TRUE)              { $this->filters['poam_is_repeat']              = array($value, $bool); }
  public function filterPoamPreviousAudits($value = NULL, $bool = TRUE)        { $this->filters['poam_previous_audits']        = array($value, $bool); }
  public function filterPoamType($value = NULL, $bool = TRUE)                  { $this->filters['poam_type']                   = array($value, $bool); }
  public function filterPoamStatus($value = NULL, $bool = TRUE)                { $this->filters['poam_status']                 = array($value, $bool); }
  public function filterPoamBlscr($value = NULL, $bool = TRUE)                 { $this->filters['poam_blscr']                  = array($value, $bool); }
  public function filterPoamCreatedBy($value = NULL, $bool = TRUE)             { $this->filters['poam_created_by']             = array($value, $bool); }
  public function filterPoamModifiedBy($value = NULL, $bool = TRUE)            { $this->filters['poam_modified_by']            = array($value, $bool); }
  public function filterPoamClosedBy($value = NULL, $bool = TRUE)              { $this->filters['poam_closed_by']              = array($value, $bool); }
  public function filterPoamDateCreated($value = NULL, $bool = TRUE)           { $this->filters['poam_date_created']           = array($value, $bool); }
  public function filterPoamDateModified($value = NULL, $bool = TRUE)          { $this->filters['poam_date_modified']          = array($value, $bool); }
  public function filterPoamDateClosed($value = NULL, $bool = TRUE)            { $this->filters['poam_date_closed']            = array($value, $bool); }
  public function filterPoamActionOwner($value = NULL, $bool = TRUE)           { $this->filters['poam_action_owner']           = array($value, $bool); }
  public function filterPoamActionSuggested($value = NULL, $bool = TRUE)       { $this->filters['poam_action_suggested']       = array($value, $bool); }
  public function filterPoamActionPlanned($value = NULL, $bool = TRUE)         { $this->filters['poam_action_planned']         = array($value, $bool); }
  public function filterPoamActionStatus($value = NULL, $bool = TRUE)          { $this->filters['poam_action_status']          = array($value, $bool); }
  public function filterPoamActionApprovedBy($value = NULL, $bool = TRUE)      { $this->filters['poam_action_approved_by']     = array($value, $bool); }
  public function filterPoamCmeasure($value = NULL, $bool = TRUE)              { $this->filters['poam_cmeasure']               = array($value, $bool); }
  public function filterPoamCmeasureEffectiveness($value = NULL, $bool = TRUE) { $this->filters['poam_cmeasure_effectiveness'] = array($value, $bool); }
  public function filterPoamCmeasureJustification($value = NULL, $bool = TRUE) { $this->filters['poam_cmeasure_justification'] = array($value, $bool); }
  public function filterPoamActionResources($value = NULL, $bool = TRUE)       { $this->filters['poam_action_resources']       = array($value, $bool); }
  public function filterPoamActionDateEst($value = NULL, $bool = TRUE)         { $this->filters['poam_action_date_est']        = array($value, $bool); }
  public function filterPoamActionDateActual($value = NULL, $bool = TRUE)      { $this->filters['poam_action_date_actual']     = array($value, $bool); }
  public function filterPoamThreatSource($value = NULL, $bool = TRUE)          { $this->filters['poam_threat_source']          = array($value, $bool); }
  public function filterPoamThreatLevel($value = NULL, $bool = TRUE)           { $this->filters['poam_threat_level']           = array($value, $bool); }
  public function filterPoamThreatJustification($value = NULL, $bool = TRUE)   { $this->filters['poam_threat_justification']   = array($value, $bool); }  

} // class PoamList

?>