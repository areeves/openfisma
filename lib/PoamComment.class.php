<?PHP

// 
// INCLUDES
// 
require_once('Database.class.php');


//
// CLASS DEFINITION
// 

class PoamComment {

	// -----------------------------------------------------------------------
	//
	// VARIABLES
	//
	// -----------------------------------------------------------------------


    private $db;

    private $comment_id;
    private $poam_id;
    private $user_id;
    private $comment_parent;
    private $comment_date;
    private $comment_topic;
    private $comment_body;


	// -----------------------------------------------------------------------
	// 
	// CLASS METHODS
	// 
	// -----------------------------------------------------------------------

	public function __construct($db, $comment_id = NULL) {

		// utilize an existing database connection
		$this->db = $db;

		// get PoamComment information or create a new one if none specified
		if ($comment_id) {
		  $this->getPoamComment($comment_id); 
		}

	} // __construct()
	

	public function __destruct() {

		// clear out the comment_id to prevent any updates
		$this->comment_id = 0;

	} // __destruct()


 	public function __ToString() {
 		
 		// return a string of information
 		return	$this->db->__ToString().
 			'<pre>'.
			'<br>POAM_COMMENTS'.
			'<br>------'.

            '<br>comment_id                                        : '.$this->comment_id.
            '<br>poam_id                                           : '.$this->poam_id.
            '<br>user_id                                           : '.$this->user_id.
            '<br>comment_parent                                    : '.$this->comment_parent.
            '<br>comment_date                                      : '.$this->comment_date.
            '<br>comment_topic                                     : '.$this->comment_topic.
            '<br>comment_body                                      : '.$this->comment_body.
			'<br></pre>';
 		
 	} // __ToString()
 	

	// -----------------------------------------------------------------------
	// 
	// CLASS MANIPULATION METHODS
	// 
	// -----------------------------------------------------------------------
	
	public function poamcommentExists($comment_id = NULL) {
		
		// make sure we have a positive, non-zero comment_id
		if ($comment_id) {
		
			// build our query
			$query = "SELECT `comment_id` FROM `POAM_COMMENTS` WHERE (`comment_id` = '$comment_id')";
			
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
		
	} // poamcommentExists()
	

	public function getPoamComment($comment_id = NULL) {
		
		// make sure we have a positive, non-zero comment_id
		if ($comment_id && $this->poamcommentExists($comment_id)) {
		
			// designate our retrieval query
			$query = "SELECT * FROM `POAM_COMMENTS` WHERE (`comment_id` = '$comment_id')";
		
			// execute the query
			$this->db->query($query);
		
			// if we get a hit, store the information
			if ($this->db->num_rows() > 0) {
			
				// retrieve the results query
				$results = $this->db->fetch_assoc();
			
				// store the results locally

                $this->comment_id                                         = $results['comment_id'];
                $this->poam_id                                            = $results['poam_id'];
                $this->user_id                                            = $results['user_id'];
                $this->comment_parent                                     = $results['comment_parent'];
                $this->comment_date                                       = $results['comment_date'];
                $this->comment_topic                                      = $results['comment_topic'];
                $this->comment_body                                       = $results['comment_body'];
			
			} // this->db->fetch_assoc()
			
			// system not retrieved, clear out any potential values
			else {
			     $this->clearPoamComment(); 
			}
		} // if $comment_id

	} // getPoamComment()


		
	public function savePoamComment(){
	
	    if ($this->comment_id && $this->poamcommentExists($this->comment_id)){
    	    $query = "UPDATE `POAM_COMMENTS` SET ";    
            	    $query .= " `poam_id`                                            = '$this->poam_id', ";
            	    $query .= " `user_id`                                            = '$this->user_id', ";
            	    $query .= " `comment_parent`                                     = '$this->comment_parent', ";
            	    $query .= " `comment_date`                                       = '$this->comment_date', ";
            	    $query .= " `comment_topic`                                      = '$this->comment_topic', ";
            	    $query .= " `comment_body`                                       = '$this->comment_body' ";	    
                    $query .= " WHERE `comment_id`                                   = '$this->comment_id' ";
	    }
	    else {
	       $query = "INSERT INTO `POAM_COMMENTS` (
                            `poam_id`, 
                            `user_id`, 
                            `comment_parent`, 
                            `comment_date`, 
                            `comment_topic`, 
                            `comment_body`
                            ) VALUES (
                            '$this->poam_id', 
                            '$this->user_id', 
                            '$this->comment_parent', 
                            '$this->comment_date', 
                            '$this->comment_topic', 
                            '$this->comment_body'
                            )";
	    }
	    
    	// execute our query
    	$this->db->query($query);
    
    	if ($this->db->queryOK()) { 
    	   if (!$this->comment_id || !$this->poamcommentExists($this->comment_id)){
    	       $this->comment_id = $this->db->insert_id();
    	   }
    	   return 1; 
    	} 
    	else {
    	   return 0; 
    	}
	} //savePoamComment()
	
