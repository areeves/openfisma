<?PHP

// 
// INCLUDES
// 
require_once('Database.class.php');


//
// CLASS DEFINITION
// 

class Finding {

	// -----------------------------------------------------------------------
	//
	// VARIABLES
	//
	// -----------------------------------------------------------------------


    private $db;

    private $finding_id;
    private $source_id;
    private $asset_id;
    private $finding_status;
    private $finding_date_created;
    private $finding_date_discovered;
    private $finding_date_closed;
    private $finding_data;


	// -----------------------------------------------------------------------
	// 
	// CLASS METHODS
	// 
	// -----------------------------------------------------------------------

	public function __construct($db, $finding_id = NULL) {

		// utilize an existing database connection
		$this->db = $db;

		// get finding information or create a new one if none specified
		if ($finding_id) {
		  $this->getFinding($finding_id); 
		}

	} // __construct()
	

	public function __destruct() {

		// clear out the finding_id to prevent any updates
		$this->finding_id = 0;

	} // __destruct()


 	public function __ToString() {
 		
 		// return a string of information
 		return	$this->db->__ToString().
 			'<pre>'.
			'<br>FINDINGS'.
			'<br>------'.

            '<br>finding_id                                        : '.$this->finding_id.
            '<br>source_id                                         : '.$this->source_id.
            '<br>asset_id                                          : '.$this->asset_id.
            '<br>finding_status                                    : '.$this->finding_status.
            '<br>finding_date_created                              : '.$this->finding_date_created.
            '<br>finding_date_discovered                           : '.$this->finding_date_discovered.
            '<br>finding_date_closed                               : '.$this->finding_date_closed.
            '<br>finding_data                                      : '.$this->finding_data.
			'<br></pre>';
 		
 	} // __ToString()
 	

	// -----------------------------------------------------------------------
	// 
	// CLASS MANIPULATION METHODS
	// 
	// -----------------------------------------------------------------------
	
	public function findingExists($finding_id = NULL) {
		
		// make sure we have a positive, non-zero finding_id
		if ($finding_id) {
		
			// build our query
			$query = "SELECT `finding_id` FROM `FINDINGS` WHERE (`finding_id` = '$finding_id')";
			
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
		
	} // findingExists()
	

	public function getFinding($finding_id = NULL) {
		
		// make sure we have a positive, non-zero finding_id
		if ($finding_id && $this->findingExists($finding_id)) {
		
			// designate our retrieval query
			$query = "SELECT * FROM `FINDINGS` WHERE (`finding_id` = '$finding_id')";
		
			// execute the query
			$this->db->query($query);
		
			// if we get a hit, store the information
			if ($this->db->num_rows() > 0) {
			
				// retrieve the results query
				$results = $this->db->fetch_assoc();
			
				// store the results locally

                $this->finding_id                                         = $results['finding_id'];
                $this->source_id                                          = $results['source_id'];
                $this->asset_id                                           = $results['asset_id'];
                $this->finding_status                                     = $results['finding_status'];
                $this->finding_date_created                               = $results['finding_date_created'];
                $this->finding_date_discovered                            = $results['finding_date_discovered'];
                $this->finding_date_closed                                = $results['finding_date_closed'];
                $this->finding_data                                       = $results['finding_data'];
			
			} // this->db->fetch_assoc()
			
			// system not retrieved, clear out any potential values
			else {
			     $this->clearFinding(); 
			}
		} // if $finding_id

	} // getFinding()


		
	public function saveFinding(){
	
	    if ($this->finding_id && $this->findingExists($this->finding_id)){
    	    $query = "UPDATE `FINDINGS` SET ";    
            	    $query .= " `source_id`                                          = '$this->source_id', ";
            	    $query .= " `asset_id`                                           = '$this->asset_id', ";
            	    $query .= " `finding_status`                                     = '$this->finding_status', ";
            	    $query .= " `finding_date_created`                               = '$this->finding_date_created', ";
            	    $query .= " `finding_date_discovered`                            = '$this->finding_date_discovered', ";
            	    $query .= " `finding_date_closed`                                = '$this->finding_date_closed', ";
            	    $query .= " `finding_data`                                       = '$this->finding_data' ";	    
                    $query .= " WHERE `finding_id`                                   = '$this->finding_id' ";
	    }
	    else {
	       $query = "INSERT INTO `FINDINGS` (
                            `source_id`, 
                            `asset_id`, 
                            `finding_status`, 
                            `finding_date_created`, 
                            `finding_date_discovered`, 
                            `finding_date_closed`, 
                            `finding_data`
                            ) VALUES (
                            '$this->source_id', 
                            '$this->asset_id', 
                            '$this->finding_status', 
                            '$this->finding_date_created', 
                            '$this->finding_date_discovered', 
                            '$this->finding_date_closed', 
                            '$this->finding_data'
                            )";
	    }
	    
    	// execute our query
    	$this->db->query($query);
    
    	if ($this->db->queryOK()) { 
    	   if (!$this->finding_id || !$this->findingExists($this->finding_id)){
    	       $this->finding_id = $this->db->insert_id();
    	   }
    	   return 1; 
    	} 
    	else {
    	   return 0; 
    	}
	} //saveFinding()
	
