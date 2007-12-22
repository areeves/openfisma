<?PHP

// 
// INCLUDES
// 
require_once('Database.class.php');


//
// CLASS DEFINITION
// 

class Blscr {

	// -----------------------------------------------------------------------
	//
	// VARIABLES
	//
	// -----------------------------------------------------------------------


    private $db;

    private $blscr_number;
    private $blscr_class;
    private $blscr_subclass;
    private $blscr_family;
    private $blscr_control;
    private $blscr_guidance;
    private $blscr_low;
    private $blscr_moderate;
    private $blscr_high;
    private $blscr_enhancements;
    private $blscr_supplement;


	// -----------------------------------------------------------------------
	// 
	// CLASS METHODS
	// 
	// -----------------------------------------------------------------------

	public function __construct($db, $blscr_number = NULL) {

		// utilize an existing database connection
		$this->db = $db;

		// get blscr information or create a new one if none specified
		if ($blscr_number) {
		  $this->getBlscr($blscr_number); 
		}

	} // __construct()
	

	public function __destruct() {

		// clear out the blscr_number to prevent any updates
		$this->blscr_number = 0;

	} // __destruct()


 	public function __ToString() {
 		
 		// return a string of information
 		return	$this->db->__ToString().
 			'<pre>'.
			'<br>BLSCR'.
			'<br>------'.

            '<br>blscr_number                                      : '.$this->blscr_number.
            '<br>blscr_class                                       : '.$this->blscr_class.
            '<br>blscr_subclass                                    : '.$this->blscr_subclass.
            '<br>blscr_family                                      : '.$this->blscr_family.
            '<br>blscr_control                                     : '.$this->blscr_control.
            '<br>blscr_guidance                                    : '.$this->blscr_guidance.
            '<br>blscr_low                                         : '.$this->blscr_low.
            '<br>blscr_moderate                                    : '.$this->blscr_moderate.
            '<br>blscr_high                                        : '.$this->blscr_high.
            '<br>blscr_enhancements                                : '.$this->blscr_enhancements.
            '<br>blscr_supplement                                  : '.$this->blscr_supplement.
			'<br></pre>';
 		
 	} // __ToString()
 	

	// -----------------------------------------------------------------------
	// 
	// CLASS MANIPULATION METHODS
	// 
	// -----------------------------------------------------------------------
	
