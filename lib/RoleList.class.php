<?PHP

// 
// INCLUDES
// 
require_once('Database.class.php');
require_once('BasicList.class.php');


//
// CLASS DEFINITION
// 

class RoleList extends BasicList {

  // -----------------------------------------------------------------------
  // 
  // CLASS METHODS
  // 
  // -----------------------------------------------------------------------
  
  public function __construct($db = NULL) { 

	// call the parent constructor with the db connection and table name
	parent::__construct($db, 'ROLES'); 

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
  
  public function getRoleId($isKey = FALSE)                                         { array_push($this->params, 'role_id');                                           if ($isKey) { $this->key = 'role_id'; } }
  public function getRoleName($isKey = FALSE)                                       { array_push($this->params, 'role_name');                                         if ($isKey) { $this->key = 'role_name'; } }
  public function getRoleNickname($isKey = FALSE)                                   { array_push($this->params, 'role_nickname');                                     if ($isKey) { $this->key = 'role_nickname'; } }
  public function getRoleDesc($isKey = FALSE)                                       { array_push($this->params, 'role_desc');                                         if ($isKey) { $this->key = 'role_desc'; } }  

  // -----------------------------------------------------------------------
  // 
  // FILTERS
  // 
  // -----------------------------------------------------------------------
  
  public function filterRoleId($value = NULL, $bool = TRUE)                                         { $this->filters['role_id']                                           = array($value, $bool); }
  public function filterRoleName($value = NULL, $bool = TRUE)                                       { $this->filters['role_name']                                         = array($value, $bool); }
  public function filterRoleNickname($value = NULL, $bool = TRUE)                                   { $this->filters['role_nickname']                                     = array($value, $bool); }
  public function filterRoleDesc($value = NULL, $bool = TRUE)                                       { $this->filters['role_desc']                                         = array($value, $bool); }  

} // class RoleList

?>