	public function clearFinding() {
		
		// clear out (non-db) user values

        unset($this->finding_id);
        unset($this->source_id);
        unset($this->asset_id);
        unset($this->finding_status);
        unset($this->finding_date_created);
        unset($this->finding_date_discovered);
        unset($this->finding_date_closed);
        unset($this->finding_data);
	} // clearFinding()


	public function deleteFinding() {

		// 
		// REMOVES FINDINGS FROM DATABASE!
		// 

		// ensure that we have an open database connection
		if ($this->db) {

			// define our query
			$query = "DELETE FROM `FINDINGS` WHERE (`finding_id` = '$this->finding_id')";

			// execute our query
			$this->db->query($query);

			// clear out the current object
			$this->clearFinding();
		
		} // $this->db

	} // deleteFinding()
	
	

	// -----------------------------------------------------------------------
	// 
	// VARIABLE ACCESS METHODS
	// 
	// -----------------------------------------------------------------------


    public function getFindingId()                                         { return $this->finding_id; }
    public function getSourceId()                                          { return $this->source_id; }
    public function getAssetId()                                           { return $this->asset_id; }
    public function getFindingStatus()                                     { return $this->finding_status; }
    public function getFindingDateCreated()                                { return $this->finding_date_created; }
    public function getFindingDateDiscovered()                             { return $this->finding_date_discovered; }
    public function getFindingDateClosed()                                 { return $this->finding_date_closed; }
    public function getFindingData()                                       { return $this->finding_data; }

	public function getValidFindingIds($offset = 0, $limit = NULL) {
		
		// array to store finding_ids
		$id_array = array();

		// create our query
		$query = "SELECT `finding_id` FROM `FINDINGS`";

		// add in our offset and limit if a limit is provided
		if ($limit) { $query .= " LIMIT $offset, $limit";  }

		// execute the query
		$this->db->query($query);
	
		// evaluate the results
		if ($this->db->queryOK()) {
			
			// push the values onto the array
			while ($id = $this->db->fetch_array()) { array_push($id_array, $id[0]); }
			
		}
		
		// return the array of finding_ids
		return $id_array;
		
	} // getValidFindingIds
    
	// -----------------------------------------------------------------------
	// 
	// VARIABLE MODIFY METHODS
	// 
	// -----------------------------------------------------------------------


    public function setSourceId($source_id  =  NULL){ 
		// error check input (by schema)
		if (strlen($source_id) <= 10){
            $this->source_id = $source_id;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setSourceId()
    
    
    public function setAssetId($asset_id  =  NULL){ 
		// error check input (by schema)
		if (strlen($asset_id) <= 10){
            $this->asset_id = $asset_id;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setAssetId()
    
    
    public function setFindingStatus($finding_status  =  NULL){ 
		// error check input (by schema)
		if (in_array($finding_status, array('OPEN','CLOSED','REMEDIATION','DELETED')) ){
            $this->finding_status = $finding_status;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setFindingStatus()
    
    
    public function setFindingDateCreated($finding_date_created  =  NULL){ 
		// error check input (by schema)
		if (strlen($finding_date_created) >= 0){
            $this->finding_date_created = $finding_date_created;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setFindingDateCreated()
    
    
    public function setFindingDateDiscovered($finding_date_discovered  =  NULL){ 
		// error check input (by schema)
		if (strlen($finding_date_discovered) >= 0){
            $this->finding_date_discovered = $finding_date_discovered;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setFindingDateDiscovered()
    
    
    public function setFindingDateClosed($finding_date_closed  =  NULL){ 
		// error check input (by schema)
		if (strlen($finding_date_closed) >= 0){
            $this->finding_date_closed = $finding_date_closed;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setFindingDateClosed()
    
    
    public function setFindingData($finding_data  =  NULL){ 
		// error check input (by schema)
		if (strlen($finding_data) >= 0){
            $this->finding_data = $finding_data;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setFindingData()
    
    

} // class Finding
?>
