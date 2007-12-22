<?PHP

// 
// INCLUDES
// 
require_once('Database.class.php');


//
// CLASS DEFINITION
// 

class RoleFunction {

	// -----------------------------------------------------------------------
	//
	// VARIABLES
	//
	// -----------------------------------------------------------------------


    private $db;

    private $role_func_id;
    private $role_id;
    private $function_id;


	// -----------------------------------------------------------------------
	// 
	// CLASS METHODS
	// 
	// -----------------------------------------------------------------------

	public function __construct($db, $role_func_id = NULL) {

		// utilize an existing database connection
		$this->db = $db;

		// get RoleFunction information or create a new one if none specified
		if ($role_func_id) {
		  $this->getRoleFunction($role_func_id); 
		}

	} // __construct()
	

	public function __destruct() {

		// clear out the role_func_id to prevent any updates
		$this->role_func_id = 0;

	} // __destruct()


 	public function __ToString() {
 		
 		// return a string of information
 		return	$this->db->__ToString().
 			'<pre>'.
			'<br>ROLE_FUNCTIONS'.
			'<br>------'.

            '<br>role_func_id                                      : '.$this->role_func_id.
            '<br>role_id                                           : '.$this->role_id.
            '<br>function_id                                       : '.$this->function_id.
			'<br></pre>';
 		
 	} // __ToString()
 	

	// -----------------------------------------------------------------------
	// 
	// CLASS MANIPULATION METHODS
	// 
	// -----------------------------------------------------------------------
	
	public function rolefunctionExists($role_func_id = NULL) {
		
		// make sure we have a positive, non-zero role_func_id
		if ($role_func_id) {
		
			// build our query
			$query = "SELECT `role_func_id` FROM `ROLE_FUNCTIONS` WHERE (`role_func_id` = '$role_func_id')";
			
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
		
	} // rolefunctionExists()
	

	public function getRoleFunction($role_func_id = NULL) {
		
		// make sure we have a positive, non-zero role_func_id
		if ($role_func_id && $this->rolefunctionExists($role_func_id)) {
		
			// designate our retrieval query
			$query = "SELECT * FROM `ROLE_FUNCTIONS` WHERE (`role_func_id` = '$role_func_id')";
		
			// execute the query
			$this->db->query($query);
		
			// if we get a hit, store the information
			if ($this->db->num_rows() > 0) {
			
				// retrieve the results query
				$results = $this->db->fetch_assoc();
			
				// store the results locally

                $this->role_func_id                                       = $results['role_func_id'];
                $this->role_id                                            = $results['role_id'];
                $this->function_id                                        = $results['function_id'];
			
			} // this->db->fetch_assoc()
			
			// system not retrieved, clear out any potential values
			else {
			     $this->clearRoleFunction(); 
			}
		} // if $role_func_id

	} // getRoleFunction()


		
	public function saveRoleFunction(){
	
	    if ($this->role_func_id && $this->rolefunctionExists($this->role_func_id)){
    	    $query = "UPDATE `ROLE_FUNCTIONS` SET ";    
            	    $query .= " `role_id`                                            = '$this->role_id', ";
            	    $query .= " `function_id`                                        = '$this->function_id' ";	    
                    $query .= " WHERE `role_func_id`                                 = '$this->role_func_id' ";
	    }
	    else {
	       $query = "INSERT INTO `ROLE_FUNCTIONS` (
                            `role_id`, 
                            `function_id`
                            ) VALUES (
                            '$this->role_id', 
                            '$this->function_id'
                            )";
	    }
	    
    	// execute our query
    	$this->db->query($query);
    
    	if ($this->db->queryOK()) { 
    	   if (!$this->role_func_id || !$this->rolefunctionExists($this->role_func_id)){
    	       $this->role_func_id = $this->db->insert_id();
    	   }
    	   return 1; 
    	} 
    	else {
    	   return 0; 
    	}
	} //saveRoleFunction()
	
	public function clearRoleFunction() {
		
		// clear out (non-db) user values

        unset($this->role_func_id);
        unset($this->role_id);
        unset($this->function_id);
	} // clearRoleFunction()


	public function deleteRoleFunction() {

		// 
		// REMOVES ROLE_FUNCTIONS FROM DATABASE!
		// 

		// ensure that we have an open database connection
		if ($this->db) {

			// define our query
			$query = "DELETE FROM `ROLE_FUNCTIONS` WHERE (`role_func_id` = '$this->role_func_id')";

			// execute our query
			$this->db->query($query);

			// clear out the current object
			$this->clearRoleFunction();
		
		} // $this->db

	} // deleteRoleFunction()
	
	

	// -----------------------------------------------------------------------
	// 
	// VARIABLE ACCESS METHODS
	// 
	// -----------------------------------------------------------------------


    public function getRoleFuncId()                                        { return $this->role_func_id; }
    public function getRoleId()                                            { return $this->role_id; }
    public function getFunctionId()                                        { return $this->function_id; }


	public function getValidRoleFunctionIds($offset = 0, $limit = NULL) {
		
		// array to store role_ids
		$id_array = array();

		// create our query
		$query = "SELECT `role_func_id` FROM `ROLE_FUNCTIONS`";

		// add in our offset and limit if a limit is provided
		if ($limit) { $query .= " LIMIT $offset, $limit";  }

		// execute the query
		$this->db->query($query);
	
		// evaluate the results
		if ($this->db->queryOK()) {
			
			// push the values onto the array
			while ($id = $this->db->fetch_array()) { array_push($id_array, $id[0]); }
			
		}
		
		// return the array of role_ids
		return $id_array;
		
	} // getValidRoleFunctionIds
    
	// -----------------------------------------------------------------------
	// 
	// VARIABLE MODIFY METHODS
	// 
	// -----------------------------------------------------------------------


    public function setRoleId($role_id  =  NULL){ 
		// error check input (by schema)
		if (strlen($role_id) <= 10){
            $this->role_id = $role_id;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setRoleId()
    
    
    public function setFunctionId($function_id  =  NULL){ 
		// error check input (by schema)
		if (strlen($function_id) <= 10){
            $this->function_id = $function_id;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setFunctionId()
    
    

} // class RoleFunction
?>
