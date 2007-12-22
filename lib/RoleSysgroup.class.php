<?PHP

// 
// INCLUDES
// 
require_once('Database.class.php');


//
// CLASS DEFINITION
// 

class RoleSysgroup {

	// -----------------------------------------------------------------------
	//
	// VARIABLES
	//
	// -----------------------------------------------------------------------


    private $db;

    private $role_group_id;
    private $role_id;
    private $sysgroup_id;


	// -----------------------------------------------------------------------
	// 
	// CLASS METHODS
	// 
	// -----------------------------------------------------------------------

	public function __construct($db, $role_group_id = NULL) {

		// utilize an existing database connection
		$this->db = $db;

		// get RoleSysgroup information or create a new one if none specified
		if ($role_group_id) {
		  $this->getRoleSysgroup($role_group_id); 
		}

	} // __construct()
	

	public function __destruct() {

		// clear out the role_group_id to prevent any updates
		$this->role_group_id = 0;

	} // __destruct()


 	public function __ToString() {
 		
 		// return a string of information
 		return	$this->db->__ToString().
 			'<pre>'.
			'<br>ROLE_SYSGROUPS'.
			'<br>------'.

            '<br>role_group_id                                     : '.$this->role_group_id.
            '<br>role_id                                           : '.$this->role_id.
            '<br>sysgroup_id                                       : '.$this->sysgroup_id.
			'<br></pre>';
 		
 	} // __ToString()
 	

	// -----------------------------------------------------------------------
	// 
	// CLASS MANIPULATION METHODS
	// 
	// -----------------------------------------------------------------------
	
	public function rolesysgroupExists($role_group_id = NULL) {
		
		// make sure we have a positive, non-zero role_group_id
		if ($role_group_id) {
		
			// build our query
			$query = "SELECT `role_group_id` FROM `ROLE_SYSGROUPS` WHERE (`role_group_id` = '$role_group_id')";
			
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
		
	} // rolesysgroupExists()
	

	public function getRoleSysgroup($role_group_id = NULL) {
		
		// make sure we have a positive, non-zero role_group_id
		if ($role_group_id && $this->rolesysgroupExists($role_group_id)) {
		
			// designate our retrieval query
			$query = "SELECT * FROM `ROLE_SYSGROUPS` WHERE (`role_group_id` = '$role_group_id')";
		
			// execute the query
			$this->db->query($query);
		
			// if we get a hit, store the information
			if ($this->db->num_rows() > 0) {
			
				// retrieve the results query
				$results = $this->db->fetch_assoc();
			
				// store the results locally

                $this->role_group_id                                      = $results['role_group_id'];
                $this->role_id                                            = $results['role_id'];
                $this->sysgroup_id                                        = $results['sysgroup_id'];
			
			} // this->db->fetch_assoc()
			
			// system not retrieved, clear out any potential values
			else {
			     $this->clearRoleSysgroup(); 
			}
		} // if $role_group_id

	} // getRoleSysgroup()


		
	public function saveRoleSysgroup(){
	
	    if ($this->role_group_id && $this->rolesysgroupExists($this->role_group_id)){
    	    $query = "UPDATE `ROLE_SYSGROUPS` SET ";    
            	    $query .= " `role_id`                                            = '$this->role_id', ";
            	    $query .= " `sysgroup_id`                                        = '$this->sysgroup_id' ";	    
                    $query .= " WHERE `role_group_id`                                = '$this->role_group_id' ";
	    }
	    else {
	       $query = "INSERT INTO `ROLE_SYSGROUPS` (
                            `role_id`, 
                            `sysgroup_id` 
                            ) VALUES (
                            '$this->role_id', 
                            '$this->sysgroup_id'
                            )";
	    }
	    
    	// execute our query
    	$this->db->query($query);
    
    	if ($this->db->queryOK()) { 
    	   if (!$this->role_group_id || !$this->rolesysgroupExists($this->role_group_id)){
    	       $this->role_group_id = $this->db->insert_id();
    	   }
    	   return 1; 
    	} 
    	else {
    	   return 0; 
    	}
	} //saveRoleSysgroup()
	
	public function clearRoleSysgroup() {
		
		// clear out (non-db) user values

        unset($this->role_group_id);
        unset($this->role_id);
        unset($this->sysgroup_id);
	} // clearRoleSysgroup()


	public function deleteRoleSysgroup() {

		// 
		// REMOVES ROLE_SYSGROUPS FROM DATABASE!
		// 

		// ensure that we have an open database connection
		if ($this->db) {

			// define our query
			$query = "DELETE FROM `ROLE_SYSGROUPS` WHERE (`role_group_id` = '$this->role_group_id')";

			// execute our query
			$this->db->query($query);

			// clear out the current object
			$this->clearRoleSysgroup();
		
		} // $this->db

	} // deleteRoleSysgroup()
	
	

	// -----------------------------------------------------------------------
	// 
	// VARIABLE ACCESS METHODS
	// 
	// -----------------------------------------------------------------------


    public function getRoleGroupId()                                       { return $this->role_group_id; }
    public function getRoleId()                                            { return $this->role_id; }
    public function getSysgroupId()                                        { return $this->sysgroup_id; }

	public function getValidRoleSysgroupIds($offset = 0, $limit = NULL) {
		
		// array to store ids
		$id_array = array();

		// create our query
		$query = "SELECT `role_group_id` FROM `ROLE_SYSGROUPS`";

		// add in our offset and limit if a limit is provided
		if ($limit) { $query .= " LIMIT $offset, $limit";  }

		// execute the query
		$this->db->query($query);
	
		// evaluate the results
		if ($this->db->queryOK()) {
			
			// push the values onto the array
			while ($id = $this->db->fetch_array()) { array_push($id_array, $id[0]); }
			
		}
		
		// return the array of ids
		return $id_array;
		
	} // getValidRoleSysgroupIds
	
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
    
    
    public function setSysgroupId($sysgroup_id  =  NULL){ 
		// error check input (by schema)
		if (strlen($sysgroup_id) <= 10){
            $this->sysgroup_id = $sysgroup_id;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setSysgroupId()
    
    

} // class RoleSysgroup
?>
