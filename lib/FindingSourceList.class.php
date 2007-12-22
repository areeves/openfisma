<?PHP

// 
// INCLUDES
// 
require_once('Database.class.php');
require_once('BasicList.class.php');


//
// CLASS DEFINITION
// 

class FindingSourceList extends BasicList {

  // -----------------------------------------------------------------------
  // 
  // CLASS METHODS
  // 
  // -----------------------------------------------------------------------
  
  public function __construct($db = NULL) { 

	// call the parent constructor with the db connection and table name
	parent::__construct($db, 'FINDING_SOURCES'); 

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
  
  public function getSourceId($isKey = FALSE)       { array_push($this->params, 'source_id');       if ($isKey) { $this->key = 'source_id';       } }
  public function getSourceName($isKey = FALSE)     { array_push($this->params, 'source_name');     if ($isKey) { $this->key = 'source_name';     } }
  public function getSourceNickname($isKey = FALSE) { array_push($this->params, 'source_nickname'); if ($isKey) { $this->key = 'source_nickname'; } }
  public function getSourceDesc($isKey = FALSE)     { array_push($this->params, 'source_desc');     if ($isKey) { $this->key = 'source_desc';     } }  

  // -----------------------------------------------------------------------
  // 
  // FILTERS
  // 
  // -----------------------------------------------------------------------
  
  public function filterSourceId($value = NULL, $bool = TRUE)       { $this->filters['source_id']       = array($value, $bool); }
  public function filterSourceName($value = NULL, $bool = TRUE)     { $this->filters['source_name']     = array($value, $bool); }
  public function filterSourceNickname($value = NULL, $bool = TRUE) { $this->filters['source_nickname'] = array($value, $bool); }
  public function filterSourceDesc($value = NULL, $bool = TRUE)     { $this->filters['source_desc']     = array($value, $bool); }  

} // class FindingSourceList

?>