<?php
/**********************************************************************
* FILE    : lib/Session.class.php
* PURPOSE : centralized user session management class
* 
* NOTES: 
* 
* Eventually, class can be extended to make use of mcrypt functions for
* encrypted storage of session variables in cookies.
* 
**********************************************************************/


// 
// CLASS DEFINITION
// 

class Session {
	
  // ----------------------------------------------------------------
  // 
  // VARIABLES
  // 
  // ----------------------------------------------------------------	
  
  // encryptor
  private $e;
  
  // session settings
  private $SESSION_TIMEOUT;
  private $SESSION_PATH;
  private $SESSION_DOMAIN;
  private $SESSION_EXPIRATION;
  private $SESSION_SECURE_ONLY;
  
  // hash texts for cookie indexes
  private $enc_session_start_time;
  private $enc_session_last_action;
  private $enc_session_user_id;
  
  // unencrypted cookie values
  private $session_start_time;
  private $session_last_action;
  private $session_user_id;
  
  
  // -----------------------------------------------------------------------
  // 
  // CLASS METHODS
  // 
  // -----------------------------------------------------------------------
  
  public function __construct($e,
							  $SESSION_TIMEOUT = NULL,
							  $SESSION_PATH = NULL,
							  $SESSION_DOMAIN = NULL,
							  $SESSION_EXPIRATION = NULL,
							  $SESSION_SECURE_ONLY = NULL
							  ) {
	
	if ($e and $SESSION_TIMEOUT and $SESSION_PATH and $SESSION_DOMAIN and $SESSION_EXPIRATION and $SESSION_SECURE_ONLY) {
	  
	  // store our encryptor
	  $this->e = $e;
	  
	  // store our session settings
	  $this->SESSION_TIMEOUT     = $SESSION_TIMEOUT;
	  $this->SESSION_PATH        = $SESSION_PATH;
	  $this->SESSION_DOMAIN      = $SESSION_DOMAIN;
	  $this->SESSION_EXPIRATION  = $SESSION_EXPIRATION;
	  $this->SESSION_SECURE_ONLY = $SESSION_SECURE_ONLY;
	  
	  // hash our cookie index texts
	  $this->enc_session_start_time  = $this->e->hash('session_start_time');
	  $this->enc_session_last_action = $this->e->hash('session_last_action');
	  $this->enc_session_user_id     = $this->e->hash('session_user_id');
 
 		// update the session action information on an active session 
 		if ($this->sessionIsActive()) {
 
	 		// grab the current time and encrypt it
			$now        = time(); 
			$cipher_now = $this->e->encrypt($now);
		
 	 		// update the last action time
			$this->session_last_action = $now;
			setcookie($this->enc_session_last_action, $cipher_now, $now + $this->SESSION_EXPIRATION, $this->SESSION_PATH); //, $this->SESSION_DOMAIN); //, $this->SESSION_SECURE_ONLY);
 
 		} 
 
	}
	
	// die on errors
	else { die('[ERROR] Session.class.php: a SESSION_* is not defined'); }
	
  } // __construct()
	
	
	public function __destruct() { 
	
		// Closing a page (thus destroying the class instance) does not end 
		// the session. The application must explicitly call endSession(). 
		
	} // __destruct() 
	
	
	public function __ToString() {
			
		// return a string of information
		return 
			"\n<pre>".
			"\nSESSION".
			"\n-------".
			"\nsession_start_time  : ".$this->session_start_time.
			"\nsession_last_action : ".$this->session_last_action.
			"\nsession_user_id     : ".$this->session_user_id.
			"\nsessionIsActive()   : ".$this->sessionIsActive().
			"\n</pre>";
		
	} // __ToString()
	

	// -----------------------------------------------------------------------
	// 
	// CLASS METHODS - PUBLIC
	// 
	// -----------------------------------------------------------------------

	public function startSession($user_id = NULL) {
		
		// only start the session if we are given a user_id
		if ($user_id) {

		  // save the user_id
		  $this->session_user_id = $user_id;
			
			// grab the current time
			$now = time();
			
			// encrypt values for to be stored as cookies
			$cipher_now     = $this->e->encrypt($now);
			$cipher_user_id = $this->e->encrypt($user_id); 
			
			// set the encrypted session cookies
			setcookie($this->enc_session_start_time,  $cipher_now, $now + $this->SESSION_EXPIRATION, $this->SESSION_PATH); //, $this->SESSION_DOMAIN); //, $this->SESSION_SECURE_ONLY);
			setcookie($this->enc_session_last_action, $cipher_now, $now + $this->SESSION_EXPIRATION, $this->SESSION_PATH); //, $this->SESSION_DOMAIN); //, $this->SESSION_SECURE_ONLY);
			setcookie($this->enc_session_user_id, $cipher_user_id, $now + $this->SESSION_EXPIRATION, $this->SESSION_PATH); //, $this->SESSION_DOMAIN); //, $this->SESSION_SECURE_ONLY);
			
			// refresh the object
			$this->getSession();
			
		} // if user_id
		
	} // startSession()
	

