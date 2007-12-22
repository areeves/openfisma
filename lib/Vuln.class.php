<?PHP

// 
// INCLUDES
// 
require_once('Database.class.php');


//
// CLASS DEFINITION
// 

class Vuln {

	// -----------------------------------------------------------------------
	//
	// VARIABLES
	//
	// -----------------------------------------------------------------------


    private $db;

    private $vuln_seq;
    private $vuln_type;
    private $vuln_desc_primary;
    private $vuln_desc_secondary;
    private $vuln_date_discovered;
    private $vuln_date_modified;
    private $vuln_date_published;
    private $vuln_severity;
    private $vuln_loss_availability;
    private $vuln_loss_confidentiality;
    private $vuln_loss_integrity;
    private $vuln_loss_security_admin;
    private $vuln_loss_security_user;
    private $vuln_loss_security_other;
    private $vuln_type_access;
    private $vuln_type_input;
    private $vuln_type_input_bound;
    private $vuln_type_input_buffer;
    private $vuln_type_design;
    private $vuln_type_exception;
    private $vuln_type_environment;
    private $vuln_type_config;
    private $vuln_type_race;
    private $vuln_type_other;
    private $vuln_range_local;
    private $vuln_range_remote;
    private $vuln_range_user;


	// -----------------------------------------------------------------------
	// 
	// CLASS METHODS
	// 
	// -----------------------------------------------------------------------

	public function __construct($db, $vuln_type = NULL, $vuln_seq = NULL) {

		// utilize an existing database connection
		$this->db = $db;

		// get Vuln information or create a new one if none specified
		if ($vuln_type && $vuln_seq) { $this->getVuln($vuln_type, $vuln_seq); }

	} // __construct()
	

	public function __destruct() {

		// clear out the vuln_seq to prevent any updates
		$this->vuln_seq = 0;

	} // __destruct()


 	public function __ToString() {
 		
 		// return a string of information
 		return	$this->db->__ToString().
 			'<pre>'.
			'<br>VULNERABILITIES'.
			'<br>------'.

            '<br>vuln_seq                                          : '.$this->vuln_seq.
            '<br>vuln_type                                         : '.$this->vuln_type.
            '<br>vuln_desc_primary                                 : '.$this->vuln_desc_primary.
            '<br>vuln_desc_secondary                               : '.$this->vuln_desc_secondary.
            '<br>vuln_date_discovered                              : '.$this->vuln_date_discovered.
            '<br>vuln_date_modified                                : '.$this->vuln_date_modified.
            '<br>vuln_date_published                               : '.$this->vuln_date_published.
            '<br>vuln_severity                                     : '.$this->vuln_severity.
            '<br>vuln_loss_availability                            : '.$this->vuln_loss_availability.
            '<br>vuln_loss_confidentiality                         : '.$this->vuln_loss_confidentiality.
            '<br>vuln_loss_integrity                               : '.$this->vuln_loss_integrity.
            '<br>vuln_loss_security_admin                          : '.$this->vuln_loss_security_admin.
            '<br>vuln_loss_security_user                           : '.$this->vuln_loss_security_user.
            '<br>vuln_loss_security_other                          : '.$this->vuln_loss_security_other.
            '<br>vuln_type_access                                  : '.$this->vuln_type_access.
            '<br>vuln_type_input                                   : '.$this->vuln_type_input.
            '<br>vuln_type_input_bound                             : '.$this->vuln_type_input_bound.
            '<br>vuln_type_input_buffer                            : '.$this->vuln_type_input_buffer.
            '<br>vuln_type_design                                  : '.$this->vuln_type_design.
            '<br>vuln_type_exception                               : '.$this->vuln_type_exception.
            '<br>vuln_type_environment                             : '.$this->vuln_type_environment.
            '<br>vuln_type_config                                  : '.$this->vuln_type_config.
            '<br>vuln_type_race                                    : '.$this->vuln_type_race.
            '<br>vuln_type_other                                   : '.$this->vuln_type_other.
            '<br>vuln_range_local                                  : '.$this->vuln_range_local.
            '<br>vuln_range_remote                                 : '.$this->vuln_range_remote.
            '<br>vuln_range_user                                   : '.$this->vuln_range_user.
			'<br></pre>';
 		
 	} // __ToString()
 	

