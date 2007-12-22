<?PHP

// ### This is a non-primarykey table ###
//
// INCLUDES
// 
require_once('Database.class.php');


//
// CLASS DEFINITION
// 

class AssetAddress {

	// -----------------------------------------------------------------------
	//
	// VARIABLES
	//
	// -----------------------------------------------------------------------


    private $db;

    private $asset_id;
    private $network_id;
    private $address_date_created;
    private $address_ip;
    private $address_port;


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
			'<br>ASSET_ADDRESSES'.
			'<br>------'.

            '<br>asset_id                                          : '.$this->asset_id.
            '<br>network_id                                        : '.$this->network_id.
            '<br>address_date_created                              : '.$this->address_date_created.
            '<br>address_ip                                        : '.$this->address_ip.
            '<br>address_port                                      : '.$this->address_port.
			'<br></pre>';
 		
 	} // __ToString()
 	

	// -----------------------------------------------------------------------
	// 
	// CLASS MANIPULATION METHODS
	// 
	// -----------------------------------------------------------------------

	public function saveAssetAddress(){
	
       $query = "INSERT INTO `ASSET_ADDRESSES` (
                    `asset_id`, 
                    `network_id`, 
                    `address_date_created`, 
                    `address_ip`, 
                    `address_port`, 
                        ) VALUES (
                       '$this->asset_id', 
                       '$this->network_id', 
                       '$this->address_date_created', 
                       '$this->address_ip', 
                       '$this->address_port')";
	    
    	// execute our query
    	$this->db->query($query);
    
    	if ($this->db->queryOK()) { 
    	   return 1; 
    	} 
    	else {
    	   return 0; 
    	}
	} //saveAssetAddress()
	
	public function clearAssetAddress() {
		
		// clear out (non-db) user values

        unset($this->asset_id);
        unset($this->network_id);
        unset($this->address_date_created);
        unset($this->address_ip);
        unset($this->address_port);
	} // clearAssetAddress()


	public function deleteAssetAddress() {

		// 
		// REMOVES ASSET_ADDRESSES FROM DATABASE!
		// 
		$whereCond = array();

        if (isset($this->asset_id)) {
	        $whereCond[] = " `asset_id` = '$this->asset_id' ";
        }
        if (isset($this->network_id)) {
	        $whereCond[] = " `network_id` = '$this->network_id' ";
        }
        if (isset($this->address_date_created)) {
	        $whereCond[] = " `address_date_created` = '$this->address_date_created' ";
        }
        if (isset($this->address_ip)) {
	        $whereCond[] = " `address_ip` = '$this->address_ip' ";
        }
        if (isset($this->address_port)) {
	        $whereCond[] = " `address_port` = '$this->address_port' ";
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
			$query = 'DELETE FROM `ASSET_ADDRESSES` WHERE '.$where;

			// execute our query
        	$this->db->query($query);

			// clear out the current object
			$this->clearAssetAddress();
		
		} // $this->db

	} // deleteAssetAddress()
	
	

	// -----------------------------------------------------------------------
	// 
	// VARIABLE ACCESS METHODS
	// 
	// -----------------------------------------------------------------------


    public function getAssetId()                                           { return $this->asset_id; }
    public function getNetworkId()                                         { return $this->network_id; }
    public function getAddressDateCreated()                                { return $this->address_date_created; }
    public function getAddressIp()                                         { return $this->address_ip; }
    public function getAddressPort()                                       { return $this->address_port; }

	// -----------------------------------------------------------------------
	// 
	// VARIABLE MODIFY METHODS
	// 
	// -----------------------------------------------------------------------


    public function setAssetId($asset_id  =  NULL){ 
		// error check input (by schema)
		if (strlen($asset_id) <= 10){
            $this->asset_id = $asset_id;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setAssetId()
    
    
    public function setNetworkId($network_id  =  NULL){ 
		// error check input (by schema)
		if (strlen($network_id) <= 10){
            $this->network_id = $network_id;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setNetworkId()
    
    
    public function setAddressDateCreated($address_date_created  =  NULL){ 
		// error check input (by schema)
		if (strlen($address_date_created) >= 0){
            $this->address_date_created = $address_date_created;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setAddressDateCreated()
    
    
    public function setAddressIp($address_ip  =  NULL){ 
		// error check input (by schema)
		if (strlen($address_ip) <= 23){
            $this->address_ip = $address_ip;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setAddressIp()
    
    
    public function setAddressPort($address_port  =  NULL){ 
		// error check input (by schema)
		if (strlen($address_port) <= 10){
            $this->address_port = $address_port;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setAddressPort()
    
    

} // class AssetAddress
?>
