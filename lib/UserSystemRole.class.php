<?PHP

// 
// INCLUDES
// 
require_once('Database.class.php');


//
// CLASS DEFINITION
// 

class UserSystemRole {

	// -----------------------------------------------------------------------
	//
	// VARIABLES
	//
	// -----------------------------------------------------------------------


    private $db;

    private $user_id;
    private $sysgroup_id;
    private $role_id;
    private $system_id;


	// -----------------------------------------------------------------------
	// 
	// CLASS METHODS
	// 
	// -----------------------------------------------------------------------

	public function __construct($db, $keyArray = NULL) {

		// utilize an existing database connection
		$this->db = $db;

		// get UserSystemRole information or create a new one if none specified
		if ($keyArray) {
		  $this->getUserSystemRole($keyArray); 
		}

	} // __construct()
	

	public function __destruct() {
		// clear out the keyArray to prevent any updates

		$this->user_id = null;
		$this->system_id = null;
		$this->role_id = null;
	} // __destruct()


 	public function __ToString() {
 		
 		// return a string of information
 		return	$this->db->__ToString().
 			'<pre>'.
			'<br>USER_SYSTEM_ROLES'.
			'<br>------'.

            '<br>user_id                                           : '.$this->user_id.
            '<br>sysgroup_id                                       : '.$this->sysgroup_id.
            '<br>role_id                                           : '.$this->role_id.
            '<br>system_id                                         : '.$this->system_id.
			'<br></pre>';
 		
 	} // __ToString()
 	

	// -----------------------------------------------------------------------
	// 
	// CLASS MANIPULATION METHODS
	// 
	// -----------------------------------------------------------------------
	
	public function usersystemroleExists($keyArray = NULL) {
		
		// make sure we have a positive, non-zero keyArray
		if ($keyArray) {
		
			// build our query

		$query = "SELECT * FROM `USER_SYSTEM_ROLES` WHERE `user_id`='".$keyArray['user_id']."' AND `system_id`='".$keyArray['system_id']."' AND `role_id`='".$keyArray['role_id']."' ";

			
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
		
	} // usersystemroleExists()
	

	public function getUserSystemRole($keyArray = NULL) {
		
		// make sure we have a positive, non-zero keyArray
		if ($keyArray && $this->usersystemroleExists($keyArray)) {
		
			// designate our retrieval query

			$query = "SELECT * FROM `USER_SYSTEM_ROLES` WHERE `user_id`='".$keyArray['user_id']."' AND `system_id`='".$keyArray['system_id']."' AND `role_id`='".$keyArray['role_id']."' ";

		
			// execute the query
			$this->db->query($query);
		
			// if we get a hit, store the information
			if ($this->db->num_rows() > 0) {
			
				// retrieve the results query
				$results = $this->db->fetch_assoc();
			
				// store the results locally

                $this->user_id                                            = $results['user_id'];
                $this->sysgroup_id                                        = $results['sysgroup_id'];
                $this->role_id                                            = $results['role_id'];
                $this->system_id                                          = $results['system_id'];
			
			} // this->db->fetch_assoc()
			
			// system not retrieved, clear out any potential values
			else {
			     $this->clearUserSystemRole(); 
			}
		} // if $keyArray

	} // getUserSystemRole()


		
	public function saveUserSystemRole($keyArray = NULL){
	
	    if ($keyArray && $this->usersystemroleExists($keyArray)){
    	    $query = "UPDATE `USER_SYSTEM_ROLES` SET ";    
            	    $query .= " `user_id`                                            = '$this->user_id', ";
            	    $query .= " `sysgroup_id`                                        = '$this->sysgroup_id', ";
            	    $query .= " `role_id`                                            = '$this->role_id', ";
            	    $query .= " `system_id`                                          = '$this->system_id' ";	    
                    $query .= " WHERE `user_id`='".$keyArray['user_id']."' AND `system_id`='".$keyArray['system_id']."' AND `role_id`='".$keyArray['role_id']."' ";
	    }
	    else {
	       $query = "INSERT INTO `USER_SYSTEM_ROLES` (
                            `user_id`, 
                            `sysgroup_id`, 
                            `role_id`, 
                            `system_id`
                            ) VALUES (
                            '$this->user_id', 
                            '$this->sysgroup_id', 
                            '$this->role_id', 
                            '$this->system_id'
                            )";
	    }
	    
    	// execute our query
    	$this->db->query($query);
    
    	if ($this->db->queryOK()) { 
    	   if (!$keyArray || !$this->usersystemroleExists($keyArray)){
    	       $this->keyArray = $this->db->insert_id();
    	   }
    	   return 1; 
    	} 
    	else {
    	   return 0; 
    	}
	} //saveUserSystemRole()
	
