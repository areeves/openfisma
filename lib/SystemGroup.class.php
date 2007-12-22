<?PHP

// 
// INCLUDES
// 
require_once('Database.class.php');


//
// CLASS DEFINITION
// 

class SystemGroup {

	// -----------------------------------------------------------------------
	//
	// VARIABLES
	//
	// -----------------------------------------------------------------------


    private $db;

    private $sysgroup_id;
    private $sysgroup_name;
    private $sysgroup_nickname;
    private $sysgroup_is_identity;


	// -----------------------------------------------------------------------
	// 
	// CLASS METHODS
	// 
	// -----------------------------------------------------------------------

	public function __construct($db, $sysgroup_id = NULL) {

		// utilize an existing database connection
		$this->db = $db;

		// get SystemGroup information or create a new one if none specified
		if ($sysgroup_id) {
		  $this->getSystemGroup($sysgroup_id); 
		}

	} // __construct()
	

	public function __destruct() {

		// clear out the sysgroup_id to prevent any updates
		$this->sysgroup_id = 0;

	} // __destruct()


 	public function __ToString() {
 		
 		// return a string of information
 		return	$this->db->__ToString().
 			'<pre>'.
			'<br>SYSTEM_GROUPS'.
			'<br>------'.

            '<br>sysgroup_id                                       : '.$this->sysgroup_id.
            '<br>sysgroup_name                                     : '.$this->sysgroup_name.
            '<br>sysgroup_nickname                                 : '.$this->sysgroup_nickname.
            '<br>sysgroup_is_identity                              : '.$this->sysgroup_is_identity.
			'<br></pre>';
 		
 	} // __ToString()
 	

	// -----------------------------------------------------------------------
	// 
	// CLASS MANIPULATION METHODS
	// 
	// -----------------------------------------------------------------------
	
	public function systemgroupExists($sysgroup_id = NULL) {
		
		// make sure we have a positive, non-zero sysgroup_id
		if ($sysgroup_id) {
		
			// build our query
			$query = "SELECT `sysgroup_id` FROM `SYSTEM_GROUPS` WHERE (`sysgroup_id` = '$sysgroup_id')";
			
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
		
	} // systemgroupExists()
	

	public function getSystemGroup($sysgroup_id = NULL) {
		
		// make sure we have a positive, non-zero sysgroup_id
		if ($sysgroup_id && $this->systemgroupExists($sysgroup_id)) {
		
			// designate our retrieval query
			$query = "SELECT * FROM `SYSTEM_GROUPS` WHERE (`sysgroup_id` = '$sysgroup_id')";
		
			// execute the query
			$this->db->query($query);
		
			// if we get a hit, store the information
			if ($this->db->num_rows() > 0) {
			
				// retrieve the results query
				$results = $this->db->fetch_assoc();
			
				// store the results locally

                $this->sysgroup_id                                        = $results['sysgroup_id'];
                $this->sysgroup_name                                      = $results['sysgroup_name'];
                $this->sysgroup_nickname                                  = $results['sysgroup_nickname'];
                $this->sysgroup_is_identity                               = $results['sysgroup_is_identity'];
			
			} // this->db->fetch_assoc()
			
			// system not retrieved, clear out any potential values
			else {
			     $this->clearSystemGroup(); 
			}
		} // if $sysgroup_id

	} // getSystemGroup()


		
	public function saveSystemGroup(){
	
	    if ($this->sysgroup_id && $this->systemgroupExists($this->sysgroup_id)){
    	    $query = "UPDATE `SYSTEM_GROUPS` SET ";    
            	    $query .= " `sysgroup_name`                                      = '$this->sysgroup_name', ";
            	    $query .= " `sysgroup_nickname`                                  = '$this->sysgroup_nickname', ";
            	    $query .= " `sysgroup_is_identity`                               = '$this->sysgroup_is_identity' ";	    
                    $query .= " WHERE `sysgroup_id`                                  = '$this->sysgroup_id' ";
	    }
	    else {
	       $query = "INSERT INTO `SYSTEM_GROUPS` (
                            `sysgroup_name`, 
                            `sysgroup_nickname`, 
                            `sysgroup_is_identity`
                            ) VALUES (
                            '$this->sysgroup_name', 
                            '$this->sysgroup_nickname', 
                            '$this->sysgroup_is_identity'
                            )";
	    }
	    
    	// execute our query
    	$this->db->query($query);
    
    	if ($this->db->queryOK()) { 
    	   if (!$this->sysgroup_id || !$this->systemgroupExists($this->sysgroup_id)){
    	       $this->sysgroup_id = $this->db->insert_id();
    	   }
    	   return 1; 
    	} 
    	else {
    	   return 0; 
    	}
	} //saveSystemGroup()
	
