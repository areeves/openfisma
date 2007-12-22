<?PHP

// 
// INCLUDES
// 
require_once('Database.class.php');


//
// CLASS DEFINITION
// 

class Poam {

	// -----------------------------------------------------------------------
	//
	// VARIABLES
	//
	// -----------------------------------------------------------------------


    private $db;

    private $poam_id;
    private $finding_id;
    private $legacy_poam_id;
    private $poam_is_repeat;
    private $poam_previous_audits;
    private $poam_type;
    private $poam_status;
    private $poam_blscr;
    private $poam_created_by;
    private $poam_modified_by;
    private $poam_closed_by;
    private $poam_date_created;
    private $poam_date_modified;
    private $poam_date_closed;
    private $poam_action_owner;
    private $poam_action_suggested;
    private $poam_action_planned;
    private $poam_action_status;
    private $poam_action_approved_by;
    private $poam_cmeasure;
    private $poam_cmeasure_effectiveness;
    private $poam_cmeasure_justification;
    private $poam_action_resources;
    private $poam_action_date_est;
    private $poam_action_date_actual;
    private $poam_threat_source;
    private $poam_threat_level;
    private $poam_threat_justification;


	// -----------------------------------------------------------------------
	// 
	// CLASS METHODS
	// 
	// -----------------------------------------------------------------------

	public function __construct($db, $poam_id = NULL) {

		// utilize an existing database connection
		$this->db = $db;

		// get Poam information or create a new one if none specified
		if ($poam_id) {
		  $this->getPoam($poam_id); 
		}

	} // __construct()
	

	public function __destruct() {

		// clear out the poam_id to prevent any updates
		$this->poam_id = 0;

	} // __destruct()


 	public function __ToString() {
 		
 		// return a string of information
 		return	$this->db->__ToString().
 			'<pre>'.
			'<br>POAMS'.
			'<br>------'.

            '<br>poam_id                                           : '.$this->poam_id.
            '<br>finding_id                                        : '.$this->finding_id.
            '<br>legacy_poam_id                                    : '.$this->legacy_poam_id.
            '<br>poam_is_repeat                                    : '.$this->poam_is_repeat.
            '<br>poam_previous_audits                              : '.$this->poam_previous_audits.
            '<br>poam_type                                         : '.$this->poam_type.
            '<br>poam_status                                       : '.$this->poam_status.
            '<br>poam_blscr                                        : '.$this->poam_blscr.
            '<br>poam_created_by                                   : '.$this->poam_created_by.
            '<br>poam_modified_by                                  : '.$this->poam_modified_by.
            '<br>poam_closed_by                                    : '.$this->poam_closed_by.
            '<br>poam_date_created                                 : '.$this->poam_date_created.
            '<br>poam_date_modified                                : '.$this->poam_date_modified.
            '<br>poam_date_closed                                  : '.$this->poam_date_closed.
            '<br>poam_action_owner                                 : '.$this->poam_action_owner.
            '<br>poam_action_suggested                             : '.$this->poam_action_suggested.
            '<br>poam_action_planned                               : '.$this->poam_action_planned.
            '<br>poam_action_status                                : '.$this->poam_action_status.
            '<br>poam_action_approved_by                           : '.$this->poam_action_approved_by.
            '<br>poam_cmeasure                                     : '.$this->poam_cmeasure.
            '<br>poam_cmeasure_effectiveness                       : '.$this->poam_cmeasure_effectiveness.
            '<br>poam_cmeasure_justification                       : '.$this->poam_cmeasure_justification.
            '<br>poam_action_resources                             : '.$this->poam_action_resources.
            '<br>poam_action_date_est                              : '.$this->poam_action_date_est.
            '<br>poam_action_date_actual                           : '.$this->poam_action_date_actual.
            '<br>poam_threat_source                                : '.$this->poam_threat_source.
            '<br>poam_threat_level                                 : '.$this->poam_threat_level.
            '<br>poam_threat_justification                         : '.$this->poam_threat_justification.
			'<br></pre>';
 		
 	} // __ToString()
 	

