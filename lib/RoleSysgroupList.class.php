<?PHP

// 
// INCLUDES
// 
require_once('Database.class.php');
require_once('BasicList.class.php');


//
// CLASS DEFINITION
// 

class RoleSysgroupList extends BasicList {

  // -----------------------------------------------------------------------
  // 
  // CLASS METHODS
  // 
  // -----------------------------------------------------------------------
  
  public function __construct($db = NULL) { 

	// call the parent constructor with the db connection and table name
	parent::__construct($db, 'ROLE_SYSGROUPS'); 

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
  
  public function getRoleGroupId($isKey = FALSE)                                    { array_push($this->params, 'role_group_id');                                     if ($isKey) { $this->key = 'role_group_id'; } }
  public function getRoleId($isKey = FALSE)                                         { array_push($this->params, 'role_id');                                           if ($isKey) { $this->key = 'role_id'; } }
  public function getSysgroupId($isKey = FALSE)                                     { array_push($this->params, 'sysgroup_id');                                       if ($isKey) { $this->key = 'sysgroup_id'; } }  

  // -----------------------------------------------------------------------
  // 
  // FILTERS
  // 
  // -----------------------------------------------------------------------
  
  public function filterRoleGroupId($value = NULL, $bool = TRUE)                                    { $this->filters['role_group_id']                                     = array($value, $bool); }
  public function filterRoleId($value = NULL, $bool = TRUE)                                         { $this->filters['role_id']                                           = array($value, $bool); }
  public function filterSysgroupId($value = NULL, $bool = TRUE)                                     { $this->filters['sysgroup_id']                                       = array($value, $bool); }  

} // class RoleSysgroupList

?>