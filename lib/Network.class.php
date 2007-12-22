<?PHP

// 
// INCLUDES
// 
require_once('Database.class.php');


//
// CLASS DEFINITION
// 

class Network {

	// -----------------------------------------------------------------------
	//
	// VARIABLES
	//
	// -----------------------------------------------------------------------


    private $db;

    private $network_id;
    private $network_name;
    private $network_nickname;
    private $network_desc;


	// -----------------------------------------------------------------------
	// 
	// CLASS METHODS
	// 
	// -----------------------------------------------------------------------

	public function __construct($db, $network_id = NULL) {

		// utilize an existing database connection
		$this->db = $db;

		// get Network information or create a new one if none specified
		if ($network_id) {
		  $this->getNetwork($network_id); 
		}

	} // __construct()
	

	public function __destruct() {

		// clear out the network_id to prevent any updates
		$this->network_id = 0;

	} // __destruct()


 	public function __ToString() {
 		
 		// return a string of information
 		return	$this->db->__ToString().
 			'<pre>'.
			'<br>NETWORKS'.
			'<br>------'.

            '<br>network_id                                        : '.$this->network_id.
            '<br>network_name                                      : '.$this->network_name.
            '<br>network_nickname                                  : '.$this->network_nickname.
            '<br>network_desc                                      : '.$this->network_desc.
			'<br></pre>';
 		
 	} // __ToString()
 	

	// -----------------------------------------------------------------------
	// 
	// CLASS MANIPULATION METHODS
	// 
	// -----------------------------------------------------------------------
	
	public function networkExists($network_id = NULL) {
		
		// make sure we have a positive, non-zero network_id
		if ($network_id) {
		
			// build our query
			$query = "SELECT `network_id` FROM `NETWORKS` WHERE (`network_id` = '$network_id')";
			
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
		
	} // networkExists()
	

	public function getNetwork($network_id = NULL) {
		
		// make sure we have a positive, non-zero network_id
		if ($network_id && $this->networkExists($network_id)) {
		
			// designate our retrieval query
			$query = "SELECT * FROM `NETWORKS` WHERE (`network_id` = '$network_id')";
		
			// execute the query
			$this->db->query($query);
		
			// if we get a hit, store the information
			if ($this->db->num_rows() > 0) {
			
				// retrieve the results query
				$results = $this->db->fetch_assoc();
			
				// store the results locally

                $this->network_id                                         = $results['network_id'];
                $this->network_name                                       = $results['network_name'];
                $this->network_nickname                                   = $results['network_nickname'];
                $this->network_desc                                       = $results['network_desc'];
			
			} // this->db->fetch_assoc()
			
			// system not retrieved, clear out any potential values
			else {
			     $this->clearNetwork(); 
			}
		} // if $network_id

	} // getNetwork()


		
	public function saveNetwork(){
	
	    if ($this->network_id && $this->networkExists($this->network_id)){
    	    $query = "UPDATE `NETWORKS` SET ";    
            	    $query .= " `network_name`                                       = '$this->network_name', ";
            	    $query .= " `network_nickname`                                   = '$this->network_nickname', ";
            	    $query .= " `network_desc`                                       = '$this->network_desc' ";	    
                    $query .= " WHERE `network_id`                                   = '$this->network_id' ";
	    }
	    else {
	       $query = "INSERT INTO `NETWORKS` (
                            `network_name`, 
                            `network_nickname`, 
                            `network_desc`
                            ) VALUES (
                            '$this->network_name', 
                            '$this->network_nickname', 
                            '$this->network_desc'
                            )";
	    }
	    
    	// execute our query
    	$this->db->query($query);
    
    	if ($this->db->queryOK()) { 
    	   if (!$this->network_id || !$this->networkExists($this->network_id)){
    	       $this->network_id = $this->db->insert_id();
    	   }
    	   return 1; 
    	} 
    	else {
    	   return 0; 
    	}
	} //saveNetwork()
	
	public function clearNetwork() {
		
		// clear out (non-db) user values

        unset($this->network_id);
        unset($this->network_name);
        unset($this->network_nickname);
        unset($this->network_desc);
	} // clearNetwork()


	public function deleteNetwork() {

		// 
		// REMOVES NETWORKS FROM DATABASE!
		// 

		// ensure that we have an open database connection
		if ($this->db) {

			// define our query
			$query = "DELETE FROM `NETWORKS` WHERE (`network_id` = '$this->network_id')";

			// execute our query
			$this->db->query($query);

			// clear out the current object
			$this->clearNetwork();
		
		} // $this->db

	} // deleteNetwork()
	
	

	// -----------------------------------------------------------------------
	// 
	// VARIABLE ACCESS METHODS
	// 
	// -----------------------------------------------------------------------


    public function getNetworkId()                                         { return $this->network_id; }
    public function getNetworkName()                                       { return $this->network_name; }
    public function getNetworkNickname()                                   { return $this->network_nickname; }
    public function getNetworkDesc()                                       { return $this->network_desc; }

	public function getValidNetworkIds($offset = 0, $limit = NULL) {
		
		// array to store netword_ids
		$id_array = array();

		// create our query
		$query = "SELECT `network_id` FROM `NETWORKS`";

		// add in our offset and limit if a limit is provided
		if ($limit) { $query .= " LIMIT $offset, $limit";  }

		// execute the query
		$this->db->query($query);
	
		// evaluate the results
		if ($this->db->queryOK()) {
			
			// push the values onto the array
			while ($id = $this->db->fetch_array()) { array_push($id_array, $id[0]); }
			
		}
		
		// return the array of netword_ids
		return $id_array;
		
	} // getValidNetworkIds
    
	// -----------------------------------------------------------------------
	// 
	// VARIABLE MODIFY METHODS
	// 
	// -----------------------------------------------------------------------


    public function setNetworkName($network_name  =  NULL){ 
		// error check input (by schema)
		if (strlen($network_name) <= 64){
            $this->network_name = $network_name;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setNetworkName()
    
    
    public function setNetworkNickname($network_nickname  =  NULL){ 
		// error check input (by schema)
		if (strlen($network_nickname) <= 8){
            $this->network_nickname = $network_nickname;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setNetworkNickname()
    
    
    public function setNetworkDesc($network_desc  =  NULL){ 
		// error check input (by schema)
		if (strlen($network_desc) >= 0){
            $this->network_desc = $network_desc;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setNetworkDesc()
    
    

} // class Network
?>