	// -----------------------------------------------------------------------
	// 
	// CLASS MANIPULATION METHODS
	// 
	// -----------------------------------------------------------------------
	
	public function poamExists($poam_id = NULL) {
		
		// make sure we have a positive, non-zero poam_id
		if ($poam_id) {
		
			// build our query
			$query = "SELECT `poam_id` FROM `POAMS` WHERE (`poam_id` = '$poam_id')";
			
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
		
	} // poamExists()
	

	public function getPoam($poam_id = NULL) {
		
		// make sure we have a positive, non-zero poam_id
		if ($poam_id && $this->poamExists($poam_id)) {
		
			// designate our retrieval query
			$query = "SELECT * FROM `POAMS` WHERE (`poam_id` = '$poam_id')";
		
			// execute the query
			$this->db->query($query);
		
			// if we get a hit, store the information
			if ($this->db->num_rows() > 0) {
			
				// retrieve the results query
				$results = $this->db->fetch_assoc();
			
				// store the results locally

                $this->poam_id                                            = $results['poam_id'];
                $this->finding_id                                         = $results['finding_id'];
                $this->legacy_poam_id                                     = $results['legacy_poam_id'];
                $this->poam_is_repeat                                     = $results['poam_is_repeat'];
                $this->poam_previous_audits                               = $results['poam_previous_audits'];
                $this->poam_type                                          = $results['poam_type'];
                $this->poam_status                                        = $results['poam_status'];
                $this->poam_blscr                                         = $results['poam_blscr'];
                $this->poam_created_by                                    = $results['poam_created_by'];
                $this->poam_modified_by                                   = $results['poam_modified_by'];
                $this->poam_closed_by                                     = $results['poam_closed_by'];
                $this->poam_date_created                                  = $results['poam_date_created'];
                $this->poam_date_modified                                 = $results['poam_date_modified'];
                $this->poam_date_closed                                   = $results['poam_date_closed'];
                $this->poam_action_owner                                  = $results['poam_action_owner'];
                $this->poam_action_suggested                              = $results['poam_action_suggested'];
                $this->poam_action_planned                                = $results['poam_action_planned'];
                $this->poam_action_status                                 = $results['poam_action_status'];
                $this->poam_action_approved_by                            = $results['poam_action_approved_by'];
                $this->poam_cmeasure                                      = $results['poam_cmeasure'];
                $this->poam_cmeasure_effectiveness                        = $results['poam_cmeasure_effectiveness'];
                $this->poam_cmeasure_justification                        = $results['poam_cmeasure_justification'];
                $this->poam_action_resources                              = $results['poam_action_resources'];
                $this->poam_action_date_est                               = $results['poam_action_date_est'];
                $this->poam_action_date_actual                            = $results['poam_action_date_actual'];
                $this->poam_threat_source                                 = $results['poam_threat_source'];
                $this->poam_threat_level                                  = $results['poam_threat_level'];
                $this->poam_threat_justification                          = $results['poam_threat_justification'];
			
			} // this->db->fetch_assoc()
			
			// system not retrieved, clear out any potential values
			else {
			     $this->clearPoam(); 
			}
		} // if $poam_id

	} // getPoam()


		
	public function savePoam(){
	
	    if ($this->poam_id && $this->poamExists($this->poam_id)){
    	    $query = "UPDATE `POAMS` SET ";    
            	    $query .= " `finding_id`                                         = '$this->finding_id', ";
            	    $query .= " `legacy_poam_id`                                     = '$this->legacy_poam_id', ";
            	    $query .= " `poam_is_repeat`                                     = '$this->poam_is_repeat', ";
            	    $query .= " `poam_previous_audits`                               = '$this->poam_previous_audits', ";
            	    $query .= " `poam_type`                                          = '$this->poam_type', ";
            	    $query .= " `poam_status`                                        = '$this->poam_status', ";
            	    $query .= " `poam_blscr`                                         = '$this->poam_blscr', ";
            	    $query .= " `poam_created_by`                                    = '$this->poam_created_by', ";
            	    $query .= " `poam_modified_by`                                   = '$this->poam_modified_by', ";
            	    $query .= " `poam_closed_by`                                     = '$this->poam_closed_by', ";
            	    $query .= " `poam_date_created`                                  = '$this->poam_date_created', ";
            	    $query .= " `poam_date_modified`                                 = '$this->poam_date_modified', ";
            	    $query .= " `poam_date_closed`                                   = '$this->poam_date_closed', ";
            	    $query .= " `poam_action_owner`                                  = '$this->poam_action_owner', ";
            	    $query .= " `poam_action_suggested`                              = '$this->poam_action_suggested', ";
            	    $query .= " `poam_action_planned`                                = '$this->poam_action_planned', ";
            	    $query .= " `poam_action_status`                                 = '$this->poam_action_status', ";
            	    $query .= " `poam_action_approved_by`                            = '$this->poam_action_approved_by', ";
            	    $query .= " `poam_cmeasure`                                      = '$this->poam_cmeasure', ";
            	    $query .= " `poam_cmeasure_effectiveness`                        = '$this->poam_cmeasure_effectiveness', ";
            	    $query .= " `poam_cmeasure_justification`                        = '$this->poam_cmeasure_justification', ";
            	    $query .= " `poam_action_resources`                              = '$this->poam_action_resources', ";
            	    $query .= " `poam_action_date_est`                               = '$this->poam_action_date_est', ";
            	    $query .= " `poam_action_date_actual`                            = '$this->poam_action_date_actual', ";
            	    $query .= " `poam_threat_source`                                 = '$this->poam_threat_source', ";
            	    $query .= " `poam_threat_level`                                  = '$this->poam_threat_level', ";
            	    $query .= " `poam_threat_justification`                          = '$this->poam_threat_justification' ";	    
                    $query .= " WHERE `poam_id`                                      = '$this->poam_id' ";
	    }
	    else {
	       $query = "INSERT INTO `POAMS` (
                            `finding_id`, 
                            `legacy_poam_id`, 
                            `poam_is_repeat`, 
                            `poam_previous_audits`, 
                            `poam_type`, 
                            `poam_status`, 
                            `poam_blscr`, 
                            `poam_created_by`, 
                            `poam_modified_by`, 
                            `poam_closed_by`, 
                            `poam_date_created`, 
                            `poam_date_modified`, 
                            `poam_date_closed`, 
                            `poam_action_owner`, 
                            `poam_action_suggested`, 
                            `poam_action_planned`, 
                            `poam_action_status`, 
                            `poam_action_approved_by`, 
                            `poam_cmeasure`, 
                            `poam_cmeasure_effectiveness`, 
                            `poam_cmeasure_justification`, 
                            `poam_action_resources`, 
                            `poam_action_date_est`, 
                            `poam_action_date_actual`, 
                            `poam_threat_source`, 
                            `poam_threat_level`, 
                            `poam_threat_justification`
                            ) VALUES (
                            '$this->finding_id', 
                            '$this->legacy_poam_id', 
                            '$this->poam_is_repeat', 
                            '$this->poam_previous_audits', 
                            '$this->poam_type', 
                            '$this->poam_status', 
                            '$this->poam_blscr', 
                            '$this->poam_created_by', 
                            '$this->poam_modified_by', 
                            '$this->poam_closed_by', 
                            '$this->poam_date_created', 
                            '$this->poam_date_modified', 
                            '$this->poam_date_closed', 
                            '$this->poam_action_owner', 
                            '$this->poam_action_suggested', 
                            '$this->poam_action_planned', 
                            '$this->poam_action_status', 
                            '$this->poam_action_approved_by', 
                            '$this->poam_cmeasure', 
                            '$this->poam_cmeasure_effectiveness', 
                            '$this->poam_cmeasure_justification', 
                            '$this->poam_action_resources', 
                            '$this->poam_action_date_est', 
                            '$this->poam_action_date_actual', 
                            '$this->poam_threat_source', 
                            '$this->poam_threat_level', 
                            '$this->poam_threat_justification'
                            )";
	    }
	    
    	// execute our query
    	$this->db->query($query);
    
    	if ($this->db->queryOK()) { 
    	   if (!$this->poam_id || !$this->poamExists($this->poam_id)){
    	       $this->poam_id = $this->db->insert_id();
    	   }
    	   return 1; 
    	} 
    	else {
    	   return 0; 
    	}
	} //savePoam()
	
