<?PHP

// 
// INCLUDES
// 
require_once('Database.class.php');
require_once('BasicList.class.php');


//
// CLASS DEFINITION
// 

class FindingList extends BasicList {

  // -----------------------------------------------------------------------
  // 
  // CLASS METHODS
  // 
  // -----------------------------------------------------------------------
  
  public function __construct($db = NULL) { 

	// call the parent constructor with the db connection and table name
	parent::__construct($db, 'FINDINGS'); 

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
  
  public function getFindingId($isKey = FALSE)                                      { array_push($this->params, 'finding_id');                                        if ($isKey) { $this->key = 'finding_id'; } }
  public function getSourceId($isKey = FALSE)                                       { array_push($this->params, 'source_id');                                         if ($isKey) { $this->key = 'source_id'; } }
  public function getAssetId($isKey = FALSE)                                        { array_push($this->params, 'asset_id');                                          if ($isKey) { $this->key = 'asset_id'; } }
  public function getFindingStatus($isKey = FALSE)                                  { array_push($this->params, 'finding_status');                                    if ($isKey) { $this->key = 'finding_status'; } }
  public function getFindingDateCreated($isKey = FALSE)                             { array_push($this->params, 'finding_date_created');                              if ($isKey) { $this->key = 'finding_date_created'; } }
  public function getFindingDateDiscovered($isKey = FALSE)                          { array_push($this->params, 'finding_date_discovered');                           if ($isKey) { $this->key = 'finding_date_discovered'; } }
  public function getFindingDateClosed($isKey = FALSE)                              { array_push($this->params, 'finding_date_closed');                               if ($isKey) { $this->key = 'finding_date_closed'; } }
  public function getFindingData($isKey = FALSE)                                    { array_push($this->params, 'finding_data');                                      if ($isKey) { $this->key = 'finding_data'; } }  

  // -----------------------------------------------------------------------
  // 
  // FILTERS
  // 
  // -----------------------------------------------------------------------
  
  public function filterFindingId($value = NULL, $bool = TRUE)                                      { $this->filters['finding_id']                                        = array($value, $bool); }
  public function filterSourceId($value = NULL, $bool = TRUE)                                       { $this->filters['source_id']                                         = array($value, $bool); }
  public function filterAssetId($value = NULL, $bool = TRUE)                                        { $this->filters['asset_id']                                          = array($value, $bool); }
  public function filterFindingStatus($value = NULL, $bool = TRUE)                                  { $this->filters['finding_status']                                    = array($value, $bool); }
  public function filterFindingDateCreated($value = NULL, $bool = TRUE)                             { $this->filters['finding_date_created']                              = array($value, $bool); }
  public function filterFindingDateDiscovered($value = NULL, $bool = TRUE)                          { $this->filters['finding_date_discovered']                           = array($value, $bool); }
  public function filterFindingDateClosed($value = NULL, $bool = TRUE)                              { $this->filters['finding_date_closed']                               = array($value, $bool); }
  public function filterFindingData($value = NULL, $bool = TRUE)                                    { $this->filters['finding_data']                                      = array($value, $bool); }  

} // class FindingList

?>