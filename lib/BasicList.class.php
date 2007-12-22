<?PHP
/**
 * @file BasicList.class.php
 * 
 * @defgroup Libraries
 * 
 */


/**
 * @class BasicList
 * @ingroup Libraries
 * @brief BasicList is a class to provide a generic list interface for 
 * database tables
 * 
 * The BasicList abstract class provides an interface from which to build 
 * row listing classes for database tables within the OVMS application by
 * implementing common listing functionality. All list classes should be
 * an extension of this class.
 *
 */
abstract class BasicList {


  // -----------------------------------------------------------------------
  // 
  // VARIABLES
  // 
  // -----------------------------------------------------------------------  

  /// db database connection
  protected $db;

  /// var table table to query
  protected $table;

  /// parameter and filter arrays
  protected $params;
  protected $filters;

  /// sorting options
  protected $order;

  /// key list key
  protected $key;

  // -----------------------------------------------------------------------
  // 
  // CLASS METHODS
  // 
  // -----------------------------------------------------------------------  
	
  /** 
   * @fn __construct()
   * @brief BasicList class constructor
   * @param db database connection (instance of class Database)
   * @param table database table we are querying
   */
  public function __construct($db = NULL, $table = NULL) {

	// grab our database connector and table
	$this->db    = $db;
	$this->table = $table;

	// use reset to initialize
	$this->reset();

  } // __construct()

  /**
   * @fn __destruct()
   * @brief BasicList class destructor
   */
  public function __destruct() {}

  /**
   * @fn __ToString()
   * @brief returns BasicList as a string (not yet implemented)
   * @return string representation of BasicList
   */
  public function __ToString() {}

  /**
   * @fn reset()
   * @brief method to reset the internal variables
   */
  public function reset() {

	// initialize our parameter and filter arrays
	$this->params  = array();
	$this->filters = array();
	
	// sorting options
	$this->order = NULL;

	// reset the key
	$this->resetKey();

  } // reset()

  /**
   * @fn resetKey()
   * @brief method used to reset the @var key for keylists
   */
  public function resetKey() {

	// reset the key
	$this->key = NULL;

  } // resetKey()


  // -----------------------------------------------------------------------
  // 
  // QUERY 
  // 
  // -----------------------------------------------------------------------  

  private function array_list($array = NULL) {

	// initialize our list
	return ("'" . join("','", $array) . "'");

  } // array_list()

  private function buildQuery($distinct = FALSE, $counter = FALSE, $offset = 0, $limit = NULL) {
	
	// work with local copies
	$params  = $this->params;
	$filters = $this->filters;
	
	// open query
	$query  = "SELECT ";
	
	if ($distinct) { $query .= "DISTINCT "; }
	
	// parameter string
	$query_params = "";
	
	// loop through parameter array
	while ($param = array_pop($params)) { $query_params .= "$param, "; }

	// trim the last comma and space (if necessary)
	if (strlen($query_params) > 0) { $query_params = substr($query_params, 0, strlen($query_params) - 2); }
	
	// apply parameters
	if ($counter) { $query .= "count(*) as count "; } else { $query  .= $query_params; }
	
	// apply table
	$query  .= " FROM ".$this->table." ";
	
	// filter string
	$query_filter  = "";
	$query_columns = array_keys($filters);
	
	// for readability
	$value    = 0;
	$polarity = 1;
	
	// loop through the keys and add the filters
	while ($column = array_pop($query_columns)) { 

	  // we were given a list, is IN ()
	  if (is_array($filters[$column][$value])) {

		// polarity values - list only supports (NOT) IN
		if ($filters[$column][$polarity] == TRUE)  { $query_filter .= $column." IN (".$this->array_list($filters[$column][$value]).")"; }
		if ($filters[$column][$polarity] == FALSE) { $query_filter .= $column." NOT IN (".$this->array_list($filters[$column][$value]).")"; }		
		
	  }

	  // we were given a single item
	  else {

		// polarity values is BOOLEAN
		if (is_bool($filters[$column][$polarity])) {
			
			// polarity values (N)EQ
			if ($filters[$column][$polarity] == TRUE)  { $query_filter .= $column."  = '".$filters[$column][$value]."'"; }
			if ($filters[$column][$polarity] == FALSE) { $query_filter .= $column." != '".$filters[$column][$value]."'"; }
			
		}
		
		// non-boolean polarity modifier
		else {
		
			// polarity values GT(E) / LT(E)
			if ($filters[$column][$polarity] == '>')   { $query_filter .= $column." >  '".$filters[$column][$value]."'"; }
			if ($filters[$column][$polarity] == '>=')  { $query_filter .= $column." >= '".$filters[$column][$value]."'"; }
			if ($filters[$column][$polarity] == '<')   { $query_filter .= $column." <  '".$filters[$column][$value]."'"; }
			if ($filters[$column][$polarity] == '<=')  { $query_filter .= $column." <= '".$filters[$column][$value]."'"; }
		}

	  }
	  
	  // AND the filters
	  $query_filter .= ' AND ';
	  
	} // filters
	
	// remove final AND and put it in parentheses
	if (strlen($query_filter) > 0) { 
	  
	  $query_filter = substr($query_filter, 0, strlen($query_filter) - 5);
	  $query_filter = 'WHERE ('.$query_filter.')';
	  
	}

	// apply filters
	$query  .= $query_filter;
	
	// apply ordering
	if ($this->order) { $query .= " ORDER BY ".$this->order; }

	// apply limits
	if (!$counter && $limit) { $query  .= " LIMIT $offset, $limit"; }
	
	// return the results
	return $query;
	
  } // buildQuery()
  
