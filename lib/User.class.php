<?PHP

//
// INCLUDES
//
require_once('Database.class.php');
require_once('Encryption.class.php');


//
// CLASS DEFINITION
//

class User {

	// -----------------------------------------------------------------------
  	// 
	// VARIABLES
	// 
	// -----------------------------------------------------------------------

	private $db;
	private $e;

	// private class variables
	private $user_id;
	
	private $user_date_created;
	private $user_date_deleted;
	private $user_date_last_login;
	private $user_is_active;
	
	private $user_password;
	private $user_date_password;
	
	private $user_history_password;
	private $user_old_password1;
	private $user_old_password2;
	private $user_old_password3;

	private $user_name;	
	private $user_name_last;
	private $user_name_middle;
	private $user_name_first;
	
	private $user_title;	
	private $user_phone_office;
	private $user_phone_mobile;
	private $user_email;
	
	// may not be necessary
	private $role_id;


	// -----------------------------------------------------------------------
	// 
	// CLASS METHODS
	// 
	// -----------------------------------------------------------------------

	public function __construct($db, $e, $user_id = NULL) {

		// verify that we're given a db connection and encryptor
		if ($db && $e) {
			
			$this->db = $db;
			$this->e  = $e;
		
		 	// get User information or create a new one if none specified
			if ($user_id) { $this->getUser($user_id); }	

		}

	} // __construct()


  	public function __destruct() {

  	} // __destruct()


  	public function __ToString() {

		// return a string of information
		return 
			$this->db->__ToString().
			"<pre>".
			"\nUSER".
			"\n----".
			"\nuser_id               : ".$this->user_id.	
			"\nuser_date_created     : ".$this->user_date_created.
			"\nuser_date_deleted     : ".$this->user_date_deleted.
			"\nuser_date_last_login  : ".$this->user_date_last_login.
			"\nuser_is_active        : ".$this->user_is_active.	
			"\nuser_password         : ".$this->user_password.
			"\nuser_date_password    : ".$this->user_date_password.	
			"\nuser_history_password : ".$this->user_history_password.
			"\nuser_old_password1    : ".$this->user_old_password1.
			"\nuser_old_password2    : ".$this->user_old_password2.
			"\nuser_old_password3    : ".$this->user_old_password3.
			"\nuser_name             : ".$this->user_name.
			"\nuser_name_last        : ".$this->user_name_last.
			"\nuser_name_middle      : ".$this->user_name_middle.
			"\nuser_name_first       : ".$this->user_name_first.	
			"\nuser_title            : ".$this->user_title.
			"\nuser_phone_office     : ".$this->user_phone_office.
			"\nuser_phone_mobile     : ".$this->user_phone_mobile.
			"\nuser_email            : ".$this->user_email.
			"\n</pre>";

  	} // __ToString()


	// -----------------------------------------------------------------------
	// 
	// CLASS METHODS 
	// 
	// -----------------------------------------------------------------------

	public function validLogin($user_name = NULL, $password = NULL) {
		
		// make sure we have non-null values for user name and password
		if ($user_name && $password) {
			
			// designate our query
			$query = "SELECT user_id FROM USERS WHERE (user_name = '$user_name' AND user_password = '".$this->e->hash($password)."')";
			
			// execute the query
			$this->db->query($query);
			
			// check for results
			if ($this->db->queryOK() && $this->db->num_rows() ) { 
				
				$result = $this->db->fetch_assoc();
				return $result['user_id'];
			
			} else { return 0; }
		
		}
		
		// otherwise don't even bother checking it
		else { return 0; }
		
	} // validUser()

	public function userExists($user_id = NULL) {
		
		// make sure we have a positive, non-zero user_id
		if ($user_id) {
		
			// build our query
			$query = "SELECT user_id FROM USERS WHERE (user_id = '$user_id')";
			
			// execute the query
			$this->db->query($query);
			
			// check for results
			if ($this->db->queryOK() && $this->db->num_rows() ) { 
				
				$result = $this->db->fetch_assoc();
				return $result['user_id'];
			
			} else { return 0; }
		
		}
		
		// otherwise don't even bother checking it
		else { return 0; }
		
	} // userExists()
		
