<?PHP

// 
// INCLUDES
// 
require_once('Database.class.php');


//
// CLASS DEFINITION
// 

class PoamEvidence {

	// -----------------------------------------------------------------------
	//
	// VARIABLES
	//
	// -----------------------------------------------------------------------


    private $db;

    private $ev_id;
    private $poam_id;
    private $ev_submission;
    private $ev_submitted_by;
    private $ev_date_submitted;
    private $ev_sso_evaluation;
    private $ev_date_sso_evaluation;
    private $ev_fsa_evaluation;
    private $ev_date_fsa_evaluation;


	// -----------------------------------------------------------------------
	// 
	// CLASS METHODS
	// 
	// -----------------------------------------------------------------------

	public function __construct($db, $ev_id = NULL) {

		// utilize an existing database connection
		$this->db = $db;

		// get PoamEvidence information or create a new one if none specified
		if ($ev_id) {
		  $this->getPoamEvidence($ev_id); 
		}

	} // __construct()
	

	public function __destruct() {

		// clear out the ev_id to prevent any updates
		$this->ev_id = 0;

	} // __destruct()


 	public function __ToString() {
 		
 		// return a string of information
 		return	$this->db->__ToString().
 			'<pre>'.
			'<br>POAM_EVIDENCE'.
			'<br>------'.

            '<br>ev_id                                             : '.$this->ev_id.
            '<br>poam_id                                           : '.$this->poam_id.
            '<br>ev_submission                                     : '.$this->ev_submission.
            '<br>ev_submitted_by                                   : '.$this->ev_submitted_by.
            '<br>ev_date_submitted                                 : '.$this->ev_date_submitted.
            '<br>ev_sso_evaluation                                 : '.$this->ev_sso_evaluation.
            '<br>ev_date_sso_evaluation                            : '.$this->ev_date_sso_evaluation.
            '<br>ev_fsa_evaluation                                 : '.$this->ev_fsa_evaluation.
            '<br>ev_date_fsa_evaluation                            : '.$this->ev_date_fsa_evaluation.
			'<br></pre>';
 		
 	} // __ToString()
 	

	// -----------------------------------------------------------------------
	// 
	// CLASS MANIPULATION METHODS
	// 
	// -----------------------------------------------------------------------
	
	public function poamevidenceExists($ev_id = NULL) {
		
		// make sure we have a positive, non-zero ev_id
		if ($ev_id) {
		
			// build our query
			$query = "SELECT `ev_id` FROM `POAM_EVIDENCE` WHERE (`ev_id` = '$ev_id')";
			
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
		
	} // poamevidenceExists()
	

	public function getPoamEvidence($ev_id = NULL) {
		
		// make sure we have a positive, non-zero ev_id
		if ($ev_id && $this->poamevidenceExists($ev_id)) {
		
			// designate our retrieval query
			$query = "SELECT * FROM `POAM_EVIDENCE` WHERE (`ev_id` = '$ev_id')";
		
			// execute the query
			$this->db->query($query);
		
			// if we get a hit, store the information
			if ($this->db->num_rows() > 0) {
			
				// retrieve the results query
				$results = $this->db->fetch_assoc();
			
				// store the results locally

                $this->ev_id                                              = $results['ev_id'];
                $this->poam_id                                            = $results['poam_id'];
                $this->ev_submission                                      = $results['ev_submission'];
                $this->ev_submitted_by                                    = $results['ev_submitted_by'];
                $this->ev_date_submitted                                  = $results['ev_date_submitted'];
                $this->ev_sso_evaluation                                  = $results['ev_sso_evaluation'];
                $this->ev_date_sso_evaluation                             = $results['ev_date_sso_evaluation'];
                $this->ev_fsa_evaluation                                  = $results['ev_fsa_evaluation'];
                $this->ev_date_fsa_evaluation                             = $results['ev_date_fsa_evaluation'];
			
			} // this->db->fetch_assoc()
			
			// system not retrieved, clear out any potential values
			else {
			     $this->clearPoamEvidence(); 
			}
		} // if $ev_id

	} // getPoamEvidence()


		
	public function savePoamEvidence(){
	
	    if ($this->ev_id && $this->poamevidenceExists($this->ev_id)){
    	    $query = "UPDATE `POAM_EVIDENCE` SET ";    
            	    $query .= " `poam_id`                                            = '$this->poam_id', ";
            	    $query .= " `ev_submission`                                      = '$this->ev_submission', ";
            	    $query .= " `ev_submitted_by`                                    = '$this->ev_submitted_by', ";
            	    $query .= " `ev_date_submitted`                                  = '$this->ev_date_submitted', ";
            	    $query .= " `ev_sso_evaluation`                                  = '$this->ev_sso_evaluation', ";
            	    $query .= " `ev_date_sso_evaluation`                             = '$this->ev_date_sso_evaluation', ";
            	    $query .= " `ev_fsa_evaluation`                                  = '$this->ev_fsa_evaluation', ";
            	    $query .= " `ev_date_fsa_evaluation`                             = '$this->ev_date_fsa_evaluation' ";	    
                    $query .= " WHERE `ev_id`                                        = '$this->ev_id' ";
	    }
	    else {
	       $query = "INSERT INTO `POAM_EVIDENCE` (
                            `poam_id`, 
                            `ev_submission`, 
                            `ev_submitted_by`, 
                            `ev_date_submitted`, 
                            `ev_sso_evaluation`, 
                            `ev_date_sso_evaluation`, 
                            `ev_fsa_evaluation`, 
                            `ev_date_fsa_evaluation`
                            ) VALUES (
                            '$this->poam_id', 
                            '$this->ev_submission', 
                            '$this->ev_submitted_by', 
                            '$this->ev_date_submitted', 
                            '$this->ev_sso_evaluation', 
                            '$this->ev_date_sso_evaluation', 
                            '$this->ev_fsa_evaluation', 
                            '$this->ev_date_fsa_evaluation'
                            )";
	    }
	    
    	// execute our query
    	$this->db->query($query);
    
    	if ($this->db->queryOK()) { 
    	   if (!$this->ev_id || !$this->poamevidenceExists($this->ev_id)){
    	       $this->ev_id = $this->db->insert_id();
    	   }
    	   return 1; 
    	} 
    	else {
    	   return 0; 
    	}
	} //savePoamEvidence()
	
