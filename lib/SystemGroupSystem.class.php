<?PHP

// 
// INCLUDES
// 
require_once('Database.class.php');


//
// CLASS DEFINITION
// 

class SystemGroupSystem {

	// -----------------------------------------------------------------------
	//
	// VARIABLES
	//
	// -----------------------------------------------------------------------


    private $db;

    private $sysgroup_id;
    private $system_id;


	// -----------------------------------------------------------------------
	// 
	// CLASS METHODS
	// 
	// -----------------------------------------------------------------------

	public function __construct($db, $keyArray = NULL) {

		// utilize an existing database connection
		$this->db = $db;

		// get SystemGroupSystem information or create a new one if none specified
		if ($keyArray) {
		  $this->getSystemGroupSystem($keyArray); 
		}

	} // __construct()
	

	public function __destruct() {
		// clear out the keyArray to prevent any updates

		$this->sysgroup_id = null;
		$this->system_id = null;
	} // __destruct()


 	public function __ToString() {
 		
 		// return a string of information
 		return	$this->db->__ToString().
 			'<pre>'.
			'<br>SYSTEM_GROUP_SYSTEMS'.
			'<br>------'.

            '<br>sysgroup_id                                       : '.$this->sysgroup_id.
            '<br>system_id                                         : '.$this->system_id.
			'<br></pre>';
 		
 	} // __ToString()
 	

	// -----------------------------------------------------------------------
	// 
	// CLASS MANIPULATION METHODS
	// 
	// -----------------------------------------------------------------------
	
	public function systemgroupsystemExists($keyArray = NULL) {
		
		// make sure we have a positive, non-zero keyArray
		if ($keyArray) {
		
			// build our query

		$query = "SELECT * FROM `SYSTEM_GROUP_SYSTEMS` WHERE `sysgroup_id`='".$keyArray['sysgroup_id']."' AND `system_id`='".$keyArray['system_id']."' ";

			
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
		
	} // systemgroupsystemExists()
	

	public function getSystemGroupSystem($keyArray = NULL) {
		
		// make sure we have a positive, non-zero keyArray
		if ($keyArray && $this->systemgroupsystemExists($keyArray)) {
		
			// designate our retrieval query

			$query = "SELECT * FROM `SYSTEM_GROUP_SYSTEMS` WHERE `sysgroup_id`='".$keyArray['sysgroup_id']."' AND `system_id`='".$keyArray['system_id']."' ";

		
			// execute the query
			$this->db->query($query);
		
			// if we get a hit, store the information
			if ($this->db->num_rows() > 0) {
			
				// retrieve the results query
				$results = $this->db->fetch_assoc();
			
				// store the results locally

                $this->sysgroup_id                                        = $results['sysgroup_id'];
                $this->system_id                                          = $results['system_id'];
			
			} // this->db->fetch_assoc()
			
			// system not retrieved, clear out any potential values
			else {
			     $this->clearSystemGroupSystem(); 
			}
		} // if $keyArray

	} // getSystemGroupSystem()


		
	public function saveSystemGroupSystem($keyArray = NULL){
	
	    if ($keyArray && $this->systemgroupsystemExists($keyArray)){
    	    $query = "UPDATE `SYSTEM_GROUP_SYSTEMS` SET ";    
            	    $query .= " `sysgroup_id`                                        = '$this->sysgroup_id', ";
            	    $query .= " `system_id`                                          = '$this->system_id' ";	    
                    $query .= " WHERE `sysgroup_id`='".$keyArray['sysgroup_id']."' AND `system_id`='".$keyArray['system_id']."' ";
	    }
	    else {
	       $query = "INSERT INTO `SYSTEM_GROUP_SYSTEMS` (
                            `sysgroup_id`, 
                            `system_id`
                            ) VALUES (
                            '$this->sysgroup_id', 
                            '$this->system_id'
                            )";
	    }
	    
    	// execute our query
    	$this->db->query($query);
    
    	if ($this->db->queryOK()) { 
    	   if (!$keyArray || !$this->systemgroupsystemExists($keyArray)){
    	       $this->keyArray = $this->db->insert_id();
    	   }
    	   return 1; 
    	} 
    	else {
    	   return 0; 
    	}
	} //saveSystemGroupSystem()
	
	public function clearSystemGroupSystem() {
		
		// clear out (non-db) user values

        unset($this->sysgroup_id);
        unset($this->system_id);
	} // clearSystemGroupSystem()


	public function deleteSystemGroupSystem($keyArray = NULL) {

		// 
		// REMOVES SYSTEM_GROUP_SYSTEMS FROM DATABASE!
		// 

		// ensure that we have an open database connection
		if ($this->db) {

			// define our query
			
			$query = "DELETE FROM `SYSTEM_GROUP_SYSTEMS` WHERE `sysgroup_id`='".$keyArray['sysgroup_id']."' AND `system_id`='".$keyArray['system_id']."' ";
			
			// execute our query
			$this->db->query($query);

			// clear out the current object
			$this->clearSystemGroupSystem();
		
		} // $this->db

	} // deleteSystemGroupSystem()
	
	

	// -----------------------------------------------------------------------
	// 
	// VARIABLE ACCESS METHODS
	// 
	// -----------------------------------------------------------------------


    public function getSysgroupId()                                        { return $this->sysgroup_id; }
    public function getSystemId()                                          { return $this->system_id; }

	public function getValidSysgroupIds($offset = 0, $limit = NULL) {
		
		// array to store SysgroupIds
		$id_array = array();

		// create our query
		$query = "SELECT `sysgroup_id` FROM `SYSTEM_GROUP_SYSTEMS`";

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
	


	public function getValidSystemIds($offset = 0, $limit = NULL) {
		
		// array to store SystemIds
		$id_array = array();

		// create our query
		$query = "SELECT `system_id` FROM `SYSTEM_GROUP_SYSTEMS`";

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
    
    

} // class SystemGroupSystem
?>
