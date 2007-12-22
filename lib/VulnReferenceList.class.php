<?PHP

// 
// INCLUDES
// 
require_once('Database.class.php');
require_once('BasicList.class.php');


//
// CLASS DEFINITION
// 

class VulnReferenceList extends BasicList {

  // -----------------------------------------------------------------------
  // 
  // CLASS METHODS
  // 
  // -----------------------------------------------------------------------
  
  public function __construct($db = NULL) { 

	// call the parent constructor with the db connection and table name
	parent::__construct($db, 'VULN_REFERENCES'); 

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
  
  public function getVulnType($isKey = FALSE)                                       { array_push($this->params, 'vuln_type');                                         if ($isKey) { $this->key = 'vuln_type'; } }
  public function getVulnSeq($isKey = FALSE)                                        { array_push($this->params, 'vuln_seq');                                          if ($isKey) { $this->key = 'vuln_seq'; } }
  public function getRefName($isKey = FALSE)                                        { array_push($this->params, 'ref_name');                                          if ($isKey) { $this->key = 'ref_name'; } }
  public function getRefSource($isKey = FALSE)                                      { array_push($this->params, 'ref_source');                                        if ($isKey) { $this->key = 'ref_source'; } }
  public function getRefUrl($isKey = FALSE)                                         { array_push($this->params, 'ref_url');                                           if ($isKey) { $this->key = 'ref_url'; } }
  public function getRefIsAdvisory($isKey = FALSE)                                  { array_push($this->params, 'ref_is_advisory');                                   if ($isKey) { $this->key = 'ref_is_advisory'; } }
  public function getRefHasToolSig($isKey = FALSE)                                  { array_push($this->params, 'ref_has_tool_sig');                                  if ($isKey) { $this->key = 'ref_has_tool_sig'; } }
  public function getRefHasPatch($isKey = FALSE)                                    { array_push($this->params, 'ref_has_patch');                                     if ($isKey) { $this->key = 'ref_has_patch'; } }  

  // -----------------------------------------------------------------------
  // 
  // FILTERS
  // 
  // -----------------------------------------------------------------------
  
  public function filterVulnType($value = NULL, $bool = TRUE)                                       { $this->filters['vuln_type']                                         = array($value, $bool); }
  public function filterVulnSeq($value = NULL, $bool = TRUE)                                        { $this->filters['vuln_seq']                                          = array($value, $bool); }
  public function filterRefName($value = NULL, $bool = TRUE)                                        { $this->filters['ref_name']                                          = array($value, $bool); }
  public function filterRefSource($value = NULL, $bool = TRUE)                                      { $this->filters['ref_source']                                        = array($value, $bool); }
  public function filterRefUrl($value = NULL, $bool = TRUE)                                         { $this->filters['ref_url']                                           = array($value, $bool); }
  public function filterRefIsAdvisory($value = NULL, $bool = TRUE)                                  { $this->filters['ref_is_advisory']                                   = array($value, $bool); }
  public function filterRefHasToolSig($value = NULL, $bool = TRUE)                                  { $this->filters['ref_has_tool_sig']                                  = array($value, $bool); }
  public function filterRefHasPatch($value = NULL, $bool = TRUE)                                    { $this->filters['ref_has_patch']                                     = array($value, $bool); }  

} // class VulnReferenceList

?>