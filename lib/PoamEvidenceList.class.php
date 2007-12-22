<?PHP

// 
// INCLUDES
// 
require_once('Database.class.php');
require_once('BasicList.class.php');


//
// CLASS DEFINITION
// 

class PoamEvidenceList extends BasicList {

  // -----------------------------------------------------------------------
  // 
  // CLASS METHODS
  // 
  // -----------------------------------------------------------------------
  
  public function __construct($db = NULL) { 

	// call the parent constructor with the db connection and table name
	parent::__construct($db, 'POAM_EVIDENCE'); 

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
  
  public function getEvId($isKey = FALSE)                                           { array_push($this->params, 'ev_id');                                             if ($isKey) { $this->key = 'ev_id'; } }
  public function getPoamId($isKey = FALSE)                                         { array_push($this->params, 'poam_id');                                           if ($isKey) { $this->key = 'poam_id'; } }
  public function getEvSubmission($isKey = FALSE)                                   { array_push($this->params, 'ev_submission');                                     if ($isKey) { $this->key = 'ev_submission'; } }
  public function getEvSubmittedBy($isKey = FALSE)                                  { array_push($this->params, 'ev_submitted_by');                                   if ($isKey) { $this->key = 'ev_submitted_by'; } }
  public function getEvDateSubmitted($isKey = FALSE)                                { array_push($this->params, 'ev_date_submitted');                                 if ($isKey) { $this->key = 'ev_date_submitted'; } }
  public function getEvSsoEvaluation($isKey = FALSE)                                { array_push($this->params, 'ev_sso_evaluation');                                 if ($isKey) { $this->key = 'ev_sso_evaluation'; } }
  public function getEvDateSsoEvaluation($isKey = FALSE)                            { array_push($this->params, 'ev_date_sso_evaluation');                            if ($isKey) { $this->key = 'ev_date_sso_evaluation'; } }
  public function getEvFsaEvaluation($isKey = FALSE)                                { array_push($this->params, 'ev_fsa_evaluation');                                 if ($isKey) { $this->key = 'ev_fsa_evaluation'; } }
  public function getEvDateFsaEvaluation($isKey = FALSE)                            { array_push($this->params, 'ev_date_fsa_evaluation');                            if ($isKey) { $this->key = 'ev_date_fsa_evaluation'; } }  

  // -----------------------------------------------------------------------
  // 
  // FILTERS
  // 
  // -----------------------------------------------------------------------
  
  public function filterEvId($value = NULL, $bool = TRUE)                                           { $this->filters['ev_id']                                             = array($value, $bool); }
  public function filterPoamId($value = NULL, $bool = TRUE)                                         { $this->filters['poam_id']                                           = array($value, $bool); }
  public function filterEvSubmission($value = NULL, $bool = TRUE)                                   { $this->filters['ev_submission']                                     = array($value, $bool); }
  public function filterEvSubmittedBy($value = NULL, $bool = TRUE)                                  { $this->filters['ev_submitted_by']                                   = array($value, $bool); }
  public function filterEvDateSubmitted($value = NULL, $bool = TRUE)                                { $this->filters['ev_date_submitted']                                 = array($value, $bool); }
  public function filterEvSsoEvaluation($value = NULL, $bool = TRUE)                                { $this->filters['ev_sso_evaluation']                                 = array($value, $bool); }
  public function filterEvDateSsoEvaluation($value = NULL, $bool = TRUE)                            { $this->filters['ev_date_sso_evaluation']                            = array($value, $bool); }
  public function filterEvFsaEvaluation($value = NULL, $bool = TRUE)                                { $this->filters['ev_fsa_evaluation']                                 = array($value, $bool); }
  public function filterEvDateFsaEvaluation($value = NULL, $bool = TRUE)                            { $this->filters['ev_date_fsa_evaluation']                            = array($value, $bool); }  

} // class PoamEvidenceList

?>