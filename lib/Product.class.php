<?PHP

// 
// INCLUDES
// 
require_once('Database.class.php');


//
// CLASS DEFINITION
// 

class Product {

	// -----------------------------------------------------------------------
	//
	// VARIABLES
	//
	// -----------------------------------------------------------------------


    private $db;

    private $prod_id;
    private $prod_nvd_defined;
    private $prod_meta;
    private $prod_vendor;
    private $prod_name;
    private $prod_version;
    private $prod_desc;


	// -----------------------------------------------------------------------
	// 
	// CLASS METHODS
	// 
	// -----------------------------------------------------------------------

	public function __construct($db, $prod_id = NULL) {

		// utilize an existing database connection
		$this->db = $db;

		// get Product information or create a new one if none specified
		if ($prod_id) {
		  $this->getProduct($prod_id); 
		}

	} // __construct()
	

	public function __destruct() {

		// clear out the prod_id to prevent any updates
		$this->prod_id = 0;

	} // __destruct()


 	public function __ToString() {
 		
 		// return a string of information
 		return	$this->db->__ToString().
 			'<pre>'.
			'<br>PRODUCTS'.
			'<br>------'.

            '<br>prod_id                                           : '.$this->prod_id.
            '<br>prod_nvd_defined                                  : '.$this->prod_nvd_defined.
            '<br>prod_meta                                         : '.$this->prod_meta.
            '<br>prod_vendor                                       : '.$this->prod_vendor.
            '<br>prod_name                                         : '.$this->prod_name.
            '<br>prod_version                                      : '.$this->prod_version.
            '<br>prod_desc                                         : '.$this->prod_desc.
			'<br></pre>';
 		
 	} // __ToString()
 	

	// -----------------------------------------------------------------------
	// 
	// CLASS MANIPULATION METHODS
	// 
	// -----------------------------------------------------------------------
	
	public function productExists($prod_id = NULL) {
		
		// make sure we have a positive, non-zero prod_id
		if ($prod_id) {
		
			// build our query
			$query = "SELECT `prod_id` FROM `PRODUCTS` WHERE (`prod_id` = '$prod_id')";
			
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
		
	} // productExists()
	

	public function getProduct($prod_id = NULL) {
		
		// make sure we have a positive, non-zero prod_id
		if ($prod_id && $this->productExists($prod_id)) {
		
			// designate our retrieval query
			$query = "SELECT * FROM `PRODUCTS` WHERE (`prod_id` = '$prod_id')";
		
			// execute the query
			$this->db->query($query);
		
			// if we get a hit, store the information
			if ($this->db->num_rows() > 0) {
			
				// retrieve the results query
				$results = $this->db->fetch_assoc();
			
				// store the results locally

                $this->prod_id                                            = $results['prod_id'];
                $this->prod_nvd_defined                                   = $results['prod_nvd_defined'];
                $this->prod_meta                                          = $results['prod_meta'];
                $this->prod_vendor                                        = $results['prod_vendor'];
                $this->prod_name                                          = $results['prod_name'];
                $this->prod_version                                       = $results['prod_version'];
                $this->prod_desc                                          = $results['prod_desc'];
			
			} // this->db->fetch_assoc()
			
			// system not retrieved, clear out any potential values
			else {
			     $this->clearProduct(); 
			}
		} // if $prod_id

	} // getProduct()


		
	public function saveProduct(){
	
	    if ($this->prod_id && $this->productExists($this->prod_id)){
    	    $query = "UPDATE `PRODUCTS` SET ";    
            	    $query .= " `prod_nvd_defined`                                   = '$this->prod_nvd_defined', ";
            	    $query .= " `prod_meta`                                          = '$this->prod_meta', ";
            	    $query .= " `prod_vendor`                                        = '$this->prod_vendor', ";
            	    $query .= " `prod_name`                                          = '$this->prod_name', ";
            	    $query .= " `prod_version`                                       = '$this->prod_version', ";
            	    $query .= " `prod_desc`                                          = '$this->prod_desc' ";	    
                    $query .= " WHERE `prod_id`                                      = '$this->prod_id' ";
	    }
	    else {
	       $query = "INSERT INTO `PRODUCTS` (
                            `prod_nvd_defined`, 
                            `prod_meta`, 
                            `prod_vendor`, 
                            `prod_name`, 
                            `prod_version`, 
                            `prod_desc`
                            ) VALUES (
                            '$this->prod_nvd_defined', 
                            '$this->prod_meta', 
                            '$this->prod_vendor', 
                            '$this->prod_name', 
                            '$this->prod_version', 
                            '$this->prod_desc'
                            )";
	    }
	    
    	// execute our query
    	$this->db->query($query);
    
    	if ($this->db->queryOK()) { 
    	   if (!$this->prod_id || !$this->productExists($this->prod_id)){
    	       $this->prod_id = $this->db->insert_id();
    	   }
    	   return 1; 
    	} 
    	else {
    	   return 0; 
    	}
	} //saveProduct()
	