	public function clearPoam() {
		
		// clear out (non-db) user values

        unset($this->poam_id);
        unset($this->finding_id);
        unset($this->legacy_poam_id);
        unset($this->poam_is_repeat);
        unset($this->poam_previous_audits);
        unset($this->poam_type);
        unset($this->poam_status);
        unset($this->poam_blscr);
        unset($this->poam_created_by);
        unset($this->poam_modified_by);
        unset($this->poam_closed_by);
        unset($this->poam_date_created);
        unset($this->poam_date_modified);
        unset($this->poam_date_closed);
        unset($this->poam_action_owner);
        unset($this->poam_action_suggested);
        unset($this->poam_action_planned);
        unset($this->poam_action_status);
        unset($this->poam_action_approved_by);
        unset($this->poam_cmeasure);
        unset($this->poam_cmeasure_effectiveness);
        unset($this->poam_cmeasure_justification);
        unset($this->poam_action_resources);
        unset($this->poam_action_date_est);
        unset($this->poam_action_date_actual);
        unset($this->poam_threat_source);
        unset($this->poam_threat_level);
        unset($this->poam_threat_justification);
	} // clearPoam()


	public function deletePoam() {

		// 
		// REMOVES POAMS FROM DATABASE!
		// 

		// ensure that we have an open database connection
		if ($this->db) {

			// define our query
			$query = "DELETE FROM `POAMS` WHERE (`poam_id` = '$this->poam_id')";

			// execute our query
			$this->db->query($query);

			// clear out the current object
			$this->clearPoam();
		
		} // $this->db

	} // deletePoam()
	
	

