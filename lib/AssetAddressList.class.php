<?PHP

// 
// INCLUDES
// 
require_once('Database.class.php');
require_once('BasicList.class.php');


//
// CLASS DEFINITION
// 

class AssetAddressList extends BasicList {

  // -----------------------------------------------------------------------
  // 
  // CLASS METHODS
  // 
  // -----------------------------------------------------------------------
  
  public function __construct($db = NULL) { 

	// call the parent constructor with the db connection and table name
	parent::__construct($db, 'ASSET_ADDRESSES'); 

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
  
  public function getAssetId($isKey = FALSE)                                        { array_push($this->params, 'asset_id');                                          if ($isKey) { $this->key = 'asset_id'; } }
  public function getNetworkId($isKey = FALSE)                                      { array_push($this->params, 'network_id');                                        if ($isKey) { $this->key = 'network_id'; } }
  public function getAddressDateCreated($isKey = FALSE)                             { array_push($this->params, 'address_date_created');                              if ($isKey) { $this->key = 'address_date_created'; } }
  public function getAddressIp($isKey = FALSE)                                      { array_push($this->params, 'address_ip');                                        if ($isKey) { $this->key = 'address_ip'; } }
  public function getAddressPort($isKey = FALSE)                                    { array_push($this->params, 'address_port');                                      if ($isKey) { $this->key = 'address_port'; } }  

  // -----------------------------------------------------------------------
  // 
  // FILTERS
  // 
  // -----------------------------------------------------------------------
  
  public function filterAssetId($value = NULL, $bool = TRUE)                                        { $this->filters['asset_id']                                          = array($value, $bool); }
  public function filterNetworkId($value = NULL, $bool = TRUE)                                      { $this->filters['network_id']                                        = array($value, $bool); }
  public function filterAddressDateCreated($value = NULL, $bool = TRUE)                             { $this->filters['address_date_created']                              = array($value, $bool); }
  public function filterAddressIp($value = NULL, $bool = TRUE)                                      { $this->filters['address_ip']                                        = array($value, $bool); }
  public function filterAddressPort($value = NULL, $bool = TRUE)                                    { $this->filters['address_port']                                      = array($value, $bool); }  

} // class AssetAddressList

?>