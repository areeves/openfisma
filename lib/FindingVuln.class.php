<?PHP

// 
// INCLUDES
// 
require_once('Database.class.php');


//
// CLASS DEFINITION
// 

class FindingVuln {

	// -----------------------------------------------------------------------
	//
	// VARIABLES
	//
	// -----------------------------------------------------------------------


    private $db;

    private $finding_id;
    private $vuln_seq;
    private $vuln_type;


	// -----------------------------------------------------------------------
	// 
	// CLASS METHODS
	// 
	// -----------------------------------------------------------------------

	public function __construct($db, $keyArray = NULL) {

		// utilize an existing database connection
		$this->db = $db;

		// get FindingVuln information or create a new one if none specified
		if ($keyArray) {
		  $this->getFindingVuln($keyArray); 
		}

	} // __construct()
	

	public function __destruct() {
		// clear out the keyArray to prevent any updates

		$this->vuln_type = null;
		$this->vuln_seq = null;
		$this->finding_id = null;
	} // __destruct()


 	public function __ToString() {
 		
 		// return a string of information
 		return	$this->db->__ToString().
 			'<pre>'.
			'<br>FINDING_VULNS'.
			'<br>------'.

            '<br>finding_id                                        : '.$this->finding_id.
            '<br>vuln_seq                                          : '.$this->vuln_seq.
            '<br>vuln_type                                         : '.$this->vuln_type.
			'<br></pre>';
 		
 	} // __ToString()
 	

	// -----------------------------------------------------------------------
	// 
	// CLASS MANIPULATION METHODS
	// 
	// -----------------------------------------------------------------------
	
	public function findingvulnExists($keyArray = NULL) {
		
		// make sure we have a positive, non-zero keyArray
		if ($keyArray) {
		
			// build our query

		$query = "SELECT * FROM `FINDING_VULNS` WHERE `vuln_type`='".$keyArray['vuln_type']."' AND `vuln_seq`='".$keyArray['vuln_seq']."' AND `finding_id`='".$keyArray['finding_id']."' ";

			
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
		
	} // findingvulnExists()
	

	public function getFindingVuln($keyArray = NULL) {
		
		// make sure we have a positive, non-zero keyArray
		if ($keyArray && $this->findingvulnExists($keyArray)) {
		
			// designate our retrieval query

			$query = "SELECT * FROM `FINDING_VULNS` WHERE `vuln_type`='".$keyArray['vuln_type']."' AND `vuln_seq`='".$keyArray['vuln_seq']."' AND `finding_id`='".$keyArray['finding_id']."' ";

		
			// execute the query
			$this->db->query($query);
		
			// if we get a hit, store the information
			if ($this->db->num_rows() > 0) {
			
				// retrieve the results query
				$results = $this->db->fetch_assoc();
			
				// store the results locally

                $this->finding_id                                         = $results['finding_id'];
                $this->vuln_seq                                           = $results['vuln_seq'];
                $this->vuln_type                                          = $results['vuln_type'];
			
			} // this->db->fetch_assoc()
			
			// system not retrieved, clear out any potential values
			else {
			     $this->clearFindingVuln(); 
			}
		} // if $keyArray

	} // getFindingVuln()


		
	public function saveFindingVuln($keyArray = NULL){
	
	    if ($keyArray && $this->findingvulnExists($keyArray)){
    	    $query = "UPDATE `FINDING_VULNS` SET ";    
            	    $query .= " `finding_id`                                         = '$this->finding_id', ";
            	    $query .= " `vuln_seq`                                           = '$this->vuln_seq', ";
            	    $query .= " `vuln_type`                                          = '$this->vuln_type' ";	    
                    $query .= " WHERE `vuln_type`='".$keyArray['vuln_type']."' AND `vuln_seq`='".$keyArray['vuln_seq']."' AND `finding_id`='".$keyArray['finding_id']."' ";
	    }
	    else {
	       $query = "INSERT INTO `FINDING_VULNS` (
                            `finding_id`, 
                            `vuln_seq`, 
                            `vuln_type`
                            ) VALUES (
                            '$this->finding_id', 
                            '$this->vuln_seq', 
                            '$this->vuln_type'
                            )";
	    }
	    
    	// execute our query
    	$this->db->query($query);
    
    	if ($this->db->queryOK()) { 
    	   if (!$keyArray || !$this->findingvulnExists($keyArray)){
    	       $this->keyArray = $this->db->insert_id();
    	   }
    	   return 1; 
    	} 
    	else {
    	   return 0; 
    	}
	} //saveFindingVuln()
	
