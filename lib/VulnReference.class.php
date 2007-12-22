<?PHP

// ### This is a non-primarykey table ###
//
// INCLUDES
// 
require_once('Database.class.php');


//
// CLASS DEFINITION
// 

class VulnReference {

	// -----------------------------------------------------------------------
	//
	// VARIABLES
	//
	// -----------------------------------------------------------------------


    private $db;

    private $vuln_type;
    private $vuln_seq;
    private $ref_name;
    private $ref_source;
    private $ref_url;
    private $ref_is_advisory;
    private $ref_has_tool_sig;
    private $ref_has_patch;


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
			'<br>VULN_REFERENCES'.
			'<br>------'.

            '<br>vuln_type                                         : '.$this->vuln_type.
            '<br>vuln_seq                                          : '.$this->vuln_seq.
            '<br>ref_name                                          : '.$this->ref_name.
            '<br>ref_source                                        : '.$this->ref_source.
            '<br>ref_url                                           : '.$this->ref_url.
            '<br>ref_is_advisory                                   : '.$this->ref_is_advisory.
            '<br>ref_has_tool_sig                                  : '.$this->ref_has_tool_sig.
            '<br>ref_has_patch                                     : '.$this->ref_has_patch.
			'<br></pre>';
 		
 	} // __ToString()
 	

	// -----------------------------------------------------------------------
	// 
	// CLASS MANIPULATION METHODS
	// 
	// -----------------------------------------------------------------------

	public function saveVulnReference(){
	
       $query = "INSERT INTO `VULN_REFERENCES` (
                    `vuln_type`, 
                    `vuln_seq`, 
                    `ref_name`, 
                    `ref_source`, 
                    `ref_url`, 
                    `ref_is_advisory`, 
                    `ref_has_tool_sig`, 
                    `ref_has_patch`, 
                        ) VALUES (
                       '$this->vuln_type', 
                       '$this->vuln_seq', 
                       '$this->ref_name', 
                       '$this->ref_source', 
                       '$this->ref_url', 
                       '$this->ref_is_advisory', 
                       '$this->ref_has_tool_sig', 
                       '$this->ref_has_patch')";
	    
    	// execute our query
    	$this->db->query($query);
    
    	if ($this->db->queryOK()) { 
    	   return 1; 
    	} 
    	else {
    	   return 0; 
    	}
	} //saveVulnReference()
	
	public function clearVulnReference() {
		
		// clear out (non-db) user values

        unset($this->vuln_type);
        unset($this->vuln_seq);
        unset($this->ref_name);
        unset($this->ref_source);
        unset($this->ref_url);
        unset($this->ref_is_advisory);
        unset($this->ref_has_tool_sig);
        unset($this->ref_has_patch);
	} // clearVulnReference()


	public function deleteVulnReference() {

		// 
		// REMOVES VULN_REFERENCES FROM DATABASE!
		// 
		$whereCond = array();

        if (isset($this->vuln_type)) {
	        $whereCond[] = " `vuln_type` = '$this->vuln_type' ";
        }
        if (isset($this->vuln_seq)) {
	        $whereCond[] = " `vuln_seq` = '$this->vuln_seq' ";
        }
        if (isset($this->ref_name)) {
	        $whereCond[] = " `ref_name` = '$this->ref_name' ";
        }
        if (isset($this->ref_source)) {
	        $whereCond[] = " `ref_source` = '$this->ref_source' ";
        }
        if (isset($this->ref_url)) {
	        $whereCond[] = " `ref_url` = '$this->ref_url' ";
        }
        if (isset($this->ref_is_advisory)) {
	        $whereCond[] = " `ref_is_advisory` = '$this->ref_is_advisory' ";
        }
        if (isset($this->ref_has_tool_sig)) {
	        $whereCond[] = " `ref_has_tool_sig` = '$this->ref_has_tool_sig' ";
        }
        if (isset($this->ref_has_patch)) {
	        $whereCond[] = " `ref_has_patch` = '$this->ref_has_patch' ";
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
			$query = 'DELETE FROM `VULN_REFERENCES` WHERE '.$where;

			// execute our query
        	$this->db->query($query);

			// clear out the current object
			$this->clearAssetAddress();
		
		} // $this->db

	} // deleteVulnReference()
	
	

	// -----------------------------------------------------------------------
	// 
	// VARIABLE ACCESS METHODS
	// 
	// -----------------------------------------------------------------------


    public function getVulnType()                                          { return $this->vuln_type; }
    public function getVulnSeq()                                           { return $this->vuln_seq; }
    public function getRefName()                                           { return $this->ref_name; }
    public function getRefSource()                                         { return $this->ref_source; }
    public function getRefUrl()                                            { return $this->ref_url; }
    public function getRefIsAdvisory()                                     { return $this->ref_is_advisory; }
    public function getRefHasToolSig()                                     { return $this->ref_has_tool_sig; }
    public function getRefHasPatch()                                       { return $this->ref_has_patch; }

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
    
    
    public function setRefName($ref_name  =  NULL){ 
		// error check input (by schema)
		if (strlen($ref_name) >= 0){
            $this->ref_name = $ref_name;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setRefName()
    
    
    public function setRefSource($ref_source  =  NULL){ 
		// error check input (by schema)
		if (strlen($ref_source) >= 0){
            $this->ref_source = $ref_source;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setRefSource()
    
    
    public function setRefUrl($ref_url  =  NULL){ 
		// error check input (by schema)
		if (strlen($ref_url) >= 0){
            $this->ref_url = $ref_url;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setRefUrl()
    
    
    public function setRefIsAdvisory($ref_is_advisory  =  NULL){ 
		// error check input (by schema)
		if (strlen($ref_is_advisory) <= 1){
            $this->ref_is_advisory = $ref_is_advisory;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setRefIsAdvisory()
    
    
    public function setRefHasToolSig($ref_has_tool_sig  =  NULL){ 
		// error check input (by schema)
		if (strlen($ref_has_tool_sig) <= 1){
            $this->ref_has_tool_sig = $ref_has_tool_sig;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setRefHasToolSig()
    
    
    public function setRefHasPatch($ref_has_patch  =  NULL){ 
		// error check input (by schema)
		if (strlen($ref_has_patch) <= 1){
            $this->ref_has_patch = $ref_has_patch;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setRefHasPatch()
    
    

} // class VulnReference
?>
