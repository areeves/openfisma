<?PHP

// 
// INCLUDES
// 
require_once('Database.class.php');
require_once('BasicList.class.php');


//
// CLASS DEFINITION
// 

class BlscrList extends BasicList {

  // -----------------------------------------------------------------------
  // 
  // CLASS METHODS
  // 
  // -----------------------------------------------------------------------
  
  public function __construct($db = NULL) { 

	// call the parent constructor with the db connection and table name
	parent::__construct($db, 'BLSCR'); 

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
  
  public function getBlscrNumber($isKey = FALSE)                                    { array_push($this->params, 'blscr_number');                                      if ($isKey) { $this->key = 'blscr_number'; } }
  public function getBlscrClass($isKey = FALSE)                                     { array_push($this->params, 'blscr_class');                                       if ($isKey) { $this->key = 'blscr_class'; } }
  public function getBlscrSubclass($isKey = FALSE)                                  { array_push($this->params, 'blscr_subclass');                                    if ($isKey) { $this->key = 'blscr_subclass'; } }
  public function getBlscrFamily($isKey = FALSE)                                    { array_push($this->params, 'blscr_family');                                      if ($isKey) { $this->key = 'blscr_family'; } }
  public function getBlscrControl($isKey = FALSE)                                   { array_push($this->params, 'blscr_control');                                     if ($isKey) { $this->key = 'blscr_control'; } }
  public function getBlscrGuidance($isKey = FALSE)                                  { array_push($this->params, 'blscr_guidance');                                    if ($isKey) { $this->key = 'blscr_guidance'; } }
  public function getBlscrLow($isKey = FALSE)                                       { array_push($this->params, 'blscr_low');                                         if ($isKey) { $this->key = 'blscr_low'; } }
  public function getBlscrModerate($isKey = FALSE)                                  { array_push($this->params, 'blscr_moderate');                                    if ($isKey) { $this->key = 'blscr_moderate'; } }
  public function getBlscrHigh($isKey = FALSE)                                      { array_push($this->params, 'blscr_high');                                        if ($isKey) { $this->key = 'blscr_high'; } }
  public function getBlscrEnhancements($isKey = FALSE)                              { array_push($this->params, 'blscr_enhancements');                                if ($isKey) { $this->key = 'blscr_enhancements'; } }
  public function getBlscrSupplement($isKey = FALSE)                                { array_push($this->params, 'blscr_supplement');                                  if ($isKey) { $this->key = 'blscr_supplement'; } }  

  // -----------------------------------------------------------------------
  // 
  // FILTERS
  // 
  // -----------------------------------------------------------------------
  
  public function filterBlscrNumber($value = NULL, $bool = TRUE)                                    { $this->filters['blscr_number']                                      = array($value, $bool); }
  public function filterBlscrClass($value = NULL, $bool = TRUE)                                     { $this->filters['blscr_class']                                       = array($value, $bool); }
  public function filterBlscrSubclass($value = NULL, $bool = TRUE)                                  { $this->filters['blscr_subclass']                                    = array($value, $bool); }
  public function filterBlscrFamily($value = NULL, $bool = TRUE)                                    { $this->filters['blscr_family']                                      = array($value, $bool); }
  public function filterBlscrControl($value = NULL, $bool = TRUE)                                   { $this->filters['blscr_control']                                     = array($value, $bool); }
  public function filterBlscrGuidance($value = NULL, $bool = TRUE)                                  { $this->filters['blscr_guidance']                                    = array($value, $bool); }
  public function filterBlscrLow($value = NULL, $bool = TRUE)                                       { $this->filters['blscr_low']                                         = array($value, $bool); }
  public function filterBlscrModerate($value = NULL, $bool = TRUE)                                  { $this->filters['blscr_moderate']                                    = array($value, $bool); }
  public function filterBlscrHigh($value = NULL, $bool = TRUE)                                      { $this->filters['blscr_high']                                        = array($value, $bool); }
  public function filterBlscrEnhancements($value = NULL, $bool = TRUE)                              { $this->filters['blscr_enhancements']                                = array($value, $bool); }
  public function filterBlscrSupplement($value = NULL, $bool = TRUE)                                { $this->filters['blscr_supplement']                                  = array($value, $bool); }  

} // class BlscrList

?>