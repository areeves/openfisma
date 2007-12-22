<?PHP

// 
// INCLUDES
// 
require_once('Database.class.php');
require_once('BasicList.class.php');


//
// CLASS DEFINITION
// 

class SystemAssetList extends BasicList {

  // -----------------------------------------------------------------------
  // 
  // CLASS METHODS
  // 
  // -----------------------------------------------------------------------
  
  public function __construct($db = NULL) { 

	// call the parent constructor with the db connection and table name
	parent::__construct($db, 'SYSTEM_ASSETS'); 

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
  
  public function getSystemId($isKey = FALSE)                                       { array_push($this->params, 'system_id');                                         if ($isKey) { $this->key = 'system_id'; } }
  public function getAssetId($isKey = FALSE)                                        { array_push($this->params, 'asset_id');                                          if ($isKey) { $this->key = 'asset_id'; } }
  public function getSystemIsOwner($isKey = FALSE)                                  { array_push($this->params, 'system_is_owner');                                   if ($isKey) { $this->key = 'system_is_owner'; } }  

  // -----------------------------------------------------------------------
  // 
  // FILTERS
  // 
  // -----------------------------------------------------------------------
  
  public function filterSystemId($value = NULL, $bool = TRUE)                                       { $this->filters['system_id']                                         = array($value, $bool); }
  public function filterAssetId($value = NULL, $bool = TRUE)                                        { $this->filters['asset_id']                                          = array($value, $bool); }
  public function filterSystemIsOwner($value = NULL, $bool = TRUE)                                  { $this->filters['system_is_owner']                                   = array($value, $bool); }  

} // class SystemAssetList

?>