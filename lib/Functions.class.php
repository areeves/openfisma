<?PHP

// 
// INCLUDES
// 
require_once('Database.class.php');


//
// CLASS DEFINITION
// 

class Functions {

	// -----------------------------------------------------------------------
	//
	// VARIABLES
	//
	// -----------------------------------------------------------------------


    private $db;

    private $function_id;
    private $function_name;
    private $function_screen;
    private $function_action;
    private $function_desc;
    private $function_open;


	// -----------------------------------------------------------------------
	// 
	// CLASS METHODS
	// 
	// -----------------------------------------------------------------------

	public function __construct($db, $function_id = NULL) {

		// utilize an existing database connection
		$this->db = $db;

		// get Functions information or create a new one if none specified
		if ($function_id) {
		  $this->getFunction($function_id); 
		}

	} // __construct()
	

	public function __destruct() {

		// clear out the function_id to prevent any updates
		$this->function_id = 0;

	} // __destruct()


 	public function __ToString() {
 		
 		// return a string of information
 		return	$this->db->__ToString().
 			'<pre>'.
			'<br>FUNCTIONS'.
			'<br>------'.

            '<br>function_id                                       : '.$this->function_id.
            '<br>function_name                                     : '.$this->function_name.
            '<br>function_screen                                   : '.$this->function_screen.
            '<br>function_action                                   : '.$this->function_action.
            '<br>function_desc                                     : '.$this->function_desc.
            '<br>function_open                                     : '.$this->function_open.
			'<br></pre>';
 		
 	} // __ToString()
 	

	// -----------------------------------------------------------------------
	// 
	// CLASS MANIPULATION METHODS
	// 
	// -----------------------------------------------------------------------
	
	public function functionExists($function_id = NULL) {
		
		// make sure we have a positive, non-zero function_id
		if ($function_id) {
		
			// build our query
			$query = "SELECT `function_id` FROM `FUNCTIONS` WHERE (`function_id` = '$function_id')";
			
			// execute the query
			$this->db->query($query);
			
			// check for results
			if ( $this->db->queryOK() && $this->db->num_rows() ) {
			     return 1; 
			} 
			else {
			     return 0; 
			}
		}
		
		// otherwise don't even bother checking it
		else { 
		     return 0; 
		}
		
	} // functionExists()
	

	public function getFunction($function_id = NULL) {
		
		// make sure we have a positive, non-zero function_id
		if ($function_id && $this->functionExists($function_id)) {
		
			// designate our retrieval query
			$query = "SELECT * FROM `FUNCTIONS` WHERE (`function_id` = '$function_id')";
		
			// execute the query
			$this->db->query($query);
		
			// if we get a hit, store the information
			if ($this->db->num_rows() > 0) {
			
				// retrieve the results query
				$results = $this->db->fetch_assoc();
			
				// store the results locally

                $this->function_id                                        = $results['function_id'];
                $this->function_name                                      = $results['function_name'];
                $this->function_screen                                    = $results['function_screen'];
                $this->function_action                                    = $results['function_action'];
                $this->function_desc                                      = $results['function_desc'];
                $this->function_open                                      = $results['function_open'];
			
			} // this->db->fetch_assoc()
			
			// system not retrieved, clear out any potential values
			else {
			     $this->clearFunction(); 
			}
		} // if $function_id

	} // getFunction()


		
	public function saveFunction(){
	
	    if ($this->function_id && $this->functionExists($this->function_id)){
    	    $query = "UPDATE `FUNCTIONS` SET ";    
            	    $query .= " `function_name`                                      = '$this->function_name', ";
            	    $query .= " `function_screen`                                    = '$this->function_screen', ";
            	    $query .= " `function_action`                                    = '$this->function_action', ";
            	    $query .= " `function_desc`                                      = '$this->function_desc', ";
            	    $query .= " `function_open`                                      = '$this->function_open' ";	    
                    $query .= " WHERE `function_id`                                  = '$this->function_id' ";
	    }
	    else {
	       $query = "INSERT INTO `FUNCTIONS` (
                            `function_name`, 
                            `function_screen`, 
                            `function_action`, 
                            `function_desc`, 
                            `function_open`
                            ) VALUES (
                            '$this->function_name', 
                            '$this->function_screen', 
                            '$this->function_action', 
                            '$this->function_desc', 
                            '$this->function_open'
                            )";
	    }
	    
