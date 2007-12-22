<?PHP

// 
// INCLUDES
// 
require_once('Database.class.php');
require_once('BasicList.class.php');


//
// CLASS DEFINITION
// 

class SystemGroupSystemList extends BasicList {

  // -----------------------------------------------------------------------
  // 
  // CLASS METHODS
  // 
  // -----------------------------------------------------------------------
  
  public function __construct($db = NULL) { 

	// call the parent constructor with the db connection and table name
	parent::__construct($db, 'SYSTEM_GROUP_SYSTEMS'); 

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
  
  public function getSysgroupId($isKey = FALSE)                                     { array_push($this->params, 'sysgroup_id');                                       if ($isKey) { $this->key = 'sysgroup_id'; } }
  public function getSystemId($isKey = FALSE)                                       { array_push($this->params, 'system_id');                                         if ($isKey) { $this->key = 'system_id'; } }  

  // -----------------------------------------------------------------------
  // 
  // FILTERS
  // 
  // -----------------------------------------------------------------------
  
  public function filterSysgroupId($value = NULL, $bool = TRUE)                                     { $this->filters['sysgroup_id']                                       = array($value, $bool); }
  public function filterSystemId($value = NULL, $bool = TRUE)                                       { $this->filters['system_id']                                         = array($value, $bool); }  

} // class SystemGroupSystemList

?>