	public function getUser($user_id = NULL) {

		// make sure we have a positive, non-zero user_id
		if ($user_id && $this->userExists($user_id)) {

			// designate our retrieval query
			$query = "SELECT * FROM USERS WHERE (user_id = '$user_id')";
		
			// execute the query
			$this->db->query($query);
		
			// we got a hit, store the information
			if ($this->db->num_rows() > 0) {
		
				// retrieve the results
				$results = $this->db->fetch_assoc();
		
				// store the results locally
				$this->user_id                = $results['user_id'];	
				$this->user_date_created      = $results['user_date_created'];
				$this->user_date_deleted      = $results['user_date_deleted'];
				$this->user_date_last_login   = $results['user_date_last_login'];
				$this->user_is_active         = $results['user_is_active'];	
				$this->user_password          = $results['user_password'];
				$this->user_date_password     = $results['user_date_password'];	
				$this->user_history_password  = $results['user_history_password'];
				$this->user_old_password1     = $results['user_old_password1'];
				$this->user_old_password2     = $results['user_old_password2'];
				$this->user_old_password3     = $results['user_old_password3'];
				$this->user_name              = $results['user_name'];
				$this->user_name_last         = $results['user_name_last'];
				$this->user_name_middle       = $results['user_name_middle'];
				$this->user_name_first        = $results['user_name_first'];	
				$this->user_title             = $results['user_title'];
				$this->user_phone_office      = $results['user_phone_office'];
				$this->user_phone_mobile      = $results['user_phone_mobile'];
				$this->user_email             = $results['user_email'];
				$this->role_id             = $results['role_id'];
			
			} // if $this->db->num_rows()
		
			// user not retrieved, clear out any potential values
			else { $this->clearUser(); }
			
		} // if $user_id		
		
	} // getUser()

	
	public function createUser() {
		
		// designate our insertion query
		$query = "INSERT INTO USERS (user_id, user_date_created) VALUES (NULL, NOW())";
		
		// execute the query
		$this->db->query($query);
		
		// grab the new user_id
		$this->user_id = $this->db->insert_id();
		
		// update the internal variables
		$this->getUser($this->user_id);
		
	} // createUser()


	public function clearUser() {
		
		// clear out (non-db) user values
		unset($this->user_id);
		unset($this->user_date_created);
		unset($this->user_date_deleted);
		unset($this->user_date_last_login);
		unset($this->user_is_active);
		unset($this->user_password);
		unset($this->user_date_password);
		unset($this->user_history_password);
		unset($this->user_old_password1);
		unset($this->user_old_password2);
		unset($this->user_old_password3);
		unset($this->user_name);
		unset($this->user_name_last);
		unset($this->user_name_middle);
		unset($this->user_name_first);
		unset($this->user_title);
		unset($this->user_phone_office);
		unset($this->user_phone_mobile);
		unset($this->user_email);
		
	} // clearUser()


	public function deleteUser() {

		// 
		// REMOVES USER FROM DATABASE!
		// 	
		
		// ensure that we have an open database connection
		if ($this->db) {

			// designate our deletion query
			$query = "DELETE FROM USERS WHERE (user_id = '$this->user_id')";
			
			// execute the query
			$this->db->query($query);
			
			// destroy current object
			$this->clearUser();

		} // $this->db
		
	} // deleteUser()

	
	// -----------------------------------------------------------------------
	// 
	// ATTRIBUTE METHODS - GET
	// 
	// -----------------------------------------------------------------------

	public function getUserId()            { return $this->user_id;              }
	public function getUserIsActive()      { return $this->user_is_active;       }
	public function getUserDateCreated()   { return $this->user_date_created;    }
	public function getUserDateDeleted()   { return $this->user_date_deleted;    }
	public function getUserDateLastLogin() { return $this->user_date_last_login; }
	public function getUserDatePassword()  { return $this->user_date_password;   }
	public function getUserName()          { return $this->user_name;            }
	public function getUserNameLast()      { return $this->user_name_last;       }
	public function getUserNameMiddle()    { return $this->user_name_middle;     }
	public function getUserNameFirst()     { return $this->user_name_first;      }
	public function getUserTitle()         { return $this->user_title;           }
	public function getUserPhoneOffice()   { return $this->user_phone_office;    }
	public function getUserPhoneMobile()   { return $this->user_phone_mobile;    }
	public function getUserEmail()         { return $this->user_email;           }
	public function getUserRoleId()        { return $this->role_id;              }

	public function getValidUserIds($offset = 0, $limit = NULL) {
		
		// array to store user ids
		$id_array = array();

		// create our query
		$query = "SELECT user_id from USERS";

		// add in our offset and limit if a limit is provided
		if ($limit) { $query .= " LIMIT $offset, $limit";  }

		// execute the query
		$this->db->query($query);
	
		// evaluate the results
		if ($this->db->queryOK()) {
			
			// push the values onto the array
			while ($id = $this->db->fetch_array()) { array_push($id_array, $id[0]); }
			
		}
		
		// return the array of user_ids
		return $id_array;
		
	} // getValidUserIds
	
