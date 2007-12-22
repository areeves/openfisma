<?PHP

// 
// INCLUDES
// 
require_once('Database.class.php');


//
// CLASS DEFINITION
// 

class UserGroup {

	// -----------------------------------------------------------------------
	//
	// VARIABLES
	//
	// -----------------------------------------------------------------------


    private $db;

    private $user_group_id;
    private $user_id;
    private $sysgroup_id;


	// -----------------------------------------------------------------------
	// 
	// CLASS METHODS
	// 
	// -----------------------------------------------------------------------

	public function __construct($db, $user_group_id = NULL) {

		// utilize an existing database connection
		$this->db = $db;

		// get UserGroup information or create a new one if none specified
		if ($user_group_id) {
		  $this->getUserGroup($user_group_id); 
		}

	} // __construct()
	

	public function __destruct() {

		// clear out the user_group_id to prevent any updates
		$this->user_group_id = 0;

	} // __destruct()


 	public function __ToString() {
 		
 		// return a string of information
 		return	$this->db->__ToString().
 			'<pre>'.
			'<br>USER_SYSGROUPS'.
			'<br>------'.

            '<br>user_group_id                                     : '.$this->user_group_id.
            '<br>user_id                                           : '.$this->user_id.
            '<br>sysgroup_id                                       : '.$this->sysgroup_id.
			'<br></pre>';
 		
 	} // __ToString()
 	

	// -----------------------------------------------------------------------
	// 
	// CLASS MANIPULATION METHODS
	// 
	// -----------------------------------------------------------------------
	
	public function usergroupExists($user_group_id = NULL) {
		
		// make sure we have a positive, non-zero user_group_id
		if ($user_group_id) {
		
			// build our query
			$query = "SELECT `user_group_id` FROM `USER_SYSGROUPS` WHERE (`user_group_id` = '$user_group_id')";
			
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
		
	} // usergroupExists()
	

	public function getUserGroup($user_group_id = NULL) {
		
		// make sure we have a positive, non-zero user_group_id
		if ($user_group_id && $this->usergroupExists($user_group_id)) {
		
			// designate our retrieval query
			$query = "SELECT * FROM `USER_SYSGROUPS` WHERE (`user_group_id` = '$user_group_id')";
		
			// execute the query
			$this->db->query($query);
		
			// if we get a hit, store the information
			if ($this->db->num_rows() > 0) {
			
				// retrieve the results query
				$results = $this->db->fetch_assoc();
			
				// store the results locally

                $this->user_group_id                                      = $results['user_group_id'];
                $this->user_id                                            = $results['user_id'];
                $this->sysgroup_id                                        = $results['sysgroup_id'];
			
			} // this->db->fetch_assoc()
			
			// system not retrieved, clear out any potential values
			else {
			     $this->clearUserGroup(); 
			}
		} // if $user_group_id

	} // getUserGroup()


		
	public function saveUserGroup(){
	
	    if ($this->user_group_id && $this->usergroupExists($this->user_group_id)){
    	    $query = "UPDATE `USER_SYSGROUPS` SET ";    
            	    $query .= " `user_id`                                            = '$this->user_id', ";
            	    $query .= " `sysgroup_id`                                        = '$this->sysgroup_id' ";	    
                    $query .= " WHERE `user_group_id`                                = '$this->user_group_id' ";
	    }
	    else {
	       $query = "INSERT INTO `USER_SYSGROUPS` (
                            `user_id`, 
                            `sysgroup_id`
                            ) VALUES (
                            '$this->user_id', 
                            '$this->sysgroup_id'
                            )";
	    }
	    
    	// execute our query
    	$this->db->query($query);
    
    	if ($this->db->queryOK()) { 
    	   if (!$this->user_group_id || !$this->usergroupExists($this->user_group_id)){
    	       $this->user_group_id = $this->db->insert_id();
    	   }
    	   return 1; 
    	} 
    	else {
    	   return 0; 
    	}
	} //saveUserGroup()
	
	public function clearUserGroup() {
		
		// clear out (non-db) user values

        unset($this->user_group_id);
        unset($this->user_id);
        unset($this->sysgroup_id);
	} // clearUserGroup()


	public function deleteUserGroup() {

		// 
		// REMOVES USER_SYSGROUPS FROM DATABASE!
		// 

		// ensure that we have an open database connection
		if ($this->db) {

			// define our query
			$query = "DELETE FROM `USER_SYSGROUPS` WHERE (`user_group_id` = '$this->user_group_id')";

			// execute our query
			$this->db->query($query);

			// clear out the current object
			$this->clearUserGroup();
		
		} // $this->db

	} // deleteUserGroup()
	
	

	// -----------------------------------------------------------------------
	// 
	// VARIABLE ACCESS METHODS
	// 
	// -----------------------------------------------------------------------


    public function getUserGroupId()                                       { return $this->user_group_id; }
    public function getUserId()                                            { return $this->user_id; }
    public function getSysgroupId()                                        { return $this->sysgroup_id; }

	public function getValidUserGroupIds($offset = 0, $limit = NULL) {
		
		// array to store user ids
		$id_array = array();

		// create our query
		$query = "SELECT user_group_id from USER_SYSGROUPS";

		// add in our offset and limit if a limit is provided
		if ($limit) { $query .= " LIMIT $offset, $limit";  }

		// execute the query
		$this->db->query($query);
	
		// evaluate the results
		if ($this->db->queryOK()) {
			
			// push the values onto the array
			while ($id = $this->db->fetch_array()) { array_push($id_array, $id[0]); }
			
		}
		
		// return the array of user_ids
		return $id_array;
		
	} // getValidUserGroupIds
    
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
    
    

} // class UserGroup
?>