	// -----------------------------------------------------------------------
	// 
	// VARIABLE ACCESS METHODS
	// 
	// -----------------------------------------------------------------------


    public function getPoamId()                                            { return $this->poam_id; }
    public function getFindingId()                                         { return $this->finding_id; }
    public function getLegacyPoamId()                                      { return $this->legacy_poam_id; }
    public function getPoamIsRepeat()                                      { return $this->poam_is_repeat; }
    public function getPoamPreviousAudits()                                { return $this->poam_previous_audits; }
    public function getPoamType()                                          { return $this->poam_type; }
    public function getPoamStatus()                                        { return $this->poam_status; }
    public function getPoamBlscr()                                         { return $this->poam_blscr; }
    public function getPoamCreatedBy()                                     { return $this->poam_created_by; }
    public function getPoamModifiedBy()                                    { return $this->poam_modified_by; }
    public function getPoamClosedBy()                                      { return $this->poam_closed_by; }
    public function getPoamDateCreated()                                   { return $this->poam_date_created; }
    public function getPoamDateModified()                                  { return $this->poam_date_modified; }
    public function getPoamDateClosed()                                    { return $this->poam_date_closed; }
    public function getPoamActionOwner()                                   { return $this->poam_action_owner; }
    public function getPoamActionSuggested()                               { return $this->poam_action_suggested; }
    public function getPoamActionPlanned()                                 { return $this->poam_action_planned; }
    public function getPoamActionStatus()                                  { return $this->poam_action_status; }
    public function getPoamActionApprovedBy()                              { return $this->poam_action_approved_by; }
    public function getPoamCmeasure()                                      { return $this->poam_cmeasure; }
    public function getPoamCmeasureEffectiveness()                         { return $this->poam_cmeasure_effectiveness; }
    public function getPoamCmeasureJustification()                         { return $this->poam_cmeasure_justification; }
    public function getPoamActionResources()                               { return $this->poam_action_resources; }
    public function getPoamActionDateEst()                                 { return $this->poam_action_date_est; }
    public function getPoamActionDateActual()                              { return $this->poam_action_date_actual; }
    public function getPoamThreatSource()                                  { return $this->poam_threat_source; }
    public function getPoamThreatLevel()                                   { return $this->poam_threat_level; }
    public function getPoamThreatJustification()                           { return $this->poam_threat_justification; }

    
	public function getValidPoamIds($offset = 0, $limit = NULL) {
		
		// array to store poam ids
		$id_array = array();

		// create our query
		$query = "SELECT `poam_id` FROM `POAMS`";

		// add in our offset and limit if a limit is provided
		if ($limit) { $query .= " LIMIT $offset, $limit";  }

		// execute the query
		$this->db->query($query);
	
		// evaluate the results
		if ($this->db->queryOK()) {
			
			// push the values onto the array
			while ($id = $this->db->fetch_array()) { array_push($id_array, $id[0]); }
			
		}
		
		// return the array of poam_ids
		return $id_array;
		
	} // getValidPoamIds
	
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
    
    
    public function setLegacyPoamId($legacy_poam_id  =  NULL){ 
		// error check input (by schema)
		if (strlen($legacy_poam_id) <= 32){
            $this->legacy_poam_id = $legacy_poam_id;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setLegacyPoamId()
    
    
    public function setPoamIsRepeat($poam_is_repeat  =  NULL){ 
		// error check input (by schema)
		if (strlen($poam_is_repeat) <= 1){
            $this->poam_is_repeat = $poam_is_repeat;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setPoamIsRepeat()
    
    
    public function setPoamPreviousAudits($poam_previous_audits  =  NULL){ 
		// error check input (by schema)
		if (strlen($poam_previous_audits) >= 0){
            $this->poam_previous_audits = $poam_previous_audits;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setPoamPreviousAudits()
    
    
    public function setPoamType($poam_type  =  NULL){ 
		// error check input (by schema)
		if (in_array($poam_type, array('NONE','CAP','FP','AR')) ){
            $this->poam_type = $poam_type;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setPoamType()
    
    
    public function setPoamStatus($poam_status  =  NULL){ 
		// error check input (by schema)
		if (in_array($poam_status, array('OPEN','EN','EP','ES','CLOSED')) ){
            $this->poam_status = $poam_status;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setPoamStatus()
    
    
    public function setPoamBlscr($poam_blscr  =  NULL){ 
		// error check input (by schema)
		if (strlen($poam_blscr) <= 5){
            $this->poam_blscr = $poam_blscr;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setPoamBlscr()
    
    
    public function setPoamCreatedBy($poam_created_by  =  NULL){ 
		// error check input (by schema)
		if (strlen($poam_created_by) <= 10){
            $this->poam_created_by = $poam_created_by;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setPoamCreatedBy()
    
    
    public function setPoamModifiedBy($poam_modified_by  =  NULL){ 
		// error check input (by schema)
		if (strlen($poam_modified_by) <= 10){
            $this->poam_modified_by = $poam_modified_by;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setPoamModifiedBy()
    
    
    public function setPoamClosedBy($poam_closed_by  =  NULL){ 
		// error check input (by schema)
		if (strlen($poam_closed_by) <= 10){
            $this->poam_closed_by = $poam_closed_by;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setPoamClosedBy()
    
    
    public function setPoamDateCreated($poam_date_created  =  NULL){ 
		// error check input (by schema)
		if (strlen($poam_date_created) >= 0){
            $this->poam_date_created = $poam_date_created;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setPoamDateCreated()
    
    
    public function setPoamDateModified($poam_date_modified  =  NULL){ 
		// error check input (by schema)
		if (strlen($poam_date_modified) >= 0){
            $this->poam_date_modified = $poam_date_modified;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setPoamDateModified()
    
    
    public function setPoamDateClosed($poam_date_closed  =  NULL){ 
		// error check input (by schema)
		if (strlen($poam_date_closed) >= 0){
            $this->poam_date_closed = $poam_date_closed;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setPoamDateClosed()
    
    
    public function setPoamActionOwner($poam_action_owner  =  NULL){ 
		// error check input (by schema)
		if (strlen($poam_action_owner) <= 10){
            $this->poam_action_owner = $poam_action_owner;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setPoamActionOwner()
    
    
    public function setPoamActionSuggested($poam_action_suggested  =  NULL){ 
		// error check input (by schema)
		if (strlen($poam_action_suggested) >= 0){
            $this->poam_action_suggested = $poam_action_suggested;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setPoamActionSuggested()
    
    
    public function setPoamActionPlanned($poam_action_planned  =  NULL){ 
		// error check input (by schema)
		if (strlen($poam_action_planned) >= 0){
            $this->poam_action_planned = $poam_action_planned;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setPoamActionPlanned()
    
    
    public function setPoamActionStatus($poam_action_status  =  NULL){ 
		// error check input (by schema)
		if (in_array($poam_action_status, array('NONE','APPROVED','DENIED')) ){
            $this->poam_action_status = $poam_action_status;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setPoamActionStatus()
    
    
    public function setPoamActionApprovedBy($poam_action_approved_by  =  NULL){ 
		// error check input (by schema)
		if (strlen($poam_action_approved_by) <= 10){
            $this->poam_action_approved_by = $poam_action_approved_by;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setPoamActionApprovedBy()
    
    
    public function setPoamCmeasure($poam_cmeasure  =  NULL){ 
		// error check input (by schema)
		if (strlen($poam_cmeasure) >= 0){
            $this->poam_cmeasure = $poam_cmeasure;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setPoamCmeasure()
    
    
    public function setPoamCmeasureEffectiveness($poam_cmeasure_effectiveness  =  NULL){ 
		// error check input (by schema)
		if (in_array($poam_cmeasure_effectiveness, array('NONE','LOW','MODERATE','HIGH')) ){
            $this->poam_cmeasure_effectiveness = $poam_cmeasure_effectiveness;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setPoamCmeasureEffectiveness()
    
    
    public function setPoamCmeasureJustification($poam_cmeasure_justification  =  NULL){ 
		// error check input (by schema)
		if (strlen($poam_cmeasure_justification) >= 0){
            $this->poam_cmeasure_justification = $poam_cmeasure_justification;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setPoamCmeasureJustification()
    
    
    public function setPoamActionResources($poam_action_resources  =  NULL){ 
		// error check input (by schema)
		if (strlen($poam_action_resources) >= 0){
            $this->poam_action_resources = $poam_action_resources;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setPoamActionResources()
    
    
    public function setPoamActionDateEst($poam_action_date_est  =  NULL){ 
		// error check input (by schema)
		if (strlen($poam_action_date_est) >= 0){
            $this->poam_action_date_est = $poam_action_date_est;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setPoamActionDateEst()
    
    
    public function setPoamActionDateActual($poam_action_date_actual  =  NULL){ 
		// error check input (by schema)
		if (strlen($poam_action_date_actual) >= 0){
            $this->poam_action_date_actual = $poam_action_date_actual;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setPoamActionDateActual()
    
    
    public function setPoamThreatSource($poam_threat_source  =  NULL){ 
		// error check input (by schema)
		if (strlen($poam_threat_source) >= 0){
            $this->poam_threat_source = $poam_threat_source;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setPoamThreatSource()
    
    
    public function setPoamThreatLevel($poam_threat_level  =  NULL){ 
		// error check input (by schema)
		if (in_array($poam_threat_level, array('NONE','LOW','MODERATE','HIGH')) ){
            $this->poam_threat_level = $poam_threat_level;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setPoamThreatLevel()
    
    
    public function setPoamThreatJustification($poam_threat_justification  =  NULL){ 
		// error check input (by schema)
		if (strlen($poam_threat_justification) >= 0){
            $this->poam_threat_justification = $poam_threat_justification;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setPoamThreatJustification()
    
    

} // class Poam
?>
