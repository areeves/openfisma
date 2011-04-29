<?php
/**
* A Zend_Auth Authentication Adapter allowing the use of PKI Credentials
* authentication mechanism
* @category Zend
* @package Zend_Auth
* @subpackage Zend_Auth_Adapter
*/
class Fisma_Zend_Auth_Adapter_PKIEmail implements Zend_Auth_Adapter_Interface
{
    protected $_username = null;

    /**
    * Constructor
    * Everything I need is in the Apache environment variables
    */
    public function __construct($username = null)
    {
        $this->_username = $username;
    }

    /**
    * Authenticates the user
    *
    * @return Zend_Auth_Result The result of the authentication
    */
    public function authenticate()
    {
        $cert = openssl_x509_parse($_SERVER['SSL_CLIENT_CERT']);
        $altname = explode(",", $cert['extensions']['subjectAltName']);
        $emailarray = explode(":", $altname[1]);
        $email = $emailarray[1];
        $user = Doctrine::getTable('User')->findOneByUsername($this->_username);
        if (strtolower($this->_username) == strtolower($email)) {
            return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $user);
        }
        return new Zend_Auth_Result(
            Zend_Auth_Result::FAILURE,
            "Username does not match your e-mail address of " . $email
        );
    }
}
