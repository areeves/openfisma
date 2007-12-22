<?PHP

// 
// INCLUDES
// 
require_once('Database.class.php');


//
// CLASS DEFINITION
// 

class Plugin {

	// -----------------------------------------------------------------------
	//
	// VARIABLES
	//
	// -----------------------------------------------------------------------


    private $db;

    private $plugin_id;
    private $plugin_name;
    private $plugin_nickname;
    private $plugin_abbreviation;
    private $plugin_desc;


	// -----------------------------------------------------------------------
	// 
	// CLASS METHODS
	// 
	// -----------------------------------------------------------------------

	public function __construct($db, $plugin_id = NULL) {

		// utilize an existing database connection
		$this->db = $db;

		// get Plugin information or create a new one if none specified
		if ($plugin_id) {
		  $this->getPlugin($plugin_id); 
		}

	} // __construct()
	

	public function __destruct() {

		// clear out the plugin_id to prevent any updates
		$this->plugin_id = 0;

	} // __destruct()


 	public function __ToString() {
 		
 		// return a string of information
 		return	$this->db->__ToString().
 			'<pre>'.
			'<br>PLUGINS'.
			'<br>------'.

            '<br>plugin_id                                         : '.$this->plugin_id.
            '<br>plugin_name                                       : '.$this->plugin_name.
            '<br>plugin_nickname                                   : '.$this->plugin_nickname.
            '<br>plugin_abbreviation                               : '.$this->plugin_abbreviation.
            '<br>plugin_desc                                       : '.$this->plugin_desc.
			'<br></pre>';
 		
 	} // __ToString()
 	

	// -----------------------------------------------------------------------
	// 
	// CLASS MANIPULATION METHODS
	// 
	// -----------------------------------------------------------------------
	
	public function pluginExists($plugin_id = NULL) {
		
		// make sure we have a positive, non-zero plugin_id
		if ($plugin_id) {
		
			// build our query
			$query = "SELECT `plugin_id` FROM `PLUGINS` WHERE (`plugin_id` = '$plugin_id')";
			
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
		
	} // pluginExists()
	

	public function getPlugin($plugin_id = NULL) {
		
		// make sure we have a positive, non-zero plugin_id
		if ($plugin_id && $this->pluginExists($plugin_id)) {
		
			// designate our retrieval query
			$query = "SELECT * FROM `PLUGINS` WHERE (`plugin_id` = '$plugin_id')";
		
			// execute the query
			$this->db->query($query);
		
			// if we get a hit, store the information
			if ($this->db->num_rows() > 0) {
			
				// retrieve the results query
				$results = $this->db->fetch_assoc();
			
				// store the results locally

                $this->plugin_id                                          = $results['plugin_id'];
                $this->plugin_name                                        = $results['plugin_name'];
                $this->plugin_nickname                                    = $results['plugin_nickname'];
                $this->plugin_abbreviation                                = $results['plugin_abbreviation'];
                $this->plugin_desc                                        = $results['plugin_desc'];
			
			} // this->db->fetch_assoc()
			
			// system not retrieved, clear out any potential values
			else {
			     $this->clearPlugin(); 
			}
		} // if $plugin_id

	} // getPlugin()


		
	public function savePlugin(){
	
	    if ($this->plugin_id && $this->pluginExists($this->plugin_id)){
    	    $query = "UPDATE `PLUGINS` SET ";    
            	    $query .= " `plugin_name`                                        = '$this->plugin_name', ";
            	    $query .= " `plugin_nickname`                                    = '$this->plugin_nickname', ";
            	    $query .= " `plugin_abbreviation`                                = '$this->plugin_abbreviation', ";
            	    $query .= " `plugin_desc`                                        = '$this->plugin_desc' ";	    
                    $query .= " WHERE `plugin_id`                                    = '$this->plugin_id' ";
	    }
	    else {
	       $query = "INSERT INTO `PLUGINS` (
                            `plugin_name`, 
                            `plugin_nickname`, 
                            `plugin_abbreviation`, 
                            `plugin_desc`
                            ) VALUES (
                            '$this->plugin_name', 
                            '$this->plugin_nickname', 
                            '$this->plugin_abbreviation', 
                            '$this->plugin_desc'
                            )";
	    }
	    
    	// execute our query
    	$this->db->query($query);
    
    	if ($this->db->queryOK()) { 
    	   if (!$this->plugin_id || !$this->pluginExists($this->plugin_id)){
    	       $this->plugin_id = $this->db->insert_id();
    	   }
    	   return 1; 
    	} 
    	else {
    	   return 0; 
    	}
	} //savePlugin()
	
	public function clearPlugin() {
		
		// clear out (non-db) user values

        unset($this->plugin_id);
        unset($this->plugin_name);
        unset($this->plugin_nickname);
        unset($this->plugin_abbreviation);
        unset($this->plugin_desc);
	} // clearPlugin()


	public function deletePlugin() {

		// 
		// REMOVES PLUGINS FROM DATABASE!
		// 

		// ensure that we have an open database connection
		if ($this->db) {

			// define our query
			$query = "DELETE FROM `PLUGINS` WHERE (`plugin_id` = '$this->plugin_id')";

			// execute our query
			$this->db->query($query);

			// clear out the current object
			$this->clearPlugin();
		
		} // $this->db

	} // deletePlugin()
	
	

	// -----------------------------------------------------------------------
	// 
	// VARIABLE ACCESS METHODS
	// 
	// -----------------------------------------------------------------------


    public function getPluginId()                                          { return $this->plugin_id; }
    public function getPluginName()                                        { return $this->plugin_name; }
    public function getPluginNickname()                                    { return $this->plugin_nickname; }
    public function getPluginAbbreviation()                                { return $this->plugin_abbreviation; }
    public function getPluginDesc()                                        { return $this->plugin_desc; }

	public function getValidPluginIds($offset = 0, $limit = NULL) {
		
		// array to store plugin_ids
		$id_array = array();

		// create our query
		$query = "SELECT `plugin_id` FROM `PLUGINS`";

		// add in our offset and limit if a limit is provided
		if ($limit) { $query .= " LIMIT $offset, $limit";  }

		// execute the query
		$this->db->query($query);
	
		// evaluate the results
		if ($this->db->queryOK()) {
			
			// push the values onto the array
			while ($id = $this->db->fetch_array()) { array_push($id_array, $id[0]); }
			
		}
		
		// return the array of plugin_ids
		return $id_array;
		
	} // getValidPluginIds
    
	// -----------------------------------------------------------------------
	// 
	// VARIABLE MODIFY METHODS
	// 
	// -----------------------------------------------------------------------


    public function setPluginName($plugin_name  =  NULL){ 
		// error check input (by schema)
		if (strlen($plugin_name) <= 64){
            $this->plugin_name = $plugin_name;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setPluginName()
    
    
    public function setPluginNickname($plugin_nickname  =  NULL){ 
		// error check input (by schema)
		if (strlen($plugin_nickname) <= 12){
            $this->plugin_nickname = $plugin_nickname;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setPluginNickname()
    
    
    public function setPluginAbbreviation($plugin_abbreviation  =  NULL){ 
		// error check input (by schema)
		if (strlen($plugin_abbreviation) <= 3){
            $this->plugin_abbreviation = $plugin_abbreviation;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setPluginAbbreviation()
    
    
    public function setPluginDesc($plugin_desc  =  NULL){ 
		// error check input (by schema)
		if (strlen($plugin_desc) >= 0){
            $this->plugin_desc = $plugin_desc;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setPluginDesc()
    
    

} // class Plugin
?>