	public function clearUserSystemRole() {
		
		// clear out (non-db) user values

        unset($this->user_id);
        unset($this->sysgroup_id);
        unset($this->role_id);
        unset($this->system_id);
	} // clearUserSystemRole()


	public function deleteUserSystemRole($keyArray = NULL) {

		// 
		// REMOVES USER_SYSTEM_ROLES FROM DATABASE!
		// 

		// ensure that we have an open database connection
		if ($this->db) {

			// define our query
			
			$query = "DELETE FROM `USER_SYSTEM_ROLES` WHERE `user_id`='".$keyArray['user_id']."' AND `system_id`='".$keyArray['system_id']."' AND `role_id`='".$keyArray['role_id']."' ";
			
			// execute our query
			$this->db->query($query);

			// clear out the current object
			$this->clearUserSystemRole();
		
		} // $this->db

	} // deleteUserSystemRole()
	
	

	// -----------------------------------------------------------------------
	// 
	// VARIABLE ACCESS METHODS
	// 
	// -----------------------------------------------------------------------


    public function getUserId()                                            { return $this->user_id; }
    public function getSysgroupId()                                        { return $this->sysgroup_id; }
    public function getRoleId()                                            { return $this->role_id; }
    public function getSystemId()                                          { return $this->system_id; }

	public function getValidUserIds($offset = 0, $limit = NULL) {
		
		// array to store UserIds
		$id_array = array();

		// create our query
		$query = "SELECT `user_id` FROM `USER_SYSTEM_ROLES`";

		// add in our offset and limit if a limit is provided
		if ($limit) { $query .= " LIMIT $offset, $limit";  }

		// execute the query
		$this->db->query($query);
	
		// evaluate the results
		if ($this->db->queryOK()) {
			
			// push the values onto the array
			while ($id = $this->db->fetch_array()) { array_push($id_array, $id[0]); }
			
		}
		
		// return the array of UserIds
		return $id_array;
		
	} // getValidUserIds
	


	public function getValidSysgroupIds($offset = 0, $limit = NULL) {
		
		// array to store SysgroupIds
		$id_array = array();

		// create our query
		$query = "SELECT `sysgroup_id` FROM `USER_SYSTEM_ROLES`";

		// add in our offset and limit if a limit is provided
		if ($limit) { $query .= " LIMIT $offset, $limit";  }

		// execute the query
		$this->db->query($query);
	
		// evaluate the results
		if ($this->db->queryOK()) {
			
			// push the values onto the array
			while ($id = $this->db->fetch_array()) { array_push($id_array, $id[0]); }
			
		}
		
		// return the array of SysgroupIds
		return $id_array;
		
	} // getValidSysgroupIds
	


	public function getValidRoleIds($offset = 0, $limit = NULL) {
		
		// array to store RoleIds
		$id_array = array();

		// create our query
		$query = "SELECT `role_id` FROM `USER_SYSTEM_ROLES`";

		// add in our offset and limit if a limit is provided
		if ($limit) { $query .= " LIMIT $offset, $limit";  }

		// execute the query
		$this->db->query($query);
	
		// evaluate the results
		if ($this->db->queryOK()) {
			
			// push the values onto the array
			while ($id = $this->db->fetch_array()) { array_push($id_array, $id[0]); }
			
		}
		
		// return the array of RoleIds
		return $id_array;
		
	} // getValidRoleIds
	


	public function getValidSystemIds($offset = 0, $limit = NULL) {
		
		// array to store SystemIds
		$id_array = array();

		// create our query
		$query = "SELECT `system_id` FROM `USER_SYSTEM_ROLES`";

		// add in our offset and limit if a limit is provided
		if ($limit) { $query .= " LIMIT $offset, $limit";  }

		// execute the query
		$this->db->query($query);
	
		// evaluate the results
		if ($this->db->queryOK()) {
			
			// push the values onto the array
			while ($id = $this->db->fetch_array()) { array_push($id_array, $id[0]); }
			
		}
		
		// return the array of SystemIds
		return $id_array;
		
	} // getValidSystemIds
	

	// -----------------------------------------------------------------------
	// 
	// VARIABLE MODIFY METHODS
	// 
	// -----------------------------------------------------------------------


    public function setUserId($user_id  =  NULL){ 
		// error check input (by schema)
		if (strlen($user_id) <= 10){
            $this->user_id = $user_id;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setUserId()
    
    
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
    
    
    public function setSystemId($system_id  =  NULL){ 
		// error check input (by schema)
		if (strlen($system_id) <= 10){
            $this->system_id = $system_id;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setSystemId()
    
    

} // class UserSystemRole
?>