	public function clearFindingVuln() {
		
		// clear out (non-db) user values

        unset($this->finding_id);
        unset($this->vuln_seq);
        unset($this->vuln_type);
	} // clearFindingVuln()


	public function deleteFindingVuln($keyArray = NULL) {

		// 
		// REMOVES FINDING_VULNS FROM DATABASE!
		// 

		// ensure that we have an open database connection
		if ($this->db) {

			// define our query
			
			$query = "DELETE FROM `FINDING_VULNS` WHERE `vuln_type`='".$keyArray['vuln_type']."' AND `vuln_seq`='".$keyArray['vuln_seq']."' AND `finding_id`='".$keyArray['finding_id']."' ";
			
			// execute our query
			$this->db->query($query);

			// clear out the current object
			$this->clearFindingVuln();
		
		} // $this->db

	} // deleteFindingVuln()
	
	

	// -----------------------------------------------------------------------
	// 
	// VARIABLE ACCESS METHODS
	// 
	// -----------------------------------------------------------------------


    public function getFindingId()                                         { return $this->finding_id; }
    public function getVulnSeq()                                           { return $this->vuln_seq; }
    public function getVulnType()                                          { return $this->vuln_type; }

	public function getValidFindingIds($offset = 0, $limit = NULL) {
		
		// array to store FindingIds
		$id_array = array();

		// create our query
		$query = "SELECT `finding_id` FROM `FINDING_VULNS`";

		// add in our offset and limit if a limit is provided
		if ($limit) { $query .= " LIMIT $offset, $limit";  }

		// execute the query
		$this->db->query($query);
	
		// evaluate the results
		if ($this->db->queryOK()) {
			
			// push the values onto the array
			while ($id = $this->db->fetch_array()) { array_push($id_array, $id[0]); }
			
		}
		
		// return the array of FindingIds
		return $id_array;
		
	} // getValidFindingIds
	


	public function getValidVulnSeqs($offset = 0, $limit = NULL) {
		
		// array to store VulnSeqs
		$id_array = array();

		// create our query
		$query = "SELECT `vuln_seq` FROM `FINDING_VULNS`";

		// add in our offset and limit if a limit is provided
		if ($limit) { $query .= " LIMIT $offset, $limit";  }

		// execute the query
		$this->db->query($query);
	
		// evaluate the results
		if ($this->db->queryOK()) {
			
			// push the values onto the array
			while ($id = $this->db->fetch_array()) { array_push($id_array, $id[0]); }
			
		}
		
		// return the array of VulnSeqs
		return $id_array;
		
	} // getValidVulnSeqs
	


	public function getValidVulnTypes($offset = 0, $limit = NULL) {
		
		// array to store VulnTypes
		$id_array = array();

		// create our query
		$query = "SELECT `vuln_type` FROM `FINDING_VULNS`";

		// add in our offset and limit if a limit is provided
		if ($limit) { $query .= " LIMIT $offset, $limit";  }

		// execute the query
		$this->db->query($query);
	
		// evaluate the results
		if ($this->db->queryOK()) {
			
			// push the values onto the array
			while ($id = $this->db->fetch_array()) { array_push($id_array, $id[0]); }
			
		}
		
		// return the array of VulnTypes
		return $id_array;
		
	} // getValidVulnTypes
	

	// -----------------------------------------------------------------------
	// 
	// VARIABLE MODIFY METHODS
	// 
	// -----------------------------------------------------------------------


    public function setFindingId($finding_id  =  NULL){ 
		// error check input (by schema)
		if (strlen($finding_id) <= 10){
            $this->finding_id = $finding_id;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setFindingId()
    
    
    public function setVulnSeq($vuln_seq  =  NULL){ 
		// error check input (by schema)
		if (strlen($vuln_seq) <= 10){
            $this->vuln_seq = $vuln_seq;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setVulnSeq()
    
    
    public function setVulnType($vuln_type  =  NULL){ 
		// error check input (by schema)
		if (strlen($vuln_type) <= 3){
            $this->vuln_type = $vuln_type;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setVulnType()
    
    

} // class FindingVuln
?>
