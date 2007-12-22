<?PHP

// 
// INCLUDES
// 
require_once('Database.class.php');


//
// CLASS DEFINITION
// 

class SystemAsset {

	// -----------------------------------------------------------------------
	//
	// VARIABLES
	//
	// -----------------------------------------------------------------------


    private $db;

    private $system_id;
    private $asset_id;
    private $system_is_owner;


	// -----------------------------------------------------------------------
	// 
	// CLASS METHODS
	// 
	// -----------------------------------------------------------------------

	public function __construct($db, $keyArray = NULL) {

		// utilize an existing database connection
		$this->db = $db;

		// get SystemAsset information or create a new one if none specified
		if ($keyArray) {
		  $this->getSystemAsset($keyArray); 
		}

	} // __construct()
	

	public function __destruct() {
		// clear out the keyArray to prevent any updates

		$this->system_id = null;
		$this->asset_id = null;
	} // __destruct()


 	public function __ToString() {
 		
 		// return a string of information
 		return	$this->db->__ToString().
 			'<pre>'.
			'<br>SYSTEM_ASSETS'.
			'<br>------'.

            '<br>system_id                                         : '.$this->system_id.
            '<br>asset_id                                          : '.$this->asset_id.
            '<br>system_is_owner                                   : '.$this->system_is_owner.
			'<br></pre>';
 		
 	} // __ToString()
 	

	// -----------------------------------------------------------------------
	// 
	// CLASS MANIPULATION METHODS
	// 
	// -----------------------------------------------------------------------
	
	public function systemassetExists($keyArray = NULL) {
		
		// make sure we have a positive, non-zero keyArray
		if ($keyArray) {
		
			// build our query

		$query = "SELECT * FROM `SYSTEM_ASSETS` WHERE `system_id`='".$keyArray['system_id']."' AND `asset_id`='".$keyArray['asset_id']."' ";

			
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
		
	} // systemassetExists()
	

	public function getSystemAsset($keyArray = NULL) {
		
		// make sure we have a positive, non-zero keyArray
		if ($keyArray && $this->systemassetExists($keyArray)) {
		
			// designate our retrieval query

			$query = "SELECT * FROM `SYSTEM_ASSETS` WHERE `system_id`='".$keyArray['system_id']."' AND `asset_id`='".$keyArray['asset_id']."' ";

		
			// execute the query
			$this->db->query($query);
		
			// if we get a hit, store the information
			if ($this->db->num_rows() > 0) {
			
				// retrieve the results query
				$results = $this->db->fetch_assoc();
			
				// store the results locally

                $this->system_id                                          = $results['system_id'];
                $this->asset_id                                           = $results['asset_id'];
                $this->system_is_owner                                    = $results['system_is_owner'];
			
			} // this->db->fetch_assoc()
			
			// system not retrieved, clear out any potential values
			else {
			     $this->clearSystemAsset(); 
			}
		} // if $keyArray

	} // getSystemAsset()


		
	public function saveSystemAsset($keyArray = NULL){
	
	    if ($keyArray && $this->systemassetExists($keyArray)){
    	    $query = "UPDATE `SYSTEM_ASSETS` SET ";    
            	    $query .= " `system_id`                                          = '$this->system_id', ";
            	    $query .= " `asset_id`                                           = '$this->asset_id', ";
            	    $query .= " `system_is_owner`                                    = '$this->system_is_owner' ";	    
                    $query .= " WHERE `system_id`='".$keyArray['system_id']."' AND `asset_id`='".$keyArray['asset_id']."' ";
	    }
	    else {
	       $query = "INSERT INTO `SYSTEM_ASSETS` (
                            `system_id`, 
                            `asset_id`, 
                            `system_is_owner`
                            ) VALUES (
                            '$this->system_id', 
                            '$this->asset_id', 
                            '$this->system_is_owner'
                            )";
	    }
	    
    	// execute our query
    	$this->db->query($query);
    
    	if ($this->db->queryOK()) { 
    	   if (!$keyArray || !$this->systemassetExists($keyArray)){
    	       $this->keyArray = $this->db->insert_id();
    	   }
    	   return 1; 
    	} 
    	else {
    	   return 0; 
    	}
	} //saveSystemAsset()
	
	public function clearSystemAsset() {
		
		// clear out (non-db) user values

        unset($this->system_id);
        unset($this->asset_id);
        unset($this->system_is_owner);
	} // clearSystemAsset()


	public function deleteSystemAsset($keyArray = NULL) {

		// 
		// REMOVES SYSTEM_ASSETS FROM DATABASE!
		// 

		// ensure that we have an open database connection
		if ($this->db) {

			// define our query
			
			$query = "DELETE FROM `SYSTEM_ASSETS` WHERE `system_id`='".$keyArray['system_id']."' AND `asset_id`='".$keyArray['asset_id']."' ";
			
			// execute our query
			$this->db->query($query);

			// clear out the current object
			$this->clearSystemAsset();
		
		} // $this->db

	} // deleteSystemAsset()
	
	

	// -----------------------------------------------------------------------
	// 
	// VARIABLE ACCESS METHODS
	// 
	// -----------------------------------------------------------------------


    public function getSystemId()                                          { return $this->system_id; }
    public function getAssetId()                                           { return $this->asset_id; }
    public function getSystemIsOwner()                                     { return $this->system_is_owner; }

	public function getValidSystemIds($offset = 0, $limit = NULL) {
		
		// array to store SystemIds
		$id_array = array();

		// create our query
		$query = "SELECT `system_id` FROM `SYSTEM_ASSETS`";

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
	


	public function getValidAssetIds($offset = 0, $limit = NULL) {
		
		// array to store AssetIds
		$id_array = array();

		// create our query
		$query = "SELECT `asset_id` FROM `SYSTEM_ASSETS`";

		// add in our offset and limit if a limit is provided
		if ($limit) { $query .= " LIMIT $offset, $limit";  }

		// execute the query
		$this->db->query($query);
	
		// evaluate the results
		if ($this->db->queryOK()) {
			
			// push the values onto the array
			while ($id = $this->db->fetch_array()) { array_push($id_array, $id[0]); }
			
		}
		
		// return the array of AssetIds
		return $id_array;
		
	} // getValidAssetIds
	


	public function getValidSystemIsOwners($offset = 0, $limit = NULL) {
		
		// array to store SystemIsOwners
		$id_array = array();

		// create our query
		$query = "SELECT `system_is_owner` FROM `SYSTEM_ASSETS`";

		// add in our offset and limit if a limit is provided
		if ($limit) { $query .= " LIMIT $offset, $limit";  }

		// execute the query
		$this->db->query($query);
	
		// evaluate the results
		if ($this->db->queryOK()) {
			
			// push the values onto the array
			while ($id = $this->db->fetch_array()) { array_push($id_array, $id[0]); }
			
		}
		
		// return the array of SystemIsOwners
		return $id_array;
		
	} // getValidSystemIsOwners
	

	// -----------------------------------------------------------------------
	// 
	// VARIABLE MODIFY METHODS
	// 
	// -----------------------------------------------------------------------


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
    
    
    public function setSystemIsOwner($system_is_owner  =  NULL){ 
		// error check input (by schema)
		if (strlen($system_is_owner) <= 1){
            $this->system_is_owner = $system_is_owner;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setSystemIsOwner()
    
    

} // class SystemAsset
?>
