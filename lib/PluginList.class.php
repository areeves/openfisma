<?PHP

// 
// INCLUDES
// 
require_once('Database.class.php');
require_once('BasicList.class.php');


//
// CLASS DEFINITION
// 

class PluginList extends BasicList {

  // -----------------------------------------------------------------------
  // 
  // CLASS METHODS
  // 
  // -----------------------------------------------------------------------
  
  public function __construct($db = NULL) { 

	// call the parent constructor with the db connection and table name
	parent::__construct($db, 'PLUGINS'); 

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
  
  public function getPluginId($isKey = FALSE)                                       { array_push($this->params, 'plugin_id');                                         if ($isKey) { $this->key = 'plugin_id'; } }
  public function getPluginName($isKey = FALSE)                                     { array_push($this->params, 'plugin_name');                                       if ($isKey) { $this->key = 'plugin_name'; } }
  public function getPluginNickname($isKey = FALSE)                                 { array_push($this->params, 'plugin_nickname');                                   if ($isKey) { $this->key = 'plugin_nickname'; } }
  public function getPluginAbbreviation($isKey = FALSE)                             { array_push($this->params, 'plugin_abbreviation');                               if ($isKey) { $this->key = 'plugin_abbreviation'; } }
  public function getPluginDesc($isKey = FALSE)                                     { array_push($this->params, 'plugin_desc');                                       if ($isKey) { $this->key = 'plugin_desc'; } }  

  // -----------------------------------------------------------------------
  // 
  // FILTERS
  // 
  // -----------------------------------------------------------------------
  
  public function filterPluginId($value = NULL, $bool = TRUE)                                       { $this->filters['plugin_id']                                         = array($value, $bool); }
  public function filterPluginName($value = NULL, $bool = TRUE)                                     { $this->filters['plugin_name']                                       = array($value, $bool); }
  public function filterPluginNickname($value = NULL, $bool = TRUE)                                 { $this->filters['plugin_nickname']                                   = array($value, $bool); }
  public function filterPluginAbbreviation($value = NULL, $bool = TRUE)                             { $this->filters['plugin_abbreviation']                               = array($value, $bool); }
  public function filterPluginDesc($value = NULL, $bool = TRUE)                                     { $this->filters['plugin_desc']                                       = array($value, $bool); }  

} // class PluginList

?>