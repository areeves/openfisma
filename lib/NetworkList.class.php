<?PHP

// 
// INCLUDES
// 
require_once('Database.class.php');
require_once('BasicList.class.php');


//
// CLASS DEFINITION
// 

class NetworkList extends BasicList {

  // -----------------------------------------------------------------------
  // 
  // CLASS METHODS
  // 
  // -----------------------------------------------------------------------
  
  public function __construct($db = NULL) { 

	// call the parent constructor with the db connection and table name
	parent::__construct($db, 'NETWORKS'); 

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
  
  public function getNetworkId($isKey = FALSE)                                      { array_push($this->params, 'network_id');                                        if ($isKey) { $this->key = 'network_id'; } }
  public function getNetworkName($isKey = FALSE)                                    { array_push($this->params, 'network_name');                                      if ($isKey) { $this->key = 'network_name'; } }
  public function getNetworkNickname($isKey = FALSE)                                { array_push($this->params, 'network_nickname');                                  if ($isKey) { $this->key = 'network_nickname'; } }
  public function getNetworkDesc($isKey = FALSE)                                    { array_push($this->params, 'network_desc');                                      if ($isKey) { $this->key = 'network_desc'; } }  

  // -----------------------------------------------------------------------
  // 
  // FILTERS
  // 
  // -----------------------------------------------------------------------
  
  public function filterNetworkId($value = NULL, $bool = TRUE)                                      { $this->filters['network_id']                                        = array($value, $bool); }
  public function filterNetworkName($value = NULL, $bool = TRUE)                                    { $this->filters['network_name']                                      = array($value, $bool); }
  public function filterNetworkNickname($value = NULL, $bool = TRUE)                                { $this->filters['network_nickname']                                  = array($value, $bool); }
  public function filterNetworkDesc($value = NULL, $bool = TRUE)                                    { $this->filters['network_desc']                                      = array($value, $bool); }  

} // class NetworkList

?>