	public function blscrExists($blscr_number = NULL) {
		
		// make sure we have a positive, non-zero blscr_number
		if ($blscr_number) {
		
			// build our query
			$query = "SELECT `blscr_number` FROM `BLSCR` WHERE (`blscr_number` = '$blscr_number')";
			
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
		
	} // blscrExists()
	

	public function getBlscr($blscr_number = NULL) {
		
		// make sure we have a positive, non-zero blscr_number
		if ($blscr_number && $this->blscrExists($blscr_number)) {
		
			// designate our retrieval query
			$query = "SELECT * FROM `BLSCR` WHERE (`blscr_number` = '$blscr_number')";
		
			// execute the query
			$this->db->query($query);
		
			// if we get a hit, store the information
			if ($this->db->num_rows() > 0) {
			
				// retrieve the results query
				$results = $this->db->fetch_assoc();
			
				// store the results locally

                $this->blscr_number                                       = $results['blscr_number'];
                $this->blscr_class                                        = $results['blscr_class'];
                $this->blscr_subclass                                     = $results['blscr_subclass'];
                $this->blscr_family                                       = $results['blscr_family'];
                $this->blscr_control                                      = $results['blscr_control'];
                $this->blscr_guidance                                     = $results['blscr_guidance'];
                $this->blscr_low                                          = $results['blscr_low'];
                $this->blscr_moderate                                     = $results['blscr_moderate'];
                $this->blscr_high                                         = $results['blscr_high'];
                $this->blscr_enhancements                                 = $results['blscr_enhancements'];
                $this->blscr_supplement                                   = $results['blscr_supplement'];
			
			} // this->db->fetch_assoc()
			
			// system not retrieved, clear out any potential values
			else {
			     $this->clearBlscr(); 
			}
		} // if $blscr_number

	} // getBlscr()


		
	public function saveBlscr(){
	
	    if ($this->blscr_number && $this->blscrExists($this->blscr_number)){
    	    $query = "UPDATE `BLSCR` SET ";    
            	    $query .= " `blscr_class`                                        = '$this->blscr_class', ";
            	    $query .= " `blscr_subclass`                                     = '$this->blscr_subclass', ";
            	    $query .= " `blscr_family`                                       = '$this->blscr_family', ";
            	    $query .= " `blscr_control`                                      = '$this->blscr_control', ";
            	    $query .= " `blscr_guidance`                                     = '$this->blscr_guidance', ";
            	    $query .= " `blscr_low`                                          = '$this->blscr_low', ";
            	    $query .= " `blscr_moderate`                                     = '$this->blscr_moderate', ";
            	    $query .= " `blscr_high`                                         = '$this->blscr_high', ";
            	    $query .= " `blscr_enhancements`                                 = '$this->blscr_enhancements', ";
            	    $query .= " `blscr_supplement`                                   = '$this->blscr_supplement' ";	    
                    $query .= " WHERE `blscr_number`                                 = '$this->blscr_number' ";
	    }
	    else {
	       $query = "INSERT INTO `BLSCR` (
                            `blscr_class`, 
                            `blscr_subclass`, 
                            `blscr_family`, 
                            `blscr_control`, 
                            `blscr_guidance`, 
                            `blscr_low`, 
                            `blscr_moderate`, 
                            `blscr_high`, 
                            `blscr_enhancements`, 
                            `blscr_supplement`, 
                            `blscr_number` 
                            ) VALUES (
                            '$this->blscr_class', 
                            '$this->blscr_subclass', 
                            '$this->blscr_family', 
                            '$this->blscr_control', 
                            '$this->blscr_guidance', 
                            '$this->blscr_low', 
                            '$this->blscr_moderate', 
                            '$this->blscr_high', 
                            '$this->blscr_enhancements', 
                            '$this->blscr_supplement',  
                            '$this->blscr_number'
                            )";
	    }
	    
    	// execute our query
    	$this->db->query($query);
    
    	if ($this->db->queryOK()) { 
    	   if (!$this->blscr_number || !$this->blscrExists($this->blscr_number)){
    	       //NOT A AUTO_INCREMENT COL !!
    	       //$this->blscr_number = $this->db->insert_id();
    	   }
    	   return 1; 
    	} 
    	else {
    	   return 0; 
    	}
	} //saveBlscr()
	
	public function clearBlscr() {
		
		// clear out (non-db) user values

        unset($this->blscr_number);
        unset($this->blscr_class);
        unset($this->blscr_subclass);
        unset($this->blscr_family);
        unset($this->blscr_control);
        unset($this->blscr_guidance);
        unset($this->blscr_low);
        unset($this->blscr_moderate);
        unset($this->blscr_high);
        unset($this->blscr_enhancements);
        unset($this->blscr_supplement);
	} // clearBlscr()


	public function deleteBlscr() {

		// 
		// REMOVES BLSCR FROM DATABASE!
		// 

		// ensure that we have an open database connection
		if ($this->db) {

			// define our query
			$query = "DELETE FROM `BLSCR` WHERE (`blscr_number` = '$this->blscr_number')";

			// execute our query
			$this->db->query($query);

			// clear out the current object
			$this->clearBlscr();
		
		} // $this->db

	} // deleteBlscr()
	
	

	// -----------------------------------------------------------------------
	// 
	// VARIABLE ACCESS METHODS
	// 
	// -----------------------------------------------------------------------


    public function getBlscrNumber()                                       { return $this->blscr_number; }
    public function getBlscrClass()                                        { return $this->blscr_class; }
    public function getBlscrSubclass()                                     { return $this->blscr_subclass; }
    public function getBlscrFamily()                                       { return $this->blscr_family; }
    public function getBlscrControl()                                      { return $this->blscr_control; }
    public function getBlscrGuidance()                                     { return $this->blscr_guidance; }
    public function getBlscrLow()                                          { return $this->blscr_low; }
    public function getBlscrModerate()                                     { return $this->blscr_moderate; }
    public function getBlscrHigh()                                         { return $this->blscr_high; }
    public function getBlscrEnhancements()                                 { return $this->blscr_enhancements; }
    public function getBlscrSupplement()                                   { return $this->blscr_supplement; }