    	// execute our query
    	$this->db->query($query);
    
    	if ($this->db->queryOK()) { 
    	   if (!$this->function_id || !$this->functionExists($this->function_id)){
    	       $this->function_id = $this->db->insert_id();
    	   }
    	   return 1; 
    	} 
    	else {
    	   return 0; 
    	}
	} //saveFunction()
	
	public function clearFunction() {
		
		// clear out (non-db) user values

        unset($this->function_id);
        unset($this->function_name);
        unset($this->function_screen);
        unset($this->function_action);
        unset($this->function_desc);
        unset($this->function_open);
	} // clearFunction()


	public function deleteFunction() {

		// 
		// REMOVES FUNCTIONS FROM DATABASE!
		// 

		// ensure that we have an open database connection
		if ($this->db) {

			// define our query
			$query = "DELETE FROM `FUNCTIONS` WHERE (`function_id` = '$this->function_id')";

			// execute our query
			$this->db->query($query);

			// clear out the current object
			$this->clearFunction();
		
		} // $this->db

	} // deleteFunction()
	
	

	// -----------------------------------------------------------------------
	// 
	// VARIABLE ACCESS METHODS
	// 
	// -----------------------------------------------------------------------


    public function getFunctionId()                                        { return $this->function_id; }
    public function getFunctionName()                                      { return $this->function_name; }
    public function getFunctionScreen()                                    { return $this->function_screen; }
    public function getFunctionAction()                                    { return $this->function_action; }
    public function getFunctionDesc()                                      { return $this->function_desc; }
    public function getFunctionOpen()                                      { return $this->function_open; }

	public function getValidFunctionIds($offset = 0, $limit = NULL) {
		
		// array to store function_ids
		$id_array = array();

		// create our query
		$query = "SELECT `function_id` FROM `FUNCTIONS`";

		// add in our offset and limit if a limit is provided
		if ($limit) { $query .= " LIMIT $offset, $limit";  }

		// execute the query
		$this->db->query($query);
	
		// evaluate the results
		if ($this->db->queryOK()) {
			
			// push the values onto the array
			while ($id = $this->db->fetch_array()) { array_push($id_array, $id[0]); }
			
		}
		
		// return the array of function_ids
		return $id_array;
		
	} // getValidFunctionIds
    
	public function getValidFunctionNames($offset = 0, $limit = NULL) {
		
		// array to store function_names
		$name_array = array();

		// create our query
		$query = "SELECT `function_name` FROM `FUNCTIONS`";

		// add in our offset and limit if a limit is provided
		if ($limit) { $query .= " LIMIT $offset, $limit";  }

		// execute the query
		$this->db->query($query);
	
		// evaluate the results
		if ($this->db->queryOK()) {
			
			// push the values onto the array
			while ($name = $this->db->fetch_array()) { array_push($name_array, $name[0]); }
			
		}
		
		// return the array of function_names
		return $name_array;
		
	} // getValidFunctionNames
	
	// -----------------------------------------------------------------------
	// 
	// VARIABLE MODIFY METHODS
	// 
	// -----------------------------------------------------------------------


    public function setFunctionName($function_name  =  NULL){ 
		// error check input (by schema)
		if (strlen($function_name) <= 64){
            $this->function_name = $function_name;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setFunctionName()
    
    
    public function setFunctionScreen($function_screen  =  NULL){ 
		// error check input (by schema)
		if (strlen($function_screen) <= 64){
            $this->function_screen = $function_screen;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setFunctionScreen()
    
    
    public function setFunctionAction($function_action  =  NULL){ 
		// error check input (by schema)
		if (strlen($function_action) <= 64){
            $this->function_action = $function_action;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setFunctionAction()
    
    
    public function setFunctionDesc($function_desc  =  NULL){ 
		// error check input (by schema)
		if (strlen($function_desc) >= 0){
            $this->function_desc = $function_desc;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setFunctionDesc()
    
    
    public function setFunctionOpen($function_open  =  NULL){ 
		// error check input (by schema)
		if (strlen($function_open) <= 1){
            $this->function_open = $function_open;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setFunctionOpen()
    
    

} // class Functions
?>
