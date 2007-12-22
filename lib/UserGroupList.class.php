<?PHP

// 
// INCLUDES
// 
require_once('Database.class.php');
require_once('BasicList.class.php');


//
// CLASS DEFINITION
// 

class UserGroupList extends BasicList {

  // -----------------------------------------------------------------------
  // 
  // CLASS METHODS
  // 
  // -----------------------------------------------------------------------
  
  public function __construct($db = NULL) { 

	// call the parent constructor with the db connection and table name
	parent::__construct($db, 'USER_SYSGROUPS'); 

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
  
  public function getUserGroupId($isKey = FALSE)                                    { array_push($this->params, 'user_group_id');                                     if ($isKey) { $this->key = 'user_group_id'; } }
  public function getUserId($isKey = FALSE)                                         { array_push($this->params, 'user_id');                                           if ($isKey) { $this->key = 'user_id'; } }
  public function getSysgroupId($isKey = FALSE)                                     { array_push($this->params, 'sysgroup_id');                                       if ($isKey) { $this->key = 'sysgroup_id'; } }  

  // -----------------------------------------------------------------------
  // 
  // FILTERS
  // 
  // -----------------------------------------------------------------------
  
  public function filterUserGroupId($value = NULL, $bool = TRUE)                                    { $this->filters['user_group_id']                                     = array($value, $bool); }
  public function filterUserId($value = NULL, $bool = TRUE)                                         { $this->filters['user_id']                                           = array($value, $bool); }
  public function filterSysgroupId($value = NULL, $bool = TRUE)                                     { $this->filters['sysgroup_id']                                       = array($value, $bool); }  

} // class UserGroupList

?>