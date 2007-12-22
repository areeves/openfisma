<?PHP

// 
// INCLUDES
// 
require_once('Database.class.php');
require_once('BasicList.class.php');


//
// CLASS DEFINITION
// 

class RoleFunctionList extends BasicList {

  // -----------------------------------------------------------------------
  // 
  // CLASS METHODS
  // 
  // -----------------------------------------------------------------------
  
  public function __construct($db = NULL) { 

	// call the parent constructor with the db connection and table name
	parent::__construct($db, 'ROLE_FUNCTIONS'); 

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
  
  public function getRoleFuncId($isKey = FALSE)                                     { array_push($this->params, 'role_func_id');                                      if ($isKey) { $this->key = 'role_func_id'; } }
  public function getRoleId($isKey = FALSE)                                         { array_push($this->params, 'role_id');                                           if ($isKey) { $this->key = 'role_id'; } }
  public function getFunctionId($isKey = FALSE)                                     { array_push($this->params, 'function_id');                                       if ($isKey) { $this->key = 'function_id'; } }  

  // -----------------------------------------------------------------------
  // 
  // FILTERS
  // 
  // -----------------------------------------------------------------------
  
  public function filterRoleFuncId($value = NULL, $bool = TRUE)                                     { $this->filters['role_func_id']                                      = array($value, $bool); }
  public function filterRoleId($value = NULL, $bool = TRUE)                                         { $this->filters['role_id']                                           = array($value, $bool); }
  public function filterFunctionId($value = NULL, $bool = TRUE)                                     { $this->filters['function_id']                                       = array($value, $bool); }  

} // class RoleFunctionList

?>