  public function getQuery() { return $this->buildQuery(FALSE, FALSE); }
  
  public function getUniques() {
	
	// work with a local param copy
	$query_params = $this->params;
	
	// create our associative array of unique items
	$results = array();
	
	// loop through the parameters
	while ($param = array_pop($query_params)) { 

	  // create the query
	  $query = $this->buildQuery(TRUE, FALSE);

	  // execute the query
	  $this->db->query($query);
		
		// set up our array
		$results[$param] = array();

		// add the results to our array
		while ($row = $this->db->fetch_array()) { array_push($results[$param], $row[0]); }
		
	} // while $query_params

	// return associative array of arrays
	return $results;

  } // getUniques()


  public function getList($offset = 0, $limit = NULL) {

	// create an array to catch the list
	$list = array();
	  
	// execute the query
	$this->db->query($this->buildQuery(FALSE, FALSE, $offset, $limit));

	// grab all of the results
	while ($row = $this->db->fetch_assoc()) { array_push($list, $row); }
	  
	// return the results
	return $list;

  } // getList()

  
  public function getListSize() { 

	// execute the count query
	$this->db->query($this->buildQuery(FALSE, TRUE));

	// retrieve the result
	$result = $this->db->fetch_assoc();

	// return the resuls
	return $result['count'];

  } // getListSize()


  public function getKeyList() {

	// retrieve the row list
	$row_list = $this->getList(0, NULL);

	// initialize the key list
	$key_list = Array();

	// loop through rows and build the key list
	while ($row = array_shift($row_list)) {

		// return an array of values
		if (count($this->params) == 2) {

			$key = '';
			$val = '';

	 		// loop through the columns 
  			while ( list($row_key, $row_val) = each($row) ) {

				if ($row_key == $this->key) { $key = $row_val; }
				else { $val = $row_val; }
			
  			}
  			
  			$key_list[$key] = $val;
			
		}
		
		// return a key=>(val1,val2,valn) assoc array of values
		else {

			// initialize a new array for the retrieved key value
			$cols = Array();

	 		// loop through the columns and add it to the $key row
  			while ( list($row_key, $row_val) = each($row) ) {

				if ($row_key != $this->key) { $cols[$row_key] = $row_val; }
			
  			}

	  		$key_list[$row[$this->key]] = $cols;
	  		
		}


	} // while $row

	// return the list
	return $key_list;

  } // getKeyList()


  // -----------------------------------------------------------------------
  // 
  // ORDER
  // 
  // -----------------------------------------------------------------------

  public function setOrder($order = NULL) { $this->order = $order; }


} // class BasicList

?>