	public function clearPoamEvidence() {
		
		// clear out (non-db) user values

        unset($this->ev_id);
        unset($this->poam_id);
        unset($this->ev_submission);
        unset($this->ev_submitted_by);
        unset($this->ev_date_submitted);
        unset($this->ev_sso_evaluation);
        unset($this->ev_date_sso_evaluation);
        unset($this->ev_fsa_evaluation);
        unset($this->ev_date_fsa_evaluation);
	} // clearPoamEvidence()


	public function deletePoamEvidence() {

		// 
		// REMOVES POAM_EVIDENCE FROM DATABASE!
		// 

		// ensure that we have an open database connection
		if ($this->db) {

			// define our query
			$query = "DELETE FROM `POAM_EVIDENCE` WHERE (`ev_id` = '$this->ev_id')";

			// execute our query
			$this->db->query($query);

			// clear out the current object
			$this->clearPoamEvidence();
		
		} // $this->db

	} // deletePoamEvidence()
	
	

	// -----------------------------------------------------------------------
	// 
	// VARIABLE ACCESS METHODS
	// 
	// -----------------------------------------------------------------------


    public function getEvId()                                              { return $this->ev_id; }
    public function getPoamId()                                            { return $this->poam_id; }
    public function getEvSubmission()                                      { return $this->ev_submission; }
    public function getEvSubmittedBy()                                     { return $this->ev_submitted_by; }
    public function getEvDateSubmitted()                                   { return $this->ev_date_submitted; }
    public function getEvSsoEvaluation()                                   { return $this->ev_sso_evaluation; }
    public function getEvDateSsoEvaluation()                               { return $this->ev_date_sso_evaluation; }
    public function getEvFsaEvaluation()                                   { return $this->ev_fsa_evaluation; }
    public function getEvDateFsaEvaluation()                               { return $this->ev_date_fsa_evaluation; }

	// -----------------------------------------------------------------------
	// 
	// VARIABLE MODIFY METHODS
	// 
	// -----------------------------------------------------------------------

	public function getValidPoamEvidenceIds($offset = 0, $limit = NULL) {
		
		// array to store ev_id
		$id_array = array();

		// create our query
		$query = "SELECT ev_id from POAM_EVIDENCE";
		
		// add in our offset and limit if a limit is provided
		if ($limit) { $query .= " LIMIT $offset, $limit";  }		
		
		// execute the query
		$this->db->query($query);
	
		// evaluate the results
		if ($this->db->queryOK()) {
			
			// push the values onto the array
			while ($id = $this->db->fetch_array()) { array_push($id_array, $id[0]); }
			
		}
		
		// return the array of ev_id
		return $id_array;
		
	} // getValidPoamEvidenceIds

    public function setPoamId($poam_id  =  NULL){ 
		// error check input (by schema)
		if (strlen($poam_id) <= 10){
            $this->poam_id = $poam_id;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setPoamId()
    
    
    public function setEvSubmission($ev_submission  =  NULL){ 
		// error check input (by schema)
		if (strlen($ev_submission) <= 128){
            $this->ev_submission = $ev_submission;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setEvSubmission()
    
    
    public function setEvSubmittedBy($ev_submitted_by  =  NULL){ 
		// error check input (by schema)
		if (strlen($ev_submitted_by) <= 10){
            $this->ev_submitted_by = $ev_submitted_by;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setEvSubmittedBy()
    
    
    public function setEvDateSubmitted($ev_date_submitted  =  NULL){ 
		// error check input (by schema)
		if (strlen($ev_date_submitted) >= 0){
            $this->ev_date_submitted = $ev_date_submitted;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setEvDateSubmitted()
    
    
    public function setEvSsoEvaluation($ev_sso_evaluation  =  NULL){ 
		// error check input (by schema)
		if (in_array($ev_sso_evaluation, array('NONE','APPROVED','DENIED','EXCLUDED')) ){
            $this->ev_sso_evaluation = $ev_sso_evaluation;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setEvSsoEvaluation()
    
    
    public function setEvDateSsoEvaluation($ev_date_sso_evaluation  =  NULL){ 
		// error check input (by schema)
		if (strlen($ev_date_sso_evaluation) >= 0){
            $this->ev_date_sso_evaluation = $ev_date_sso_evaluation;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setEvDateSsoEvaluation()
    
    
    public function setEvFsaEvaluation($ev_fsa_evaluation  =  NULL){ 
		// error check input (by schema)
		if (in_array($ev_fsa_evaluation, array('NONE','APPROVED','DENIED','EXCLUDED')) ){
            $this->ev_fsa_evaluation = $ev_fsa_evaluation;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setEvFsaEvaluation()
    
    
    public function setEvDateFsaEvaluation($ev_date_fsa_evaluation  =  NULL){ 
		// error check input (by schema)
		if (strlen($ev_date_fsa_evaluation) >= 0){
            $this->ev_date_fsa_evaluation = $ev_date_fsa_evaluation;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setEvDateFsaEvaluation()
    
    

} // class PoamEvidence
?>