	public function getValidBlscrIds($offset = 0, $limit = NULL) {
		
		// array to store blscr_ids
		$id_array = array();

		// create our query
		$query = "SELECT `blscr_number` FROM `BLSCR`";

		// add in our offset and limit if a limit is provided
		if ($limit) { $query .= " LIMIT $offset, $limit";  }

		// execute the query
		$this->db->query($query);
	
		// evaluate the results
		if ($this->db->queryOK()) {
			
			// push the values onto the array
			while ($id = $this->db->fetch_array()) { array_push($id_array, $id[0]); }
			
		}
		
		// return the array of blscr_ids
		return $id_array;
		
	} // getValidBlscrIds
	
	public function getValidBlscrNames($offset = 0, $limit = NULL) {
		
		// array to store blscr_ids
		$id_array = array();

		// create our query
		$query = "SELECT `blscr_number`,`blscr_subclass` FROM `BLSCR`";

		// add in our offset and limit if a limit is provided
		if ($limit) { $query .= " LIMIT $offset, $limit";  }

		// execute the query
		$this->db->query($query);
	
		// evaluate the results
		if ($this->db->queryOK()) {
			
			// push the values onto the array
			while ($id = $this->db->fetch_array()) { array_push($id_array, '('.$id[0].') '.$id[1]); }
			
		}
		
		// return the array of blscr_ids
		return $id_array;
		
	} // getValidBlscrNames
	//
	// -----------------------------------------------------------------------
	// 
	// VARIABLE MODIFY METHODS
	// 
	// -----------------------------------------------------------------------

    public function setBlscrNumber($blscr_number  =  NULL){ 
		// error check input (by schema)
		if (strlen($blscr_number) <= 5){
            $this->blscr_number = $blscr_number;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setBlscrNumber()
	
    public function setBlscrClass($blscr_class  =  NULL){ 
		// error check input (by schema)
		if (in_array($blscr_class, array('MANAGEMENT','OPERATIONAL','TECHNICAL')) ){
            $this->blscr_class = $blscr_class;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setBlscrClass()
    
    
    public function setBlscrSubclass($blscr_subclass  =  NULL){ 
		// error check input (by schema)
		if (strlen($blscr_subclass) >= 0){
            $this->blscr_subclass = $blscr_subclass;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setBlscrSubclass()
    
    
    public function setBlscrFamily($blscr_family  =  NULL){ 
		// error check input (by schema)
		if (strlen($blscr_family) >= 0){
            $this->blscr_family = $blscr_family;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setBlscrFamily()
    
    
    public function setBlscrControl($blscr_control  =  NULL){ 
		// error check input (by schema)
		if (strlen($blscr_control) >= 0){
            $this->blscr_control = $blscr_control;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setBlscrControl()
    
    
    public function setBlscrGuidance($blscr_guidance  =  NULL){ 
		// error check input (by schema)
		if (strlen($blscr_guidance) >= 0){
            $this->blscr_guidance = $blscr_guidance;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setBlscrGuidance()
    
    
    public function setBlscrLow($blscr_low  =  NULL){ 
		// error check input (by schema)
		if (strlen($blscr_low) <= 1){
            $this->blscr_low = $blscr_low;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setBlscrLow()
    
    
    public function setBlscrModerate($blscr_moderate  =  NULL){ 
		// error check input (by schema)
		if (strlen($blscr_moderate) <= 1){
            $this->blscr_moderate = $blscr_moderate;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setBlscrModerate()
    
    
    public function setBlscrHigh($blscr_high  =  NULL){ 
		// error check input (by schema)
		if (strlen($blscr_high) <= 1){
            $this->blscr_high = $blscr_high;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setBlscrHigh()
    
    
    public function setBlscrEnhancements($blscr_enhancements  =  NULL){ 
		// error check input (by schema)
		if (strlen($blscr_enhancements) >= 0){
            $this->blscr_enhancements = $blscr_enhancements;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setBlscrEnhancements()
    
    
    public function setBlscrSupplement($blscr_supplement  =  NULL){ 
		// error check input (by schema)
		if (strlen($blscr_supplement) >= 0){
            $this->blscr_supplement = $blscr_supplement;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setBlscrSupplement()
    
    

} // class Blscr
?>