	// -----------------------------------------------------------------------
	// 
	// CLASS MANIPULATION METHODS
	// 
	// -----------------------------------------------------------------------
	
	public function vulnExists($vuln_type = NULL, $vuln_seq = NULL) {
		
	  // make sure we have a positive, non-zero vuln_seq
	  if ($vuln_type && $vuln_seq) {
		
			// build our query
			$query = "SELECT `vuln_type`, `vuln_seq` FROM `VULNERABILITIES` WHERE (`vuln_type` = '$vuln_type' and `vuln_seq` = '$vuln_seq')";
			
			// execute the query
			$this->db->query($query);
			
			// check for results
			if ( $this->db->queryOK() && $this->db->num_rows() ) { return 1; } 
			else { return 0; }

		}
		
		// otherwise don't even bother checking it
		else { 
		     return 0; 
		}
		
	} // vulnExists()
	

	public function getVuln($vuln_type = NULL, $vuln_seq = NULL) {
		
	  // make sure we have a positive, non-zero vuln_seq
	  if ($this->vulnExists($vuln_type, $vuln_seq)) {
		
			// designate our retrieval query
			$query = "SELECT * FROM `VULNERABILITIES` WHERE (`vuln_type` = '$vuln_type' and `vuln_seq` = '$vuln_seq')";
		
			// execute the query
			$this->db->query($query);
		
			// if we get a hit, store the information
			if ($this->db->num_rows() > 0) {
			
				// retrieve the results query
				$results = $this->db->fetch_assoc();
			
				// store the results locally

                $this->vuln_seq                                           = $results['vuln_seq'];
                $this->vuln_type                                          = $results['vuln_type'];
                $this->vuln_desc_primary                                  = $results['vuln_desc_primary'];
                $this->vuln_desc_secondary                                = $results['vuln_desc_secondary'];
                $this->vuln_date_discovered                               = $results['vuln_date_discovered'];
                $this->vuln_date_modified                                 = $results['vuln_date_modified'];
                $this->vuln_date_published                                = $results['vuln_date_published'];
                $this->vuln_severity                                      = $results['vuln_severity'];
                $this->vuln_loss_availability                             = $results['vuln_loss_availability'];
                $this->vuln_loss_confidentiality                          = $results['vuln_loss_confidentiality'];
                $this->vuln_loss_integrity                                = $results['vuln_loss_integrity'];
                $this->vuln_loss_security_admin                           = $results['vuln_loss_security_admin'];
                $this->vuln_loss_security_user                            = $results['vuln_loss_security_user'];
                $this->vuln_loss_security_other                           = $results['vuln_loss_security_other'];
                $this->vuln_type_access                                   = $results['vuln_type_access'];
                $this->vuln_type_input                                    = $results['vuln_type_input'];
                $this->vuln_type_input_bound                              = $results['vuln_type_input_bound'];
                $this->vuln_type_input_buffer                             = $results['vuln_type_input_buffer'];
                $this->vuln_type_design                                   = $results['vuln_type_design'];
                $this->vuln_type_exception                                = $results['vuln_type_exception'];
                $this->vuln_type_environment                              = $results['vuln_type_environment'];
                $this->vuln_type_config                                   = $results['vuln_type_config'];
                $this->vuln_type_race                                     = $results['vuln_type_race'];
                $this->vuln_type_other                                    = $results['vuln_type_other'];
                $this->vuln_range_local                                   = $results['vuln_range_local'];
                $this->vuln_range_remote                                  = $results['vuln_range_remote'];
                $this->vuln_range_user                                    = $results['vuln_range_user'];
			
			} // this->db->fetch_assoc()
			
			// system not retrieved, clear out any potential values
			else {
			     $this->clearVuln(); 
			}
		} // if $vuln_seq

	} // getVuln()


		
	public function saveVuln(){

	    if ($this->vuln_seq && $this->vulnExists($this->vuln_seq)){
    	    $query = "UPDATE `VULNERABILITIES` SET ";    
            	    $query .= " `vuln_type`                                          = '$this->vuln_type', ";
            	    $query .= " `vuln_desc_primary`                                  = '$this->vuln_desc_primary', ";
            	    $query .= " `vuln_desc_secondary`                                = '$this->vuln_desc_secondary', ";
            	    $query .= " `vuln_date_discovered`                               = '$this->vuln_date_discovered', ";
            	    $query .= " `vuln_date_modified`                                 = '$this->vuln_date_modified', ";
            	    $query .= " `vuln_date_published`                                = '$this->vuln_date_published', ";
            	    $query .= " `vuln_severity`                                      = '$this->vuln_severity', ";
            	    $query .= " `vuln_loss_availability`                             = '$this->vuln_loss_availability', ";
            	    $query .= " `vuln_loss_confidentiality`                          = '$this->vuln_loss_confidentiality', ";
            	    $query .= " `vuln_loss_integrity`                                = '$this->vuln_loss_integrity', ";
            	    $query .= " `vuln_loss_security_admin`                           = '$this->vuln_loss_security_admin', ";
            	    $query .= " `vuln_loss_security_user`                            = '$this->vuln_loss_security_user', ";
            	    $query .= " `vuln_loss_security_other`                           = '$this->vuln_loss_security_other', ";
            	    $query .= " `vuln_type_access`                                   = '$this->vuln_type_access', ";
            	    $query .= " `vuln_type_input`                                    = '$this->vuln_type_input', ";
            	    $query .= " `vuln_type_input_bound`                              = '$this->vuln_type_input_bound', ";
            	    $query .= " `vuln_type_input_buffer`                             = '$this->vuln_type_input_buffer', ";
            	    $query .= " `vuln_type_design`                                   = '$this->vuln_type_design', ";
            	    $query .= " `vuln_type_exception`                                = '$this->vuln_type_exception', ";
            	    $query .= " `vuln_type_environment`                              = '$this->vuln_type_environment', ";
            	    $query .= " `vuln_type_config`                                   = '$this->vuln_type_config', ";
            	    $query .= " `vuln_type_race`                                     = '$this->vuln_type_race', ";
            	    $query .= " `vuln_type_other`                                    = '$this->vuln_type_other', ";
            	    $query .= " `vuln_range_local`                                   = '$this->vuln_range_local', ";
            	    $query .= " `vuln_range_remote`                                  = '$this->vuln_range_remote', ";
            	    $query .= " `vuln_range_user`                                    = '$this->vuln_range_user' ";	    
                    $query .= " WHERE `vuln_seq`                                     = '$this->vuln_seq' ";
	    }
	    else {
	       $query = "INSERT INTO `VULNERABILITIES` (
                            `vuln_type`, 
                            `vuln_desc_primary`, 
                            `vuln_desc_secondary`, 
                            `vuln_date_discovered`, 
                            `vuln_date_modified`, 
                            `vuln_date_published`, 
                            `vuln_severity`, 
                            `vuln_loss_availability`, 
                            `vuln_loss_confidentiality`, 
                            `vuln_loss_integrity`, 
                            `vuln_loss_security_admin`, 
                            `vuln_loss_security_user`, 
                            `vuln_loss_security_other`, 
                            `vuln_type_access`, 
                            `vuln_type_input`, 
                            `vuln_type_input_bound`, 
                            `vuln_type_input_buffer`, 
                            `vuln_type_design`, 
                            `vuln_type_exception`, 
                            `vuln_type_environment`, 
                            `vuln_type_config`, 
                            `vuln_type_race`, 
                            `vuln_type_other`, 
                            `vuln_range_local`, 
                            `vuln_range_remote`, 
                            `vuln_range_user`
                            ) VALUES (
                            '$this->vuln_type', 
                            '$this->vuln_desc_primary', 
                            '$this->vuln_desc_secondary', 
                            '$this->vuln_date_discovered', 
                            '$this->vuln_date_modified', 
                            '$this->vuln_date_published', 
                            '$this->vuln_severity', 
                            '$this->vuln_loss_availability', 
                            '$this->vuln_loss_confidentiality', 
                            '$this->vuln_loss_integrity', 
                            '$this->vuln_loss_security_admin', 
                            '$this->vuln_loss_security_user', 
                            '$this->vuln_loss_security_other', 
                            '$this->vuln_type_access', 
                            '$this->vuln_type_input', 
                            '$this->vuln_type_input_bound', 
                            '$this->vuln_type_input_buffer', 
                            '$this->vuln_type_design', 
                            '$this->vuln_type_exception', 
                            '$this->vuln_type_environment', 
                            '$this->vuln_type_config', 
                            '$this->vuln_type_race', 
                            '$this->vuln_type_other', 
                            '$this->vuln_range_local', 
                            '$this->vuln_range_remote', 
                            '$this->vuln_range_user'
                            )";
	    }
	    