	public function clearSystemGroup() {
		
		// clear out (non-db) user values

        unset($this->sysgroup_id);
        unset($this->sysgroup_name);
        unset($this->sysgroup_nickname);
        unset($this->sysgroup_is_identity);
	} // clearSystemGroup()


	public function deleteSystemGroup() {

		// 
		// REMOVES SYSTEM_GROUPS FROM DATABASE!
		// 

		// ensure that we have an open database connection
		if ($this->db) {

			// define our query
			$query = "DELETE FROM `SYSTEM_GROUPS` WHERE (`sysgroup_id` = '$this->sysgroup_id')";

			// execute our query
			$this->db->query($query);

			// clear out the current object
			$this->clearSystemGroup();
		
		} // $this->db

	} // deleteSystemGroup()
	
	

	// -----------------------------------------------------------------------
	// 
	// VARIABLE ACCESS METHODS
	// 
	// -----------------------------------------------------------------------


    public function getSysgroupId()                                        { return $this->sysgroup_id; }
    public function getSysgroupName()                                      { return $this->sysgroup_name; }
    public function getSysgroupNickname()                                  { return $this->sysgroup_nickname; }
    public function getSysgroupIsIdentity()                                { return $this->sysgroup_is_identity; }

	public function getValidSystemGroupIds($offset = 0, $limit = NULL) {
		
		// array to store system_ids
		$id_array = array();

		// create our query
		$query = "SELECT `sysgroup_id` FROM `SYSTEM_GROUPS`";

		// add in our offset and limit if a limit is provided
		if ($limit) { $query .= " LIMIT $offset, $limit";  }

		// execute the query
		$this->db->query($query);
	
		// evaluate the results
		if ($this->db->queryOK()) {
			
			// push the values onto the array
			while ($id = $this->db->fetch_array()) { array_push($id_array, $id[0]); }
			
		}
		
		// return the array of system_ids
		return $id_array;
		
	} // getValidSystemGroupIds
	
	public function getValidSystemGroupNames($offset = 0, $limit = NULL) {
		
		// array to store system_ids
		$id_array = array();

		// create our query
		$query = "SELECT `sysgroup_nickname` FROM `SYSTEM_GROUPS`";

		// add in our offset and limit if a limit is provided
		if ($limit) { $query .= " LIMIT $offset, $limit";  }

		// execute the query
		$this->db->query($query);
	
		// evaluate the results
		if ($this->db->queryOK()) {
			
			// push the values onto the array
			while ($id = $this->db->fetch_array()) { array_push($id_array, $id[0]); }
			
		}
		
		// return the array of system_ids
		return $id_array;
		
	} // getValidSystemGroupNames
    
	// -----------------------------------------------------------------------
	// 
	// VARIABLE MODIFY METHODS
	// 
	// -----------------------------------------------------------------------


    public function setSysgroupName($sysgroup_name  =  NULL){ 
		// error check input (by schema)
		if (strlen($sysgroup_name) <= 64){
            $this->sysgroup_name = $sysgroup_name;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setSysgroupName()
    
    
    public function setSysgroupNickname($sysgroup_nickname  =  NULL){ 
		// error check input (by schema)
		if (strlen($sysgroup_nickname) <= 8){
            $this->sysgroup_nickname = $sysgroup_nickname;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setSysgroupNickname()
    
    
    public function setSysgroupIsIdentity($sysgroup_is_identity  =  NULL){ 
		// error check input (by schema)
		if (strlen($sysgroup_is_identity) <= 1){
            $this->sysgroup_is_identity = $sysgroup_is_identity;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setSysgroupIsIdentity()
    
    

} // class SystemGroup
?>