	public function getValidUserNames($offset = 0, $limit = NULL) {
		
		// array to store user names
		$name_array = array();

		// create our query
		$query = "SELECT user_name from USERS";

		// add in our offset and limit if a limit is provided
		if ($limit) { $query .= " LIMIT $offset, $limit";  }

		// execute the query
		$this->db->query($query);
	
		// evaluate the results
		if ($this->db->queryOK()) {
			
			// push the values onto the array
			while ($name = $this->db->fetch_array()) { array_push($name_array, $name[0]); }
			
		}
		
		// return the array of user_names
		return $name_array;
		
	} // getValidUserNames
	
	//  -----------------------------------------------------------------------
	// 
	// ATTRIBUTE METHODS - STATUS RELATED
	// 
	// -----------------------------------------------------------------------

	public function setUserIsActive($user_is_active = 0) {
		
		// deactivate the user
		if ($user_is_active == 0) {
		
			$query = 
				"UPDATE USERS ".
				"SET user_is_active = '$user_is_active' ".
				"WHERE user_id = '$this->user_id'";
			
		}
		
		// activating the user, remove deletion date
		else {
			
			$query = 
				"UPDATE USERS ".
				"SET user_is_active = '$user_is_active', user_date_deleted = NULL ".
				"WHERE user_id = '$this->user_id'"; 
			
		}
		
		// execute the query
		$this->db->query($query);
		
		// refresh the user information
		if ($this->db->queryOK()) { $this->getUser($this->user_id); }
		
	} // setUserIsActive()


	public function updateUserDateDeleted() {
		
		// designate our update query
		$query = "UPDATE USERS SET user_date_deleted = NOW() WHERE user_id = '$this->user_id'";
		
		// execute the query
		$this->db->query($query);
		
		// refresh the user status
		if ($this->db->queryOK()) { $this->getUser($this->user_id); }
		
	} // updateUserDateDeleted()
	
	
	public function clearUserDateDeleted() {
		
		// designate our update query
		$query = "UPDATE USERS SET user_date_deleted = NULL WHERE user_id = '$this->user_id'";
		
		// execute the query
		$this->db->query($query);
		
		// refresh the user status
		if ($this->db->queryOK()) { $this->getUser($this->user_id); }
		
	} // clearUserDateDeleted()
	
	
	public function updateUserDateLastLogin() {
		
		// designate our update query
		$query = "UPDATE USERS SET user_date_last_login = NOW() WHERE user_id = '$this->user_id'";
		
		// execute the query
		$this->db->query($query);
		
		// refresh the user status
		if ($this->db->queryOK()) { $this->getUser($this->user_id); }
		
	} // updateUserDateLastLogin()

	
	// -----------------------------------------------------------------------
	// 
	// ATTRIBUTE METHODS - USER PASSWORD RELATED
	// 
	// -----------------------------------------------------------------------
	
	public function setUserPassword($new_password = NULL) {

		// error check the password
		if ($new_password) {
		
			// hash the new password value
			$pw_hash = $this->e->hash($new_password);
		
			// compare the new password to the current and old passwords
			if (($pw_hash != $this->user_password) && 
				($pw_hash != $this->user_old_password1) &&
				($pw_hash != $this->user_old_password2) &&
				($pw_hash != $this->user_old_password3) ) {
		
				// designate our password update query
				$query = 
					"UPDATE USERS ".
					"SET user_old_password3 = '$this->user_old_password2', ".
					"    user_old_password2 = '$this->user_old_password1', ".
					"    user_old_password1 = '$this->user_password', ".
					"    user_password      = '$pw_hash' ".
					"WHERE  user_id = '$this->user_id'";
			
				// execute the query
				$this->db->query($query);
			
				// after query tasks
				if ($this->db->queryOK()) {
				
					// update the password time stamp and refresh the user
					$this->updateUserDatePassword();
					$this->getUser($this->user_id);
				
				}
			
			} // new password comparison
			
		} // input error check
				
	} // updateUserPassword
	
	
	public function updateUserDatePassword() {
		
		// designate our update query
		$query = "UPDATE USERS SET user_date_password = NOW() WHERE user_id = '$this->user_id'";
		
		// execute the query
		$this->db->query($query);
		
		// refresh the user status
		if ($this->db->queryOK()) { $this->getUser($this->user_id); }
		
	} // updateUserDatePassword()

	
	// -----------------------------------------------------------------------
	// 
	// ATTRIBUTE METHODS - USER NAME RELATED
	// 
	// -----------------------------------------------------------------------
	
	
	public function setUserName($user_name = NULL) {
		
		// error check the input by schema
		if (($user_name) && (strlen($user_name) <= 32 )) {
		
			// designate our update query
			$query = "UPDATE USERS SET user_name = '$user_name' WHERE user_id = '$this->user_id'";
		
			// execute the query
			$this->db->query($query);
		
			// refresh the user status
			if ($this->db->queryOK()) { $this->getUser($this->user_id); }
			
		} // input error check
		
	} // setUserName()
	
	
	public function setUserNameLast($user_name_last = NULL) {

		// error check the input by schema
		if (($user_name_last) && (strlen($user_name_last) <= 32)) {
		
			// designate our update query
			$query = "UPDATE USERS SET user_name_last = '$user_name_last' WHERE user_id = '$this->user_id'";
		
			// execute the query
			$this->db->query($query);
		
			// refresh the user status
			if ($this->db->queryOK()) { $this->getUser($this->user_id); }		

		} // input error check
		
	} // setUserNameLast()
	
	
	public function setUserNameMiddle($user_name_middle = NULL) {

		// error check the input by schema
		if (($user_name_middle) && (strlen($user_name_middle) <= 1)) {
		
			// designate our update query
			$query = "UPDATE USERS SET user_name_middle = '$user_name_middle' WHERE user_id = '$this->user_id'";
		
			// execute the query
			$this->db->query($query);
		
			// refresh the user status
			if ($this->db->queryOK()) { $this->getUser($this->user_id); }
			
		} // input error check
		
	} // setUserNameMiddle()
	
	
	public function setUserNameFirst($user_name_first = NULL) {
		
		// error check the input by schema
		if (($user_name_first) && (strlen($user_name_first) <= 32)) {
		
			// designate our update query
			$query = "UPDATE USERS SET user_name_first = '$user_name_first' WHERE user_id = '$this->user_id'";
		
			// execute the query
			$this->db->query($query);
		
			// refresh the user status
			if ($this->db->queryOK()) { $this->getUser($this->user_id); } else { print $this->db->error(); }
			
		} // error check input
		
	} // setUserNameFirst()


