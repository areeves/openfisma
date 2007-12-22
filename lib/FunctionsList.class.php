<?PHP

// 
// INCLUDES
// 
require_once('Database.class.php');
require_once('BasicList.class.php');


//
// CLASS DEFINITION
// 

class FunctionsList extends BasicList {

  // -----------------------------------------------------------------------
  // 
  // CLASS METHODS
  // 
  // -----------------------------------------------------------------------
  
  public function __construct($db = NULL) { 

	// call the parent constructor with the db connection and table name
	parent::__construct($db, 'FUNCTIONS'); 

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
  
  public function getFunction($isKey = FALSE)                                       { array_push($this->params, 'function');                                          if ($isKey) { $this->key = 'function'; } }
  public function getFunctionId($isKey = FALSE)                                     { array_push($this->params, 'function_id');                                       if ($isKey) { $this->key = 'function_id'; } }
  public function getFunctionName($isKey = FALSE)                                   { array_push($this->params, 'function_name');                                     if ($isKey) { $this->key = 'function_name'; } }
  public function getFunctionScreen($isKey = FALSE)                                 { array_push($this->params, 'function_screen');                                   if ($isKey) { $this->key = 'function_screen'; } }
  public function getFunctionAction($isKey = FALSE)                                 { array_push($this->params, 'function_action');                                   if ($isKey) { $this->key = 'function_action'; } }
  public function getFunctionDesc($isKey = FALSE)                                   { array_push($this->params, 'function_desc');                                     if ($isKey) { $this->key = 'function_desc'; } }
  public function getFunctionOpen($isKey = FALSE)                                   { array_push($this->params, 'function_open');                                     if ($isKey) { $this->key = 'function_open'; } }  

  // -----------------------------------------------------------------------
  // 
  // FILTERS
  // 
  // -----------------------------------------------------------------------
  
  public function filterFunction($value = NULL, $bool = TRUE)                                       { $this->filters['function']                                          = array($value, $bool); }
  public function filterFunctionId($value = NULL, $bool = TRUE)                                     { $this->filters['function_id']                                       = array($value, $bool); }
  public function filterFunctionName($value = NULL, $bool = TRUE)                                   { $this->filters['function_name']                                     = array($value, $bool); }
  public function filterFunctionScreen($value = NULL, $bool = TRUE)                                 { $this->filters['function_screen']                                   = array($value, $bool); }
  public function filterFunctionAction($value = NULL, $bool = TRUE)                                 { $this->filters['function_action']                                   = array($value, $bool); }
  public function filterFunctionDesc($value = NULL, $bool = TRUE)                                   { $this->filters['function_desc']                                     = array($value, $bool); }
  public function filterFunctionOpen($value = NULL, $bool = TRUE)                                   { $this->filters['function_open']                                     = array($value, $bool); }  

} // class FunctionsList

?>