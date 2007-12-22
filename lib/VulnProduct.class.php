<?PHP

// ### This is a non-primarykey table ###
//
// INCLUDES
// 
require_once('Database.class.php');


//
// CLASS DEFINITION
// 

class VulnProduct {

	// -----------------------------------------------------------------------
	//
	// VARIABLES
	//
	// -----------------------------------------------------------------------


    private $db;

    private $vuln_seq;
    private $vuln_type;
    private $prod_id;


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
			'<br>VULN_PRODUCTS'.
			'<br>------'.

            '<br>vuln_seq                                          : '.$this->vuln_seq.
            '<br>vuln_type                                         : '.$this->vuln_type.
            '<br>prod_id                                           : '.$this->prod_id.
			'<br></pre>';
 		
 	} // __ToString()
 	

	// -----------------------------------------------------------------------
	// 
	// CLASS MANIPULATION METHODS
	// 
	// -----------------------------------------------------------------------

	public function saveVulnProduct(){
	
       $query = "INSERT INTO `VULN_PRODUCTS` (
                    `vuln_seq`, 
                    `vuln_type`, 
                    `prod_id`, 
                        ) VALUES (
                       '$this->vuln_seq', 
                       '$this->vuln_type', 
                       '$this->prod_id')";
	    
    	// execute our query
    	$this->db->query($query);
    
    	if ($this->db->queryOK()) { 
    	   return 1; 
    	} 
    	else {
    	   return 0; 
    	}
	} //saveVulnProduct()
	
	public function clearVulnProduct() {
		
		// clear out (non-db) user values

        unset($this->vuln_seq);
        unset($this->vuln_type);
        unset($this->prod_id);
	} // clearVulnProduct()


	public function deleteVulnProduct() {

		// 
		// REMOVES VULN_PRODUCTS FROM DATABASE!
		// 
		$whereCond = array();

        if (isset($this->vuln_seq)) {
	        $whereCond[] = " `vuln_seq` = '$this->vuln_seq' ";
        }
        if (isset($this->vuln_type)) {
	        $whereCond[] = " `vuln_type` = '$this->vuln_type' ";
        }
        if (isset($this->prod_id)) {
	        $whereCond[] = " `prod_id` = '$this->prod_id' ";
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
			$query = 'DELETE FROM `VULN_PRODUCTS` WHERE '.$where;

			// execute our query
        	$this->db->query($query);

			// clear out the current object
			$this->clearAssetAddress();
		
		} // $this->db

	} // deleteVulnProduct()
	
	

	// -----------------------------------------------------------------------
	// 
	// VARIABLE ACCESS METHODS
	// 
	// -----------------------------------------------------------------------


    public function getVulnSeq()                                           { return $this->vuln_seq; }
    public function getVulnType()                                          { return $this->vuln_type; }
    public function getProdId()                                            { return $this->prod_id; }

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
    
    
    public function setProdId($prod_id  =  NULL){ 
		// error check input (by schema)
		if (strlen($prod_id) <= 10){
            $this->prod_id = $prod_id;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setProdId()
    
    

} // class VulnProduct
?>
