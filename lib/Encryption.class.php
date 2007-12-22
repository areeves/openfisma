<?php
/**********************************************************************
* FILE    : lib/Encryption.class.php
* PURPOSE : centralized encryption class
* 
* NOTES:
* 
* The private variable key is a CIPHER_HASH hash of the current date
* (without time information). This will create a rotating key that has
* a maximum life of 24 hours. 
*  
* KNOWN ISSUE:
* 
* If a new Encryption object is created to handle cipher texts created 
* the previous day, the results will be invalid. Could potentially be
* a problem with users who have active sessions during the day roll
* over. 
* 
**********************************************************************/


// 
// CLASS DEFINITION
// 

class Encryption {
	
  // -----------------------------------------------------------------------
  // 
  // VARIABLES
  // 
  // -----------------------------------------------------------------------

  private $CIPHER_HASH;
  private $CIPHER_SYMMETRIC;
  private $CIPHER_MODE;

  // 2-way encryption key
  private $key;

	
  // -----------------------------------------------------------------------
  // 
  // CLASS METHODS
  // 
  // -----------------------------------------------------------------------
	
  public function __construct($CIPHER_HASH = NULL,
							  $CIPHER_SYMMETRIC = NULL,
							  $CIPHER_MODE = NULL
							  ) {
	
	// ensure we have all the cipher values
	if ($CIPHER_HASH &&  $CIPHER_SYMMETRIC && $CIPHER_MODE) {

	  // store the values
	  $this->CIPHER_HASH      = $CIPHER_HASH;
	  $this->CIPHER_SYMMETRIC = $CIPHER_SYMMETRIC;
	  $this->CIPHER_MODE      = $CIPHER_MODE;

	  // hash the date amd store it as the key
	  $this->key = $this->hash(date('Y-m-d'));

	}

	// die on error
	else { die('[ERROR] Encryption.class.php: a CIPHER_* is not specified'); }
		
  } // __construct()
	
	
  public function __destruct() { }
	
	 
  public function __ToString() { 
		
	return			 
	  "\n<pre>".
	  "\nENCRYPTION".
	  "\n----------".
	  "\nCIPHER_HASH      : ".$this->CIPHER_HASH.
	  "\nCIPHER_SYMMETRIC : ".$this->CIPHER_SYMMETRIC.
	  "\nCIPHER_MODE      : ".$this->CIPHER_MODE.
	  "\nkey              : ".$this->key.
	  "\n".
	  "\nNOTE!".
	  "\n-----".
	  "\ntwo-way encryption is not currently functional!".
	  "\nCIPHER_SYMMETRIC, CIPHER_MODE, key are unused!".
	  "\n</pre>";
		
  } // __ToString();


  // -----------------------------------------------------------------------
  // 
  // CLASS METHODS - PRIVATE
  // 
  // -----------------------------------------------------------------------

  public function hash($str = NULL) {
		
	// create the hashed cookie names
	switch ($this->CIPHER_HASH) {
	case 'SHA1' : return sha1($str);  break;
	case 'MD5'  : return md5($str);   break;
	case 'CRC32': return crc32($str); break;
	default: return $str;
	}
		
  } // hash()
	
	
  public function encrypt($str = NULL) {
		
	// NOTE: use this until the mcrypt libraries are available
	return base64_encode($str);
		
	/*		
	 // encrypt the plain text
	 return mcrypt_encrypt($this->CIPHER_SYMMETRIC, $this->key, $str, $this->CIPHER_MODE);
	*/
		
		
  } // encrypt()
	
	
  public function decrypt($str = NULL) {
		
		
	// NOTE: use this until the mcrypt libraries are available
	return base64_decode($str); 
		
	/*
	 // decrypt the cipher text
	 return mcrypt_decrypt($this->CIPHER_SYMMETRIC, $this->key, $str, $this->CIPHER_MODE);
	*/
		
  } // decrypt()
	
} // Encryption


// -----------------------------------------------------------------------------
// 
// MAIN
// 
// -----------------------------------------------------------------------------

// require the configuration class
require_once('Config.class.php');

// check for necessary definitions
$errors = 0;

// check for encryption definitions
if (!$_CONFIG->CIPHER_HASH())      { echo('[ERROR] Encryption.class.php: CIPHER_HASH not defined!<br>');      $errors++; }
if (!$_CONFIG->CIPHER_SYMMETRIC()) { echo('[ERROR] Encryption.class.php: CIPHER_SYMMETRIC not defined!<br>'); $errors++; }
if (!$_CONFIG->CIPHER_MODE())      { echo('[ERROR] Encryption.class.php: CIPHER_MODE not defined!<br>');      $errors++; }

// exit on errors
if ($errors) { die('exiting from previous errors'); }

// create our encryption instance
$_E = new Encryption($_CONFIG->CIPHER_HASH(),
					 $_CONFIG->CIPHER_SYMMETRIC(),
					 $_CONFIG->CIPHER_MODE()
					 );

?>
