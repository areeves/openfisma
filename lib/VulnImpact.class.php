<?PHP

// ### This is a non-primarykey table ###
//
// INCLUDES
// 
require_once('Database.class.php');


//
// CLASS DEFINITION
// 

class VulnImpact {

	// -----------------------------------------------------------------------
	//
	// VARIABLES
	//
	// -----------------------------------------------------------------------


    private $db;

    private $vuln_seq;
    private $vuln_type;
    private $imp_desc;
    private $imp_source;


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
			'<br>VULN_IMPACTS'.
			'<br>------'.

            '<br>vuln_seq                                          : '.$this->vuln_seq.
            '<br>vuln_type                                         : '.$this->vuln_type.
            '<br>imp_desc                                          : '.$this->imp_desc.
            '<br>imp_source                                        : '.$this->imp_source.
			'<br></pre>';
 		
 	} // __ToString()
 	

	// -----------------------------------------------------------------------
	// 
	// CLASS MANIPULATION METHODS
	// 
	// -----------------------------------------------------------------------

	public function saveVulnImpact(){
	
       $query = "INSERT INTO `VULN_IMPACTS` (
                    `vuln_seq`, 
                    `vuln_type`, 
                    `imp_desc`, 
                    `imp_source`, 
                        ) VALUES (
                       '$this->vuln_seq', 
                       '$this->vuln_type', 
                       '$this->imp_desc', 
                       '$this->imp_source')";
	    
    	// execute our query
    	$this->db->query($query);
    
    	if ($this->db->queryOK()) { 
    	   return 1; 
    	} 
    	else {
    	   return 0; 
    	}
	} //saveVulnImpact()
	
	public function clearVulnImpact() {
		
		// clear out (non-db) user values

        unset($this->vuln_seq);
        unset($this->vuln_type);
        unset($this->imp_desc);
        unset($this->imp_source);
	} // clearVulnImpact()


	public function deleteVulnImpact() {

		// 
		// REMOVES VULN_IMPACTS FROM DATABASE!
		// 
		$whereCond = array();

        if (isset($this->vuln_seq)) {
	        $whereCond[] = " `vuln_seq` = '$this->vuln_seq' ";
        }
        if (isset($this->vuln_type)) {
	        $whereCond[] = " `vuln_type` = '$this->vuln_type' ";
        }
        if (isset($this->imp_desc)) {
	        $whereCond[] = " `imp_desc` = '$this->imp_desc' ";
        }
        if (isset($this->imp_source)) {
	        $whereCond[] = " `imp_source` = '$this->imp_source' ";
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
			$query = 'DELETE FROM `VULN_IMPACTS` WHERE '.$where;

			// execute our query
        	$this->db->query($query);

			// clear out the current object
			$this->clearAssetAddress();
		
		} // $this->db

	} // deleteVulnImpact()
	
	

	// -----------------------------------------------------------------------
	// 
	// VARIABLE ACCESS METHODS
	// 
	// -----------------------------------------------------------------------


    public function getVulnSeq()                                           { return $this->vuln_seq; }
    public function getVulnType()                                          { return $this->vuln_type; }
    public function getImpDesc()                                           { return $this->imp_desc; }
    public function getImpSource()                                         { return $this->imp_source; }

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
    
    
    public function setImpDesc($imp_desc  =  NULL){ 
		// error check input (by schema)
		if (strlen($imp_desc) >= 0){
            $this->imp_desc = $imp_desc;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setImpDesc()
    
    
    public function setImpSource($imp_source  =  NULL){ 
		// error check input (by schema)
		if (strlen($imp_source) >= 0){
            $this->imp_source = $imp_source;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setImpSource()
    
    

} // class VulnImpact
?>
