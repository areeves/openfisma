<?PHP

// 
// INCLUDES
// 
require_once('Database.class.php');
require_once('BasicList.class.php');


//
// CLASS DEFINITION
// 

class UserList extends BasicList {

  // -----------------------------------------------------------------------
  // 
  // CLASS METHODS
  // 
  // -----------------------------------------------------------------------
  
  public function __construct($db = NULL) { 

	// call the parent constructor with the db connection and table name
	parent::__construct($db, 'USERS'); 

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
  
  public function getUserId($isKey = FALSE)                                         { array_push($this->params, 'user_id');                                           if ($isKey) { $this->key = 'user_id'; } }
  public function getUserIsActive($isKey = FALSE)                                   { array_push($this->params, 'user_is_active');                                    if ($isKey) { $this->key = 'user_is_active'; } }
  public function getUserDateCreated($isKey = FALSE)                                { array_push($this->params, 'user_date_created');                                 if ($isKey) { $this->key = 'user_date_created'; } }
  public function getUserDateDeleted($isKey = FALSE)                                { array_push($this->params, 'user_date_deleted');                                 if ($isKey) { $this->key = 'user_date_deleted'; } }
  public function getUserDateLastLogin($isKey = FALSE)                              { array_push($this->params, 'user_date_last_login');                              if ($isKey) { $this->key = 'user_date_last_login'; } }
  public function getUserDatePassword($isKey = FALSE)                               { array_push($this->params, 'user_date_password');                                if ($isKey) { $this->key = 'user_date_password'; } }
  public function getUserName($isKey = FALSE)                                       { array_push($this->params, 'user_name');                                         if ($isKey) { $this->key = 'user_name'; } }
  public function getUserNameLast($isKey = FALSE)                                   { array_push($this->params, 'user_name_last');                                    if ($isKey) { $this->key = 'user_name_last'; } }
  public function getUserNameMiddle($isKey = FALSE)                                 { array_push($this->params, 'user_name_middle');                                  if ($isKey) { $this->key = 'user_name_middle'; } }
  public function getUserNameFirst($isKey = FALSE)                                  { array_push($this->params, 'user_name_first');                                   if ($isKey) { $this->key = 'user_name_first'; } }
  public function getUserTitle($isKey = FALSE)                                      { array_push($this->params, 'user_title');                                        if ($isKey) { $this->key = 'user_title'; } }
  public function getUserPhoneOffice($isKey = FALSE)                                { array_push($this->params, 'user_phone_office');                                 if ($isKey) { $this->key = 'user_phone_office'; } }
  public function getUserPhoneMobile($isKey = FALSE)                                { array_push($this->params, 'user_phone_mobile');                                 if ($isKey) { $this->key = 'user_phone_mobile'; } }
  public function getUserEmail($isKey = FALSE)                                      { array_push($this->params, 'user_email');                                        if ($isKey) { $this->key = 'user_email'; } }
  public function getUserRoleId($isKey = FALSE)                                     { array_push($this->params, 'user_role_id');                                      if ($isKey) { $this->key = 'user_role_id'; } }  

  // -----------------------------------------------------------------------
  // 
  // FILTERS
  // 
  // -----------------------------------------------------------------------
  
  public function filterUserId($value = NULL, $bool = TRUE)                                         { $this->filters['user_id']                                           = array($value, $bool); }
  public function filterUserIsActive($value = NULL, $bool = TRUE)                                   { $this->filters['user_is_active']                                    = array($value, $bool); }
  public function filterUserDateCreated($value = NULL, $bool = TRUE)                                { $this->filters['user_date_created']                                 = array($value, $bool); }
  public function filterUserDateDeleted($value = NULL, $bool = TRUE)                                { $this->filters['user_date_deleted']                                 = array($value, $bool); }
  public function filterUserDateLastLogin($value = NULL, $bool = TRUE)                              { $this->filters['user_date_last_login']                              = array($value, $bool); }
  public function filterUserDatePassword($value = NULL, $bool = TRUE)                               { $this->filters['user_date_password']                                = array($value, $bool); }
  public function filterUserName($value = NULL, $bool = TRUE)                                       { $this->filters['user_name']                                         = array($value, $bool); }
  public function filterUserNameLast($value = NULL, $bool = TRUE)                                   { $this->filters['user_name_last']                                    = array($value, $bool); }
  public function filterUserNameMiddle($value = NULL, $bool = TRUE)                                 { $this->filters['user_name_middle']                                  = array($value, $bool); }
  public function filterUserNameFirst($value = NULL, $bool = TRUE)                                  { $this->filters['user_name_first']                                   = array($value, $bool); }
  public function filterUserTitle($value = NULL, $bool = TRUE)                                      { $this->filters['user_title']                                        = array($value, $bool); }
  public function filterUserPhoneOffice($value = NULL, $bool = TRUE)                                { $this->filters['user_phone_office']                                 = array($value, $bool); }
  public function filterUserPhoneMobile($value = NULL, $bool = TRUE)                                { $this->filters['user_phone_mobile']                                 = array($value, $bool); }
  public function filterUserEmail($value = NULL, $bool = TRUE)                                      { $this->filters['user_email']                                        = array($value, $bool); }
  public function filterUserRoleId($value = NULL, $bool = TRUE)                                     { $this->filters['user_role_id']                                      = array($value, $bool); }  

} // class UserList

?>