	public function getSession() {
		
		// grab the encrypted cookie values
		if (isset($_COOKIE[$this->enc_session_start_time]) &&
			isset($_COOKIE[$this->enc_session_last_action]) &&
			isset($_COOKIE[$this->enc_session_user_id]) ) { 

			// grab the cookie information
			$this->session_start_time  = $this->e->decrypt($_COOKIE[$this->enc_session_start_time]);
			$this->session_last_action = $this->e->decrypt($_COOKIE[$this->enc_session_last_action]);
			$this->session_user_id     = $this->e->decrypt($_COOKIE[$this->enc_session_user_id]);

		}
		
		else {
			
			$this->session_start_time  = NULL;
			$this->session_last_action = NULL;
			$this->session_user_id     = NULL;
	
		} 
				
	} // getSession()


	public function endSession() {

		// grab the current time
		$now = time();

		// set the cookies to NULL and already being expired
		setcookie($this->enc_session_start_time,  NULL, $now - $this->SESSION_EXPIRATION, $this->SESSION_PATH); //, $this->SESSION_DOMAIN); //, $this->SESSION_SECURE_ONLY);
		setcookie($this->enc_session_last_action, NULL, $now - $this->SESSION_EXPIRATION, $this->SESSION_PATH); //, $this->SESSION_DOMAIN); //, $this->SESSION_SECURE_ONLY);
		setcookie($this->enc_session_user_id,     NULL, $now - $this->SESSION_EXPIRATION, $this->SESSION_PATH); //, $this->SESSION_DOMAIN); //, $this->SESSION_SECURE_ONLY);
				
		// unset the local variables
		unset($this->session_start_time);
		unset($this->session_last_action);
		unset($this->session_user_id);
						
	} // endSession()
	

	// -----------------------------------------------------------------------
	// 
	// CLASS METHODS - STATUS
	// 
	// -----------------------------------------------------------------------
	
	public function sessionIsActive() {

		// update our session information
		$this->getSession();
		
		// grab the current time and the cookie time
		$now = time();
				
		// compare the current time to the session time
		if (($this->session_last_action + $this->SESSION_TIMEOUT) > $now) { return 1; } else { return 0; }  
		
	} // sessionIsActive()
	
	
	// -----------------------------------------------------------------------
	// 
	// ATTRIBUTE METHODS - GET
	// 
	// -----------------------------------------------------------------------

	public function getSessionUserId() { return $this->session_user_id; }


} // Session


// -----------------------------------------------------------------------------
// 
// MAIN
// 
// -----------------------------------------------------------------------------

// INCLUDES
require_once('Config.class.php');
require_once('User.class.php');
require_once('Encryption.class.php');

// check for necessary definitions
$errors = 0;

// check for session definitions
if (!$_CONFIG->SESSION_TIMEOUT())     { echo('[ERROR] Session.class.php: SESSION_TIMEOUT not defined!<br>');     $errors++; }
if (!$_CONFIG->SESSION_PATH())        { echo('[ERROR] Session.class.php: SESSION_PATH not defined!<br>');        $errors++; }
if (!$_CONFIG->SESSION_DOMAIN())      { echo('[ERROR] Session.class.php: SESSION_DOMAIN not defined!<br>');      $errors++; }
if (!$_CONFIG->SESSION_EXPIRATION())  { echo('[ERROR] Session.class.php: SESSION_EXPIRATION not defined!<br>');  $errors++; }
if (!$_CONFIG->SESSION_SECURE_ONLY()) { echo('[ERROR] Session.class.php: SESSION_SECURE_ONLY not defined!<br>'); $errors++; }

// exit on errors
if ($errors) { die('exiting from previous errors'); }

// create our session instance
$_SESSION = new Session($_E,
						$_CONFIG->SESSION_TIMEOUT(),
						$_CONFIG->SESSION_PATH(),
						$_CONFIG->SESSION_DOMAIN(),
						$_CONFIG->SESSION_EXPIRATION(),
						$_CONFIG->SESSION_SECURE_ONLY()
						);

// create our session user instnace
$_SESSION_USER = new User($_DB, $_E);

?>