<?PHP

// 
// INCLUDES
// 
require_once('Database.class.php');
require_once('BasicList.class.php');


//
// CLASS DEFINITION
// 

class PoamCommentList extends BasicList {

  // -----------------------------------------------------------------------
  // 
  // CLASS METHODS
  // 
  // -----------------------------------------------------------------------
  
  public function __construct($db = NULL) { 

	// call the parent constructor with the db connection and table name
	parent::__construct($db, 'POAM_COMMENTS'); 

  } // __construct()
  

  public function __destruct() {

	// call the parent destructor
	parent::__destruct();

  } // __destruct()
  
  public function __ToString() {} // __ToString()
 	

  // -----------------------------------------------------------------------
  // 
  // PARAMETERS
  // 
  // -----------------------------------------------------------------------
  
  public function getCommentId($isKey = FALSE)                                      { array_push($this->params, 'comment_id');                                        if ($isKey) { $this->key = 'comment_id'; } }
  public function getPoamId($isKey = FALSE)                                         { array_push($this->params, 'poam_id');                                           if ($isKey) { $this->key = 'poam_id'; } }
  public function getUserId($isKey = FALSE)                                         { array_push($this->params, 'user_id');                                           if ($isKey) { $this->key = 'user_id'; } }
  public function getCommentParent($isKey = FALSE)                                  { array_push($this->params, 'comment_parent');                                    if ($isKey) { $this->key = 'comment_parent'; } }
  public function getCommentDate($isKey = FALSE)                                    { array_push($this->params, 'comment_date');                                      if ($isKey) { $this->key = 'comment_date'; } }
  public function getCommentTopic($isKey = FALSE)                                   { array_push($this->params, 'comment_topic');                                     if ($isKey) { $this->key = 'comment_topic'; } }
  public function getCommentBody($isKey = FALSE)                                    { array_push($this->params, 'comment_body');                                      if ($isKey) { $this->key = 'comment_body'; } }  

  // -----------------------------------------------------------------------
  // 
  // FILTERS
  // 
  // -----------------------------------------------------------------------
  
  public function filterCommentId($value = NULL, $bool = TRUE)                                      { $this->filters['comment_id']                                        = array($value, $bool); }
  public function filterPoamId($value = NULL, $bool = TRUE)                                         { $this->filters['poam_id']                                           = array($value, $bool); }
  public function filterUserId($value = NULL, $bool = TRUE)                                         { $this->filters['user_id']                                           = array($value, $bool); }
  public function filterCommentParent($value = NULL, $bool = TRUE)                                  { $this->filters['comment_parent']                                    = array($value, $bool); }
  public function filterCommentDate($value = NULL, $bool = TRUE)                                    { $this->filters['comment_date']                                      = array($value, $bool); }
  public function filterCommentTopic($value = NULL, $bool = TRUE)                                   { $this->filters['comment_topic']                                     = array($value, $bool); }
  public function filterCommentBody($value = NULL, $bool = TRUE)                                    { $this->filters['comment_body']                                      = array($value, $bool); }  

} // class PoamCommentList

?>