<?PHP

// 
// INCLUDES
// 
require_once('Database.class.php');
require_once('BasicList.class.php');


//
// CLASS DEFINITION
// 

class ProductList extends BasicList {

  // -----------------------------------------------------------------------
  // 
  // CLASS METHODS
  // 
  // -----------------------------------------------------------------------
  
  public function __construct($db = NULL) { 

	// call the parent constructor with the db connection and table name
	parent::__construct($db, 'PRODUCTS'); 

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
  
  public function getProdId($isKey = FALSE)                                         { array_push($this->params, 'prod_id');                                           if ($isKey) { $this->key = 'prod_id'; } }
  public function getProdNvdDefined($isKey = FALSE)                                 { array_push($this->params, 'prod_nvd_defined');                                  if ($isKey) { $this->key = 'prod_nvd_defined'; } }
  public function getProdMeta($isKey = FALSE)                                       { array_push($this->params, 'prod_meta');                                         if ($isKey) { $this->key = 'prod_meta'; } }
  public function getProdVendor($isKey = FALSE)                                     { array_push($this->params, 'prod_vendor');                                       if ($isKey) { $this->key = 'prod_vendor'; } }
  public function getProdName($isKey = FALSE)                                       { array_push($this->params, 'prod_name');                                         if ($isKey) { $this->key = 'prod_name'; } }
  public function getProdVersion($isKey = FALSE)                                    { array_push($this->params, 'prod_version');                                      if ($isKey) { $this->key = 'prod_version'; } }
  public function getProdDesc($isKey = FALSE)                                       { array_push($this->params, 'prod_desc');                                         if ($isKey) { $this->key = 'prod_desc'; } }  

  // -----------------------------------------------------------------------
  // 
  // FILTERS
  // 
  // -----------------------------------------------------------------------
  
  public function filterProdId($value = NULL, $bool = TRUE)                                         { $this->filters['prod_id']                                           = array($value, $bool); }
  public function filterProdNvdDefined($value = NULL, $bool = TRUE)                                 { $this->filters['prod_nvd_defined']                                  = array($value, $bool); }
  public function filterProdMeta($value = NULL, $bool = TRUE)                                       { $this->filters['prod_meta']                                         = array($value, $bool); }
  public function filterProdVendor($value = NULL, $bool = TRUE)                                     { $this->filters['prod_vendor']                                       = array($value, $bool); }
  public function filterProdName($value = NULL, $bool = TRUE)                                       { $this->filters['prod_name']                                         = array($value, $bool); }
  public function filterProdVersion($value = NULL, $bool = TRUE)                                    { $this->filters['prod_version']                                      = array($value, $bool); }
  public function filterProdDesc($value = NULL, $bool = TRUE)                                       { $this->filters['prod_desc']                                         = array($value, $bool); }  

} // class ProductList

?>