	public function clearPoamComment() {
		
		// clear out (non-db) user values

        unset($this->comment_id);
        unset($this->poam_id);
        unset($this->user_id);
        unset($this->comment_parent);
        unset($this->comment_date);
        unset($this->comment_topic);
        unset($this->comment_body);
	} // clearPoamComment()


	public function deletePoamComment() {

		// 
		// REMOVES POAM_COMMENTS FROM DATABASE!
		// 

		// ensure that we have an open database connection
		if ($this->db) {

			// define our query
			$query = "DELETE FROM `POAM_COMMENTS` WHERE (`comment_id` = '$this->comment_id')";

			// execute our query
			$this->db->query($query);

			// clear out the current object
			$this->clearPoamComment();
		
		} // $this->db

	} // deletePoamComment()
	
	

	// -----------------------------------------------------------------------
	// 
	// VARIABLE ACCESS METHODS
	// 
	// -----------------------------------------------------------------------


    public function getCommentId()                                         { return $this->comment_id; }
    public function getPoamId()                                            { return $this->poam_id; }
    public function getUserId()                                            { return $this->user_id; }
    public function getCommentParent()                                     { return $this->comment_parent; }
    public function getCommentDate()                                       { return $this->comment_date; }
    public function getCommentTopic()                                      { return $this->comment_topic; }
    public function getCommentBody()                                       { return $this->comment_body; }

	public function getValidCommentIds($offset = 0, $limit = NULL) {
		
		// array to store comment_id
		$id_array = array();

		// create our query
		$query = "SELECT comment_id from POAM_COMMENTS";
		
		// add in our offset and limit if a limit is provided
		if ($limit) { $query .= " LIMIT $offset, $limit";  }		
		
		// execute the query
		$this->db->query($query);
	
		// evaluate the results
		if ($this->db->queryOK()) {
			
			// push the values onto the array
			while ($id = $this->db->fetch_array()) { array_push($id_array, $id[0]); }
			
		}
		
		// return the array of comment_id
		return $id_array;
		
	} // getValidCommentIds
    
    //
	// -----------------------------------------------------------------------
	// 
	// VARIABLE MODIFY METHODS
	// 
	// -----------------------------------------------------------------------


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
    
    
    public function setUserId($user_id  =  NULL){ 
		// error check input (by schema)
		if (strlen($user_id) <= 10){
            $this->user_id = $user_id;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setUserId()
    
    
    public function setCommentParent($comment_parent  =  NULL){ 
		// error check input (by schema)
		if (strlen($comment_parent) <= 10){
            $this->comment_parent = $comment_parent;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setCommentParent()
    
    
    public function setCommentDate($comment_date  =  NULL){ 
		// error check input (by schema)
		if (strlen($comment_date) >= 0){
            $this->comment_date = $comment_date;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setCommentDate()
    
    
    public function setCommentTopic($comment_topic  =  NULL){ 
		// error check input (by schema)
		if (strlen($comment_topic) <= 64){
            $this->comment_topic = $comment_topic;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setCommentTopic()
    
    
    public function setCommentBody($comment_body  =  NULL){ 
		// error check input (by schema)
		if (strlen($comment_body) >= 0){
            $this->comment_body = $comment_body;
            return true;
		} // input error check
		else {
		    return false;
		}
	} // setCommentBody()
    
    

} // class PoamComment
?>