	//  -----------------------------------------------------------------------
	// 
	// ATTRIBUTE METHODS - USER META INFORMATION
	// 
	// -----------------------------------------------------------------------

	public function setUserTitle($user_title = NULL) {
		
		// error check the input by the schema
		if (($user_title) && (strlen($user_title) <= 32)) {
		
			// designate our update query
			$query = "UPDATE USERS SET user_title = '$user_title' WHERE user_id = '$this->user_id'";
		
			// execute the query
			$this->db->query($query);
		
			// refresh the user status
			if ($this->db->queryOK()) { $this->getUser($this->user_id); }
			
		} // input error check		
		
	} // setUserTitle()
	
	public function setUserRoleId($role_id = NULL) {
		
		// error check the input by the schema
		if (($role_id) && (strlen($role_id) <= 10)) {
		
			// designate our update query
			$query = "UPDATE USERS SET role_id = '$role_id' WHERE user_id = '$this->user_id'";
		
			// execute the query
			$this->db->query($query);
		
			// refresh the user status
			if ($this->db->queryOK()) { $this->getUser($this->user_id); }
			
		} // input error check		
		
	} // setUserRoleId()
	
	public function setUserPhoneOffice($user_phone_office = NULL) {
		
		// error check the input by the schema
		if (($user_phone_office) && (strlen($user_phone_office) <= 13)) {
		
			// designate our update query
			$query = "UPDATE USERS SET user_phone_office = '$user_phone_office' WHERE user_id = '$this->user_id'";
		
			// execute the query
			$this->db->query($query);
		
			// refresh the user status
			if ($this->db->queryOK()) { $this->getUser($this->user_id); }
			
		} // input error check
		
	} // setUserPhoneOffice()
	
	
	public function setUserPhoneMobile($user_phone_mobile = NULL) {
		
		// error check the input by the schema
		if (($user_phone_mobile) && (strlen($user_phone_mobile) <= 13)) {
		
			// designate our update query
			$query = "UPDATE USERS SET user_phone_mobile = '$user_phone_mobile' WHERE user_id = '$this->user_id'";
		
			// execute the query
			$this->db->query($query);
		
			// refresh the user status
			if ($this->db->queryOK()) { $this->getUser($this->user_id); }

		} // input error check
	
	} // setUserPhoneMobile()
	
	
	public function setUserEmail($user_email = NULL) {

		// error check the input by the schema
		if (($user_email) && (strlen($user_email) <= 64)) {

			// designate our update query
			$query = "UPDATE USERS SET user_email = '$user_email' WHERE user_id = '$this->user_id'";
		
			// execute the query
			$this->db->query($query);
		
			// refresh the user status
			if ($this->db->queryOK()) { $this->getUser($this->user_id); }
			
		} // input error check
		
	} // setUserEmail()	


} // User

?>