	public function clearProduct() {
		
		// clear out (non-db) user values

        unset($this->prod_id);
        unset($this->prod_nvd_defined);
        unset($this->prod_meta);
        unset($this->prod_vendor);
        unset($this->prod_name);
        unset($this->prod_version);
        unset($this->prod_desc);
	} // clearProduct()


	public function deleteProduct() {

		// 
		// REMOVES PRODUCTS FROM DATABASE!
		// 

		// ensure that we have an open database connection
		if ($this->db) {

			// define our query
			$query = "DELETE FROM `PRODUCTS` WHERE (`prod_id` = '$this->prod_id')";

			// execute our query
			$this->db->query($query);

			// clear out the current object
			$this->clearProduct();
		
		} // $this->db

	} // deleteProduct()
	
	

	// -----------------------------------------------------------------------
	// 
	// VARIABLE ACCESS METHODS
	// 
	// -----------------------------------------------------------------------


    public function getProdId()                                            { return $this->prod_id; }
    public function getProdNvdDefined()                                    { return $this->prod_nvd_defined; }
    public function getProdMeta()                                          { return $this->prod_meta; }
    public function getProdVendor()                                        { return $this->prod_vendor; }
    public function getProdName()                                          { return $this->prod_name; }
    public function getProdVersion()                                       { return $this->prod_version; }
    public function getProdDesc()                                          { return $this->prod_desc; }

	public function getValidProductIds($offset = 0, $limit = NULL) {
		
		// array to store prod_ids
		$id_array = array();

		// create our query
		$query = "SELECT `prod_id` FROM `PRODUCTS`";

		// add in our offset and limit if a limit is provided
		if ($limit) { $query .= " LIMIT $offset, $limit";  }

		// execute the query
		$this->db->query($query);
	
		// evaluate the results
		if ($this->db->queryOK()) {
			
			// push the values onto the array
			while ($id = $this->db->fetch_array()) { array_push($id_array, $id[0]); }
			
		}
		
		// return the array of prod_ids
		return $id_array;
		
	} // getValidProductIds
	
	public function getValidProductNames($offset = 0, $limit = NULL) {
		
		// array to store prod_names
		$name_array = array();

		// create our query
		$query = "SELECT `prod_name` FROM `PRODUCTS`";

		// add in our offset and limit if a limit is provided
		if ($limit) { $query .= " LIMIT $offset, $limit";  }

		// execute the query
		$this->db->query($query);
	
		// evaluate the results
		if ($this->db->queryOK()) {
			
			// push the values onto the array
			while ($name = $this->db->fetch_array()) { array_push($name_array, $name[0]); }
			
		}
		
		// return the array of prod_names
		return $name_array;
		
	} // getValidProductNames
	
	// -----------------------------------------------------------------------
	// 
	// VARIABLE MODIFY METHODS
	// 
	// -----------------------------------------------------------------------


    public function setProdNvdDefined($prod_nvd_defined  =  NULL){ 
		// error check input (by schema)
		if (strlen($prod_nvd_defined) <= 1){
            $this->prod_nvd_defined = $prod_nvd_defined;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setProdNvdDefined()
    
    
    public function setProdMeta($prod_meta  =  NULL){ 
		// error check input (by schema)
		if (strlen($prod_meta) >= 0){
            $this->prod_meta = $prod_meta;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setProdMeta()
    
    
    public function setProdVendor($prod_vendor  =  NULL){ 
		// error check input (by schema)
		if (strlen($prod_vendor) <= 64){
            $this->prod_vendor = $prod_vendor;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setProdVendor()
    
    
    public function setProdName($prod_name  =  NULL){ 
		// error check input (by schema)
		if (strlen($prod_name) <= 64){
            $this->prod_name = $prod_name;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setProdName()
    
    
    public function setProdVersion($prod_version  =  NULL){ 
		// error check input (by schema)
		if (strlen($prod_version) <= 32){
            $this->prod_version = $prod_version;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setProdVersion()
    
    
    public function setProdDesc($prod_desc  =  NULL){ 
		// error check input (by schema)
		if (strlen($prod_desc) >= 0){
            $this->prod_desc = $prod_desc;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setProdDesc()
    
    

} // class Product
?>
