<?PHP

// 
// INCLUDES
// 
require_once('Database.class.php');


//
// CLASS DEFINITION
// 

class FindingSource {

	// -----------------------------------------------------------------------
	//
	// VARIABLES
	//
	// -----------------------------------------------------------------------


    private $db;

    private $source_id;
    private $source_name;
    private $source_nickname;
    private $source_desc;


	// -----------------------------------------------------------------------
	// 
	// CLASS METHODS
	// 
	// -----------------------------------------------------------------------

	public function __construct($db, $source_id = NULL) {

		// utilize an existing database connection
		$this->db = $db;

		// get FindingSource information or create a new one if none specified
		if ($source_id) {
		  $this->getFindingSource($source_id); 
		}

	} // __construct()
	

	public function __destruct() {

		// clear out the source_id to prevent any updates
		$this->source_id = 0;

	} // __destruct()


 	public function __ToString() {
 		
 		// return a string of information
 		return	$this->db->__ToString().
 			'<pre>'.
			'<br>FINDING_SOURCES'.
			'<br>------'.

            '<br>source_id                                         : '.$this->source_id.
            '<br>source_name                                       : '.$this->source_name.
            '<br>source_nickname                                   : '.$this->source_nickname.
            '<br>source_desc                                       : '.$this->source_desc.
			'<br></pre>';
 		
 	} // __ToString()
 	

	// -----------------------------------------------------------------------
	// 
	// CLASS MANIPULATION METHODS
	// 
	// -----------------------------------------------------------------------
	
	public function findingsourceExists($source_id = NULL) {
		
		// make sure we have a positive, non-zero source_id
		if ($source_id) {
		
			// build our query
			$query = "SELECT `source_id` FROM `FINDING_SOURCES` WHERE (`source_id` = '$source_id')";
			
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
		
	} // findingsourceExists()
	

	public function getFindingSource($source_id = NULL) {
		
		// make sure we have a positive, non-zero source_id
		if ($source_id && $this->findingsourceExists($source_id)) {
		
			// designate our retrieval query
			$query = "SELECT * FROM `FINDING_SOURCES` WHERE (`source_id` = '$source_id')";
		
			// execute the query
			$this->db->query($query);
		
			// if we get a hit, store the information
			if ($this->db->num_rows() > 0) {
			
				// retrieve the results query
				$results = $this->db->fetch_assoc();
			
				// store the results locally

                $this->source_id                                          = $results['source_id'];
                $this->source_name                                        = $results['source_name'];
                $this->source_nickname                                    = $results['source_nickname'];
                $this->source_desc                                        = $results['source_desc'];
			
			} // this->db->fetch_assoc()
			
			// system not retrieved, clear out any potential values
			else {
			     $this->clearFindingSource(); 
			}
		} // if $source_id

	} // getFindingSource()


		
	public function saveFindingSource(){
	
	    if ($this->source_id && $this->findingsourceExists($this->source_id)){
    	    $query = "UPDATE `FINDING_SOURCES` SET ";    
            	    $query .= " `source_name`                                        = '$this->source_name', ";
            	    $query .= " `source_nickname`                                    = '$this->source_nickname', ";
            	    $query .= " `source_desc`                                        = '$this->source_desc' ";	    
                    $query .= " WHERE `source_id`                                    = '$this->source_id' ";
	    }
	    else {
	       $query = "INSERT INTO `FINDING_SOURCES` (
                            `source_name`, 
                            `source_nickname`, 
                            `source_desc`
                            ) VALUES (
                            '$this->source_name', 
                            '$this->source_nickname', 
                            '$this->source_desc'
                            )";
	    }
	    
    	// execute our query
    	$this->db->query($query);
    
    	if ($this->db->queryOK()) { 
    	   if (!$this->source_id || !$this->findingsourceExists($this->source_id)){
    	       $this->source_id = $this->db->insert_id();
    	   }
    	   return 1; 
    	} 
    	else {
    	   return 0; 
    	}
	} //saveFindingSource()
	
	public function clearFindingSource() {
		
		// clear out (non-db) user values

        unset($this->source_id);
        unset($this->source_name);
        unset($this->source_nickname);
        unset($this->source_desc);
	} // clearFindingSource()


	public function deleteFindingSource() {

		// 
		// REMOVES FINDING_SOURCES FROM DATABASE!
		// 

		// ensure that we have an open database connection
		if ($this->db) {

			// define our query
			$query = "DELETE FROM `FINDING_SOURCES` WHERE (`source_id` = '$this->source_id')";

			// execute our query
			$this->db->query($query);

			// clear out the current object
			$this->clearFindingSource();
		
		} // $this->db

	} // deleteFindingSource()
	
	

	// -----------------------------------------------------------------------
	// 
	// VARIABLE ACCESS METHODS
	// 
	// -----------------------------------------------------------------------


    public function getSourceId()                                          { return $this->source_id; }
    public function getSourceName()                                        { return $this->source_name; }
    public function getSourceNickname()                                    { return $this->source_nickname; }
    public function getSourceDesc()                                        { return $this->source_desc; }


	public function getValidSourceIds($offset = 0, $limit = NULL) {
		
		// array to store Source_ids
		$id_array = array();

		// create our query
		$query = "SELECT `source_id` FROM `FINDING_SOURCES`";

		// add in our offset and limit if a limit is provided
		if ($limit) { $query .= " LIMIT $offset, $limit";  }

		// execute the query
		$this->db->query($query);
	
		// evaluate the results
		if ($this->db->queryOK()) {
			
			// push the values onto the array
			while ($id = $this->db->fetch_array()) { array_push($id_array, $id[0]); }
			
		}
		
		// return the array of Source_ids
		return $id_array;
		
	} // getValidSourceIds
	
	public function getValidSourceNames($offset = 0, $limit = NULL) {
		
		// array to store Source_names
		$name_array = array();

		// create our query
		$query = "SELECT `source_nickname` FROM `FINDING_SOURCES`";

		// add in our offset and limit if a limit is provided
		if ($limit) { $query .= " LIMIT $offset, $limit";  }

		// execute the query
		$this->db->query($query);
	
		// evaluate the results
		if ($this->db->queryOK()) {
			
			// push the values onto the array
			while ($name = $this->db->fetch_array()) { array_push($name_array, $name[0]); }
			
		}
		
		// return the array of Source_names
		return $name_array;
		
	} // getValidSourceNames
    
	// -----------------------------------------------------------------------
	// 
	// VARIABLE MODIFY METHODS
	// 
	// -----------------------------------------------------------------------


    public function setSourceName($source_name  =  NULL){ 
		// error check input (by schema)
		if (strlen($source_name) <= 32){
            $this->source_name = $source_name;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setSourceName()
    
    
    public function setSourceNickname($source_nickname  =  NULL){ 
		// error check input (by schema)
		if (strlen($source_nickname) <= 8){
            $this->source_nickname = $source_nickname;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setSourceNickname()
    
    
    public function setSourceDesc($source_desc  =  NULL){ 
		// error check input (by schema)
		if (strlen($source_desc) >= 0){
            $this->source_desc = $source_desc;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setSourceDesc()
    
    

} // class FindingSource
?>
