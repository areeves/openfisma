<?PHP

// ### This is a non-primarykey table ###
//
// INCLUDES
// 
require_once('Database.class.php');


//
// CLASS DEFINITION
// 

class VulnSolution {

	// -----------------------------------------------------------------------
	//
	// VARIABLES
	//
	// -----------------------------------------------------------------------


    private $db;

    private $vuln_seq;
    private $vuln_type;
    private $sol_desc;
    private $sol_source;


	// -----------------------------------------------------------------------
	// 
	// CLASS METHODS
	// 
	// -----------------------------------------------------------------------

	public function __construct($db) {

		// utilize an existing database connection
		$this->db = $db;
	} // __construct()
	

	public function __destruct() {

	} // __destruct()


 	public function __ToString() {
 		
 		// return a string of information
 		return	$this->db->__ToString().
 			'<pre>'.
			'<br>VULN_SOLUTIONS'.
			'<br>------'.

            '<br>vuln_seq                                          : '.$this->vuln_seq.
            '<br>vuln_type                                         : '.$this->vuln_type.
            '<br>sol_desc                                          : '.$this->sol_desc.
            '<br>sol_source                                        : '.$this->sol_source.
			'<br></pre>';
 		
 	} // __ToString()
 	

	// -----------------------------------------------------------------------
	// 
	// CLASS MANIPULATION METHODS
	// 
	// -----------------------------------------------------------------------

	public function saveVulnSolution(){
	
       $query = "INSERT INTO `VULN_SOLUTIONS` (
                    `vuln_seq`, 
                    `vuln_type`, 
                    `sol_desc`, 
                    `sol_source`, 
                        ) VALUES (
                       '$this->vuln_seq', 
                       '$this->vuln_type', 
                       '$this->sol_desc', 
                       '$this->sol_source')";
	    
    	// execute our query
    	$this->db->query($query);
    
    	if ($this->db->queryOK()) { 
    	   return 1; 
    	} 
    	else {
    	   return 0; 
    	}
	} //saveVulnSolution()
	
	public function clearVulnSolution() {
		
		// clear out (non-db) user values

        unset($this->vuln_seq);
        unset($this->vuln_type);
        unset($this->sol_desc);
        unset($this->sol_source);
	} // clearVulnSolution()


	public function deleteVulnSolution() {

		// 
		// REMOVES VULN_SOLUTIONS FROM DATABASE!
		// 
		$whereCond = array();

        if (isset($this->vuln_seq)) {
	        $whereCond[] = " `vuln_seq` = '$this->vuln_seq' ";
        }
        if (isset($this->vuln_type)) {
	        $whereCond[] = " `vuln_type` = '$this->vuln_type' ";
        }
        if (isset($this->sol_desc)) {
	        $whereCond[] = " `sol_desc` = '$this->sol_desc' ";
        }
        if (isset($this->sol_source)) {
	        $whereCond[] = " `sol_source` = '$this->sol_source' ";
        }
		if (count($whereCond) < 1){
		    return false;
		}
		elseif (count($whereCond) == 1){
		    $where = $whereCond[0];
		}
		else {
			$where = implode(' AND ', $whereCond);
		}
		// ensure that we have an open database connection
		if ($this->db) {

			// define our query
			$query = 'DELETE FROM `VULN_SOLUTIONS` WHERE '.$where;

			// execute our query
        	$this->db->query($query);

			// clear out the current object
			$this->clearAssetAddress();
		
		} // $this->db

	} // deleteVulnSolution()
	
	

	// -----------------------------------------------------------------------
	// 
	// VARIABLE ACCESS METHODS
	// 
	// -----------------------------------------------------------------------


    public function getVulnSeq()                                           { return $this->vuln_seq; }
    public function getVulnType()                                          { return $this->vuln_type; }
    public function getSolDesc()                                           { return $this->sol_desc; }
    public function getSolSource()                                         { return $this->sol_source; }

	// -----------------------------------------------------------------------
	// 
	// VARIABLE MODIFY METHODS
	// 
	// -----------------------------------------------------------------------


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
    
    
    public function setSolDesc($sol_desc  =  NULL){ 
		// error check input (by schema)
		if (strlen($sol_desc) >= 0){
            $this->sol_desc = $sol_desc;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setSolDesc()
    
    
    public function setSolSource($sol_source  =  NULL){ 
		// error check input (by schema)
		if (strlen($sol_source) >= 0){
            $this->sol_source = $sol_source;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setSolSource()
    
    

} // class VulnSolution
?>
