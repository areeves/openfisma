<?PHP

// 
// INCLUDES
// 
require_once('Database.class.php');


//
// CLASS DEFINITION
// 

class Role {

	// -----------------------------------------------------------------------
	//
	// VARIABLES
	//
	// -----------------------------------------------------------------------


    private $db;

    private $role_id;
    private $role_name;
    private $role_nickname;
    private $role_desc;


	// -----------------------------------------------------------------------
	// 
	// CLASS METHODS
	// 
	// -----------------------------------------------------------------------

	public function __construct($db, $role_id = NULL) {

		// utilize an existing database connection
		$this->db = $db;

		// get Role information or create a new one if none specified
		if ($role_id) {
		  $this->getRole($role_id); 
		}

	} // __construct()
	

	public function __destruct() {

		// clear out the role_id to prevent any updates
		$this->role_id = 0;

	} // __destruct()


 	public function __ToString() {
 		
 		// return a string of information
 		return	$this->db->__ToString().
 			'<pre>'.
			'<br>ROLES'.
			'<br>------'.

            '<br>role_id                                           : '.$this->role_id.
            '<br>role_name                                         : '.$this->role_name.
            '<br>role_nickname                                     : '.$this->role_nickname.
            '<br>role_desc                                         : '.$this->role_desc.
			'<br></pre>';
 		
 	} // __ToString()
 	

	// -----------------------------------------------------------------------
	// 
	// CLASS MANIPULATION METHODS
	// 
	// -----------------------------------------------------------------------
	
	public function roleExists($role_id = NULL) {
		
		// make sure we have a positive, non-zero role_id
		if ($role_id) {
		
			// build our query
			$query = "SELECT `role_id` FROM `ROLES` WHERE (`role_id` = '$role_id')";
			
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
		
	} // roleExists()
	

	public function getRole($role_id = NULL) {
		
		// make sure we have a positive, non-zero role_id
		if ($role_id && $this->roleExists($role_id)) {
		
			// designate our retrieval query
			$query = "SELECT * FROM `ROLES` WHERE (`role_id` = '$role_id')";
		
			// execute the query
			$this->db->query($query);
		
			// if we get a hit, store the information
			if ($this->db->num_rows() > 0) {
			
				// retrieve the results query
				$results = $this->db->fetch_assoc();
			
				// store the results locally

                $this->role_id                                            = $results['role_id'];
                $this->role_name                                          = $results['role_name'];
                $this->role_nickname                                      = $results['role_nickname'];
                $this->role_desc                                          = $results['role_desc'];
			
			} // this->db->fetch_assoc()
			
			// system not retrieved, clear out any potential values
			else {
			     $this->clearRole(); 
			}
		} // if $role_id

	} // getRole()


		
	public function saveRole(){
	
	    if ($this->role_id && $this->roleExists($this->role_id)){
    	    $query = "UPDATE `ROLES` SET ";    
            	    $query .= " `role_name`                                          = '$this->role_name', ";
            	    $query .= " `role_nickname`                                      = '$this->role_nickname', ";
            	    $query .= " `role_desc`                                          = '$this->role_desc' ";	    
                    $query .= " WHERE `role_id`                                      = '$this->role_id' ";
	    }
	    else {
	       $query = "INSERT INTO `ROLES` (
                            `role_name`, 
                            `role_nickname`, 
                            `role_desc`
                            ) VALUES (
                            '$this->role_name', 
                            '$this->role_nickname', 
                            '$this->role_desc'
                            )";
	    }
	    
    	// execute our query
    	$this->db->query($query);
    
    	if ($this->db->queryOK()) { 
    	   if (!$this->role_id || !$this->roleExists($this->role_id)){
    	       $this->role_id = $this->db->insert_id();
    	   }
    	   return 1; 
    	} 
    	else {
    	   return 0; 
    	}
	} //saveRole()
	
	public function clearRole() {
		
		// clear out (non-db) user values

        unset($this->role_id);
        unset($this->role_name);
        unset($this->role_nickname);
        unset($this->role_desc);
	} // clearRole()


	public function deleteRole() {

		// 
		// REMOVES ROLES FROM DATABASE!
		// 

		// ensure that we have an open database connection
		if ($this->db) {

			// define our query
			$query = "DELETE FROM `ROLES` WHERE (`role_id` = '$this->role_id')";

			// execute our query
			$this->db->query($query);

			// clear out the current object
			$this->clearRole();
		
		} // $this->db

	} // deleteRole()
	
	

	// -----------------------------------------------------------------------
	// 
	// VARIABLE ACCESS METHODS
	// 
	// -----------------------------------------------------------------------


    public function getRoleId()                                            { return $this->role_id; }
    public function getRoleName()                                          { return $this->role_name; }
    public function getRoleNickname()                                      { return $this->role_nickname; }
    public function getRoleDesc()                                          { return $this->role_desc; }

	public function getValidRoleIds($offset = 0, $limit = NULL) {
		
		// array to store role_ids
		$id_array = array();

		// create our query
		$query = "SELECT `role_id` FROM `ROLES`";

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
		
	} // getValidRoleIds
	
	public function getValidRoleNames($offset = 0, $limit = NULL) {
		
		// array to store role_nicknames
		$name_array = array();

		// create our query
		$query = "SELECT `role_nickname` FROM `ROLES`";

		// add in our offset and limit if a limit is provided
		if ($limit) { $query .= " LIMIT $offset, $limit";  }

		// execute the query
		$this->db->query($query);
	
		// evaluate the results
		if ($this->db->queryOK()) {
			
			// push the values onto the array
			while ($name = $this->db->fetch_array()) { array_push($name_array, $name[0]); }
			
		}
		
		// return the array of role_nicknames
		return $name_array;
		
	} // getValidRoleNames
	
	// -----------------------------------------------------------------------
	// 
	// VARIABLE MODIFY METHODS
	// 
	// -----------------------------------------------------------------------


    public function setRoleName($role_name  =  NULL){ 
		// error check input (by schema)
		if (strlen($role_name) <= 64){
            $this->role_name = $role_name;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setRoleName()
    
    
    public function setRoleNickname($role_nickname  =  NULL){ 
		// error check input (by schema)
		if (strlen($role_nickname) <= 8){
            $this->role_nickname = $role_nickname;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setRoleNickname()
    
    
    public function setRoleDesc($role_desc  =  NULL){ 
		// error check input (by schema)
		if (strlen($role_desc) >= 0){
            $this->role_desc = $role_desc;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setRoleDesc()
    
    

} // class Role
?>