    	// execute our query
    	$this->db->query($query);
    //echo($query);
    	if ($this->db->queryOK()) { 
    	   if (!$this->vuln_seq || !$this->vulnExists($this->vuln_seq)){
    	       $this->vuln_seq = $this->db->insert_id();
    	   }
    	   return 1; 
    	} 
    	else {
    	   return 0; 
    	}
	} //saveVuln()
	
	public function clearVuln() {
		
		// clear out (non-db) user values

        unset($this->vuln_seq);
        unset($this->vuln_type);
        unset($this->vuln_desc_primary);
        unset($this->vuln_desc_secondary);
        unset($this->vuln_date_discovered);
        unset($this->vuln_date_modified);
        unset($this->vuln_date_published);
        unset($this->vuln_severity);
        unset($this->vuln_loss_availability);
        unset($this->vuln_loss_confidentiality);
        unset($this->vuln_loss_integrity);
        unset($this->vuln_loss_security_admin);
        unset($this->vuln_loss_security_user);
        unset($this->vuln_loss_security_other);
        unset($this->vuln_type_access);
        unset($this->vuln_type_input);
        unset($this->vuln_type_input_bound);
        unset($this->vuln_type_input_buffer);
        unset($this->vuln_type_design);
        unset($this->vuln_type_exception);
        unset($this->vuln_type_environment);
        unset($this->vuln_type_config);
        unset($this->vuln_type_race);
        unset($this->vuln_type_other);
        unset($this->vuln_range_local);
        unset($this->vuln_range_remote);
        unset($this->vuln_range_user);
	} // clearVuln()


	public function deleteVuln() {

		// 
		// REMOVES VULNERABILITIES FROM DATABASE!
		// 

		// ensure that we have an open database connection
		if ($this->db) {

			// define our query
			$query = "DELETE FROM `VULNERABILITIES` WHERE (`vuln_seq` = '$this->vuln_seq')";

			// execute our query
			$this->db->query($query);

			// clear out the current object
			$this->clearVuln();
		
		} // $this->db

	} // deleteVuln()
	
	

	// -----------------------------------------------------------------------
	// 
	// VARIABLE ACCESS METHODS
	// 
	// -----------------------------------------------------------------------


    public function getVulnSeq()                                           { return $this->vuln_seq; }
    public function getVulnType()                                          { return $this->vuln_type; }
    public function getVulnDescPrimary()                                   { return $this->vuln_desc_primary; }
    public function getVulnDescSecondary()                                 { return $this->vuln_desc_secondary; }
    public function getVulnDateDiscovered()                                { return $this->vuln_date_discovered; }
    public function getVulnDateModified()                                  { return $this->vuln_date_modified; }
    public function getVulnDatePublished()                                 { return $this->vuln_date_published; }
    public function getVulnSeverity()                                      { return $this->vuln_severity; }
    public function getVulnLossAvailability()                              { return $this->vuln_loss_availability; }
    public function getVulnLossConfidentiality()                           { return $this->vuln_loss_confidentiality; }
    public function getVulnLossIntegrity()                                 { return $this->vuln_loss_integrity; }
    public function getVulnLossSecurityAdmin()                             { return $this->vuln_loss_security_admin; }
    public function getVulnLossSecurityUser()                              { return $this->vuln_loss_security_user; }
    public function getVulnLossSecurityOther()                             { return $this->vuln_loss_security_other; }
    public function getVulnTypeAccess()                                    { return $this->vuln_type_access; }
    public function getVulnTypeInput()                                     { return $this->vuln_type_input; }
    public function getVulnTypeInputBound()                                { return $this->vuln_type_input_bound; }
    public function getVulnTypeInputBuffer()                               { return $this->vuln_type_input_buffer; }
    public function getVulnTypeDesign()                                    { return $this->vuln_type_design; }
    public function getVulnTypeException()                                 { return $this->vuln_type_exception; }
    public function getVulnTypeEnvironment()                               { return $this->vuln_type_environment; }
    public function getVulnTypeConfig()                                    { return $this->vuln_type_config; }
    public function getVulnTypeRace()                                      { return $this->vuln_type_race; }
    public function getVulnTypeOther()                                     { return $this->vuln_type_other; }
    public function getVulnRangeLocal()                                    { return $this->vuln_range_local; }
    public function getVulnRangeRemote()                                   { return $this->vuln_range_remote; }
    public function getVulnRangeUser()                                     { return $this->vuln_range_user; }

	public function getValidVulnSeqs($offset = 0, $limit = NULL) {
		
		// array to store user ids
		$id_array = array();

		// create our query
		$query = "SELECT `vuln_seq` FROM `VULNERABILITIES`";

		// add in our offset and limit if a limit is provided
		if ($limit) { $query .= " LIMIT $offset, $limit";  }

		// execute the query
		$this->db->query($query);
	
		// evaluate the results
		if ($this->db->queryOK()) {
			
			// push the values onto the array
			while ($id = $this->db->fetch_array()) { array_push($id_array, $id[0]); }
			
		}
		
		// return the array of vuln_seqs
		return $id_array;
		
	} // getValidVulnSeqs
	
	// -----------------------------------------------------------------------
	// 
	// VARIABLE MODIFY METHODS
	// 
	// -----------------------------------------------------------------------


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
    
    
    public function setVulnDescPrimary($vuln_desc_primary  =  NULL){ 
		// error check input (by schema)
		if (strlen($vuln_desc_primary) >= 0){
            $this->vuln_desc_primary = $vuln_desc_primary;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setVulnDescPrimary()
    
    
    public function setVulnDescSecondary($vuln_desc_secondary  =  NULL){ 
		// error check input (by schema)
		if (strlen($vuln_desc_secondary) >= 0){
            $this->vuln_desc_secondary = $vuln_desc_secondary;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setVulnDescSecondary()
    
    
    public function setVulnDateDiscovered($vuln_date_discovered  =  NULL){ 
		// error check input (by schema)
		if (strlen($vuln_date_discovered) >= 0){
            $this->vuln_date_discovered = $vuln_date_discovered;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setVulnDateDiscovered()
    
    
    public function setVulnDateModified($vuln_date_modified  =  NULL){ 
		// error check input (by schema)
		if (strlen($vuln_date_modified) >= 0){
            $this->vuln_date_modified = $vuln_date_modified;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setVulnDateModified()
    
    
    public function setVulnDatePublished($vuln_date_published  =  NULL){ 
		// error check input (by schema)
		if (strlen($vuln_date_published) >= 0){
            $this->vuln_date_published = $vuln_date_published;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setVulnDatePublished()
    
    
    public function setVulnSeverity($vuln_severity  =  NULL){ 
		// error check input (by schema)
		if (strlen($vuln_severity) <= 10){
            $this->vuln_severity = $vuln_severity;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setVulnSeverity()
    
    
    public function setVulnLossAvailability($vuln_loss_availability  =  NULL){ 
		// error check input (by schema)
		if (strlen($vuln_loss_availability) <= 1){
            $this->vuln_loss_availability = $vuln_loss_availability;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setVulnLossAvailability()
    
    
    public function setVulnLossConfidentiality($vuln_loss_confidentiality  =  NULL){ 
		// error check input (by schema)
		if (strlen($vuln_loss_confidentiality) <= 1){
            $this->vuln_loss_confidentiality = $vuln_loss_confidentiality;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setVulnLossConfidentiality()
    
    
    public function setVulnLossIntegrity($vuln_loss_integrity  =  NULL){ 
		// error check input (by schema)
		if (strlen($vuln_loss_integrity) <= 1){
            $this->vuln_loss_integrity = $vuln_loss_integrity;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setVulnLossIntegrity()
    
    
    public function setVulnLossSecurityAdmin($vuln_loss_security_admin  =  NULL){ 
		// error check input (by schema)
		if (strlen($vuln_loss_security_admin) <= 1){
            $this->vuln_loss_security_admin = $vuln_loss_security_admin;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setVulnLossSecurityAdmin()
    
    
    public function setVulnLossSecurityUser($vuln_loss_security_user  =  NULL){ 
		// error check input (by schema)
		if (strlen($vuln_loss_security_user) <= 1){
            $this->vuln_loss_security_user = $vuln_loss_security_user;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setVulnLossSecurityUser()
    
    
    public function setVulnLossSecurityOther($vuln_loss_security_other  =  NULL){ 
		// error check input (by schema)
		if (strlen($vuln_loss_security_other) <= 1){
            $this->vuln_loss_security_other = $vuln_loss_security_other;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setVulnLossSecurityOther()
    
    
    public function setVulnTypeAccess($vuln_type_access  =  NULL){ 
		// error check input (by schema)
		if (strlen($vuln_type_access) <= 1){
            $this->vuln_type_access = $vuln_type_access;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setVulnTypeAccess()
    
    
    public function setVulnTypeInput($vuln_type_input  =  NULL){ 
		// error check input (by schema)
		if (strlen($vuln_type_input) <= 1){
            $this->vuln_type_input = $vuln_type_input;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setVulnTypeInput()
    
    
    public function setVulnTypeInputBound($vuln_type_input_bound  =  NULL){ 
		// error check input (by schema)
		if (strlen($vuln_type_input_bound) <= 1){
            $this->vuln_type_input_bound = $vuln_type_input_bound;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setVulnTypeInputBound()
    
    
    public function setVulnTypeInputBuffer($vuln_type_input_buffer  =  NULL){ 
		// error check input (by schema)
		if (strlen($vuln_type_input_buffer) <= 1){
            $this->vuln_type_input_buffer = $vuln_type_input_buffer;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setVulnTypeInputBuffer()
    
    
    public function setVulnTypeDesign($vuln_type_design  =  NULL){ 
		// error check input (by schema)
		if (strlen($vuln_type_design) <= 1){
            $this->vuln_type_design = $vuln_type_design;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setVulnTypeDesign()
    
    
    public function setVulnTypeException($vuln_type_exception  =  NULL){ 
		// error check input (by schema)
		if (strlen($vuln_type_exception) <= 1){
            $this->vuln_type_exception = $vuln_type_exception;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setVulnTypeException()
    
    
    public function setVulnTypeEnvironment($vuln_type_environment  =  NULL){ 
		// error check input (by schema)
		if (strlen($vuln_type_environment) <= 1){
            $this->vuln_type_environment = $vuln_type_environment;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setVulnTypeEnvironment()
    
    
    public function setVulnTypeConfig($vuln_type_config  =  NULL){ 
		// error check input (by schema)
		if (strlen($vuln_type_config) <= 1){
            $this->vuln_type_config = $vuln_type_config;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setVulnTypeConfig()
    
    
    public function setVulnTypeRace($vuln_type_race  =  NULL){ 
		// error check input (by schema)
		if (strlen($vuln_type_race) <= 1){
            $this->vuln_type_race = $vuln_type_race;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setVulnTypeRace()
    
    
    public function setVulnTypeOther($vuln_type_other  =  NULL){ 
		// error check input (by schema)
		if (strlen($vuln_type_other) <= 1){
            $this->vuln_type_other = $vuln_type_other;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setVulnTypeOther()
    
    
    public function setVulnRangeLocal($vuln_range_local  =  NULL){ 
		// error check input (by schema)
		if (strlen($vuln_range_local) <= 1){
            $this->vuln_range_local = $vuln_range_local;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setVulnRangeLocal()
    
    
    public function setVulnRangeRemote($vuln_range_remote  =  NULL){ 
		// error check input (by schema)
		if (strlen($vuln_range_remote) <= 1){
            $this->vuln_range_remote = $vuln_range_remote;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setVulnRangeRemote()
    
    
    public function setVulnRangeUser($vuln_range_user  =  NULL){ 
		// error check input (by schema)
		if (strlen($vuln_range_user) <= 1){
            $this->vuln_range_user = $vuln_range_user;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setVulnRangeUser()
    
    

} // class Vuln
?>
