<?PHP

// 
// INCLUDES
// 
require_once('Database.class.php');
require_once('BasicList.class.php');


//
// CLASS DEFINITION
// 

class VulnProductList extends BasicList {

  // -----------------------------------------------------------------------
  // 
  // CLASS METHODS
  // 
  // -----------------------------------------------------------------------
  
  public function __construct($db = NULL) { 

	// call the parent constructor with the db connection and table name
	parent::__construct($db, 'VULN_PRODUCTS'); 

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
  public function getProdId($isKey = FALSE)                                         { array_push($this->params, 'prod_id');                                           if ($isKey) { $this->key = 'prod_id'; } }  

  // -----------------------------------------------------------------------
  // 
  // FILTERS
  // 
  // -----------------------------------------------------------------------
  
  public function filterVulnSeq($value = NULL, $bool = TRUE)                                        { $this->filters['vuln_seq']                                          = array($value, $bool); }
  public function filterVulnType($value = NULL, $bool = TRUE)                                       { $this->filters['vuln_type']                                         = array($value, $bool); }
  public function filterProdId($value = NULL, $bool = TRUE)                                         { $this->filters['prod_id']                                           = array($value, $bool); }  

} // class VulnProductList

?>