<?PHP

// 
// INCLUDES
// 
require_once('Database.class.php');
require_once('BasicList.class.php');


//
// CLASS DEFINITION
// 

class VulnSolutionList extends BasicList {

  // -----------------------------------------------------------------------
  // 
  // CLASS METHODS
  // 
  // -----------------------------------------------------------------------
  
  public function __construct($db = NULL) { 

	// call the parent constructor with the db connection and table name
	parent::__construct($db, 'VULN_SOLUTIONS'); 

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
  
  public function getVulnSeq($isKey = FALSE)                                        { array_push($this->params, 'vuln_seq');                                          if ($isKey) { $this->key = 'vuln_seq'; } }
  public function getVulnType($isKey = FALSE)                                       { array_push($this->params, 'vuln_type');                                         if ($isKey) { $this->key = 'vuln_type'; } }
  public function getSolDesc($isKey = FALSE)                                        { array_push($this->params, 'sol_desc');                                          if ($isKey) { $this->key = 'sol_desc'; } }
  public function getSolSource($isKey = FALSE)                                      { array_push($this->params, 'sol_source');                                        if ($isKey) { $this->key = 'sol_source'; } }  

  // -----------------------------------------------------------------------
  // 
  // FILTERS
  // 
  // -----------------------------------------------------------------------
  
  public function filterVulnSeq($value = NULL, $bool = TRUE)                                        { $this->filters['vuln_seq']                                          = array($value, $bool); }
  public function filterVulnType($value = NULL, $bool = TRUE)                                       { $this->filters['vuln_type']                                         = array($value, $bool); }
  public function filterSolDesc($value = NULL, $bool = TRUE)                                        { $this->filters['sol_desc']                                          = array($value, $bool); }
  public function filterSolSource($value = NULL, $bool = TRUE)                                      { $this->filters['sol_source']                                        = array($value, $bool); }  

} // class VulnSolutionList

?>