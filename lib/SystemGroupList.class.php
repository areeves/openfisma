<?PHP

// 
// INCLUDES
// 
require_once('Database.class.php');
require_once('BasicList.class.php');


//
// CLASS DEFINITION
// 

class SystemGroupList extends BasicList {

  // -----------------------------------------------------------------------
  // 
  // CLASS METHODS
  // 
  // -----------------------------------------------------------------------
  
  public function __construct($db = NULL) { 

	// call the parent constructor with the db connection and table name
	parent::__construct($db, 'SYSTEM_GROUPS'); 

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
  public function getSysgroupName($isKey = FALSE)                                   { array_push($this->params, 'sysgroup_name');                                     if ($isKey) { $this->key = 'sysgroup_name'; } }
  public function getSysgroupNickname($isKey = FALSE)                               { array_push($this->params, 'sysgroup_nickname');                                 if ($isKey) { $this->key = 'sysgroup_nickname'; } }
  public function getSysgroupIsIdentity($isKey = FALSE)                             { array_push($this->params, 'sysgroup_is_identity');                              if ($isKey) { $this->key = 'sysgroup_is_identity'; } }  

  // -----------------------------------------------------------------------
  // 
  // FILTERS
  // 
  // -----------------------------------------------------------------------
  
  public function filterSysgroupId($value = NULL, $bool = TRUE)                                     { $this->filters['sysgroup_id']                                       = array($value, $bool); }
  public function filterSysgroupName($value = NULL, $bool = TRUE)                                   { $this->filters['sysgroup_name']                                     = array($value, $bool); }
  public function filterSysgroupNickname($value = NULL, $bool = TRUE)                               { $this->filters['sysgroup_nickname']                                 = array($value, $bool); }
  public function filterSysgroupIsIdentity($value = NULL, $bool = TRUE)                             { $this->filters['sysgroup_is_identity']                              = array($value, $bool); }  

} // class SystemGroupList

?>