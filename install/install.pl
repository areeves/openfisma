#!/usr/bin/perl
# ---------------------------------------------------------------------
# Test Commit
#
#
#
#
#
#
# ---------------------------------------------------------------------

package OVMS::Install;


# ---------------------------------------------------------------------
# 
# SCRIPT REQUIRES AND INCLUDES
# 
# ---------------------------------------------------------------------

require strict;

# database modules
#require DBI;
#require DBD::mysql;

# network modules
require IO::Socket;
require Net::Ping;
require Sys::Hostname;


# ---------------------------------------------------------------------
# 
# CONSTANTS
# 
# ---------------------------------------------------------------------

# default configuration file name
use constant CONFIG_FILE			=> 'config';

# default database values
use constant DEFAULT_DB_TYPE 		=> 'MYSQL';
use constant DEFAULT_DB_HOST 		=> 'localhost';
use constant DEFAULT_DB_PORT 		=> '3306';

use constant DEFAULT_DB_NAME 		=> 'ovms';
use constant DEFAULT_DB_USER 		=> 'ovms';
use constant DEFAULT_DB_PASS 		=> '1q#E5t&U9o@W4r^Y8i)P';

# default environment values
use constant DEFAULT_APP_HOST 		=> 'localhost';
use constant DEFAULT_APP_DIR 		=> '/opt/endeavor/';

# default crypto values
use constant CIPHER_HASH			=> 'SHA1';
use constant CIPHER_SYMMETRIC		=> 'MCRYPT_3DES';
use constant CIPHER_MODE			=> 'MCRYPT_MODE_ECB';

# default session values
use constant SESSION_TIMEOUT		=> 300;
use constant SESSION_PATH			=> '/';
use constant SESSION_DOMAIN			=> '.localhost.tld';
use constant SESSION_EXPIRATION		=> 24*60*60;
use constant SESSION_SECURE_ONLY	=> 1;


# ---------------------------------------------------------------------
#
# LOCAL VARIABLES
#
# ---------------------------------------------------------------------

# do not buffer output
$| = 1;

# default to *nix clear
my $clear_screen = 'clear';

# use cls on a windows machine
if ($^O =~ /win32/i) { $clear_screen = 'cls'; }


# ---------------------------------------------------------------------
#
# FLOW CONTROL AND INPUT FUNCTIONS
#
# ---------------------------------------------------------------------

sub prompt {
# Name     : prompt()
# Purpose  : prompts user for a value and provides a default entry
# Requires : none 
# Input    : $default - default value for prompt
# Output   : user input or default (upon no input)

	# grab the default value
	my $default = shift @_;
	
	# print the prompt
	print "\n[$default] ? ";
	
	# retrieve and chomp the input
	my $input = <>;
	chomp $input;

	# return the default value on no input
	if ($input =~ /^$/) { return $default; }
	else { return $input; }

} # prompt()


sub confirmed {
# Name     : confirmed()
# Purpose  : prompts user to confirm their input
# Requires : none
# Input    : user-input
# Output   : boolean result of confirmation

	# grab the parameter to be checked
	my $input = shift @_;

	# reiterate selection
	print "\nYou entered '$input', is this correct? [y/n] ";
	
	# test response
	if (prompt('n') =~ /y/i) { return 1; } 
	else { return 0; }

} # confirmed()


sub pause {
# Name     : pause()
# Purpose  : simple spin-wait pause procedure, exits on ENTER
# Requires : none
# Input    : none
# Output   : none
	
	print "\nPlease press ENTER to continue.";
	while (! <>) {}

} # pause()


# ---------------------------------------------------------------------
#
# MESSAGE ROUTINES
#
# ---------------------------------------------------------------------

sub display_warning() {
# Name     : display_warning()
# Purpose  : desplays initial 'disclaimer' warning before starting the install
# Requires : none
# Input    : none
# Output   : none

	# clear the screen
	system($clear_screen);

	# print the warning
	print "\n".
		"IMPORTANT!\n".
		"\n".
		"* If you have not already done so, please ensure that your database and web\n".
		"  servers are properly configured and running before continuing.\n".
		"\n".
		"* Once the installation process begins, any existing data in configured\n".
		"  locations will be DESTROYED. Please be sure to backup any existing data\n".
		"  in configured locations (especially database data) before selecting 'p'\n".
		"  to proceed with the installation.\n";

	# pause for contemplation
	pause();

} # display_warning()


# ---------------------------------------------------------------------
#
# BOOLEAN ROUTINES
#
# ---------------------------------------------------------------------

sub complex {
# Name     : complex()
# Purpose  : ensures that a given password meets complexity requirements
# Requires : none 
# Input    : $input - password to be checked
# Output   : boolean result of complexity test

	# grab the string to be checked
	my $input = shift @_;
	
	# check for complexity
	if ((length($input) >= 8) &&				# minimum 8 characters long
		($input =~ /[0-9]+/) &&					# one or more numbers
		($input =~ /[a-z]+/) &&					# one or more lower case letters
		($input =~ /[A-Z]+/) &&					# one or more upper case letters
		($input =~ /[!@#\$%^&*()_]+/) &&		# one or more special characters
		($input !~ /[^0-9a-zA-Z!@#\$%^&*()_]/)	# only allow above cases
		) 
		
		{ return 1; }
		
	# does not meet complexity requirements
	else { return 0; }

} # complex()


sub configured {
# Name     : configured()
# Purpose  : verifies that all necessary information is completed
# Requires : none 
# Input    : none
# Output   : boolean result of install information completeness
	
	# test for variable definitions
	if (defined($DB_TYPE) 		&&
		defined($DB_HOST) 		&&
		defined($DB_PORT) 		&&
		defined($DB_ADMIN_USER) &&
		defined($DB_ADMIN_PASS) &&
		defined($DB_NAME) 		&&
		defined($DB_USER) 		&&
		defined($DB_PASS) 		&&
		defined($APP_HOST) 		&&
		defined($APP_DIR) 		&&
		defined($APP_URL) 		&&
		defined($WEB_USER) 		&&
		defined($WEB_GROUP) 
		)		
		
		{ return 1; }
		
	# everything has not been defined
	else { return 0; }

} # configured


# ---------------------------------------------------------------------
#
# DATABASE INFORMATION GET FUNCTIONS
#
# ---------------------------------------------------------------------

sub get_db_type() {
# Name     : get_db_type()
# Purpose  : prompts the user to select database type from a list
# Requires : prompt(), confirmed(), pause()
# Input    : none
# Output   : string of database type to be used in config file

	# local variables
	my $DB_TYPE = '';
	my $input = '';
	my $input_ok = 0;

	# loop until valid input
	while (! $input_ok) {

		# clear the screen
		system($clear_screen);

		# print screen header
		print "\n".
			"Please select your database type from the following list, or x to exit:\n".
			"-----------------------------------------------------------------------\n\n";

		# prompt for the database type
		print 
			"1) MySQL (default)\n".
#			"2) Oracle\n".
#			"3) PostGreSQL\n".
			"\nx) exit install script\n";

		# prompt the user with a default value of 1
		$input = prompt('1');
	
		# test input
		if ($input =~ /^[1-3x]$/i) {

			# if we're given a x|X exit immediately
			if ($input =~ /^x$/i) { return (defined($DB_TYPE) ? $DB_TYPE : undef); }

			# confirm our input if it was not a quit
			$input_ok = confirmed($input);
			
			# set our confirmed value
			if ($input_ok) {
	
				# validate the user input
				if ($input eq '1') { $DB_TYPE = 'MYSQL'; }
				if ($input eq '2') { $DB_TYPE = 'ORACLE'; }
				if ($input eq '3') { $DB_TYPE = 'POSTGRESQL'; }

			}		

		}
	
		# exit or invalid information
		else { print "\n>>> Invalid selection.\n"; pause(); }

	} # while ! $input_ok

	# return the fruits of our labor
	return $DB_TYPE;

} # get_db_type()


sub get_db_host() {
# Name     : get_db_host()
# Purpose  : prompts the user for the database host name and pings for existence
# Requires : Net::Ping, prompt(), confirmed(), pause()
# Input    : none
# Output   : string of database host (name or IP) to be used in config file

	# local variables
	my $input = '';
	my $input_ok = 0;
	
	# loop until valid input
	while (! $input_ok) {

		# clear the screen
		system($clear_screen);

		# prompt for the database type
		print "\n".
			"Please enter the database location (IP address or hostname), or x to exit:\n".
			"--------------------------------------------------------------------------\n";

		# prompt the user with a default value of 'localhost'
		$input = prompt(defined($DB_HOST) ? $DB_HOST : DEFAULT_DB_HOST);

		# if we're given a x|X exit immediately
		if ($input =~ /^x$/i) { return (defined($DB_HOST) ? $DB_HOST : undef); }

		# prompt that we are attempting to ping
		print "\n>>> attempting to ping (UDP) '$input'... ";

		# create a pinger to see if we can hit the host
		my $p = Net::Ping->new();

		# attempt to ping the host
		if ($p->ping($input)) {

			# verify 
			print "SUCCESS!\n";
		
			# confirm our in put if it was not a quit
			$input_ok = confirmed($input);

		} # ping host test
		
		# could not ping the server
		else { print "FAILED!\n>>> please ensure that your database host is alive and correctly entered\n"; pause(); }
		
		# kill our pinger
		$p->close();

	} # while ! $input_ok

	# return the fruits of our labor
	return $input;

} # get_db_host()


sub get_db_port() {
# Name     : get_db_port()
# Purpose  : prompts the user for a port on DB_HOST and tests for connectivity
# Requires : IO::Socket, prompt(), confirm(), pause()
# Input    : none
# Output   : string of database port to be used in config file
	
	# local variables
	my $input = '';
	my $input_ok = 0;

	# loop until valid input
	while (! $input_ok) {

		# clear the screen
		system($clear_screen);

		# prompt for the database type
		print "\n". 
			"Please enter the database listener port [1-65535], or x to exit:\n".
			"----------------------------------------------------------------\n";

		# prompt the user with a default value of 3306
		$input = prompt(defined($DB_PORT) ? $DB_PORT : DEFAULT_DB_PORT);

		# if we're given a x|X exit immediately
		if ($input =~ /^x$/i) { return (defined($DB_PORT) ? $DB_PORT : undef); }

		# ensure that we have numeric input within range
		if (($input =~ /^[1-9][0-9]{0,4}$/) && ($input > 0) && ($input < 65536)) {

			# prompt that we are attempting to connect
			print "\n>>> attempting socket connection (TCP) to '$DB_HOST' on port '$input'... ";

			# let's play "Test the port for socket connections"
			my $sock = new IO::Socket::INET(PeerAddr=>$DB_HOST, PeerPort=>$input, Proto=>'tcp');
			
			# test the connection and respond appropriately
			if ($sock) {

				# verify
				print "SUCCESS!\n";

				# confirm our in put if it was not a quit
				$input_ok = confirmed($input);

			} # connect DB_HOST port
			
			# could not connect to server @ port
			else { print "FAILED!\n>>> is port '$input' open and listening?\n"; pause(); }
			
			# close the socket
			close($sock);
			
		} # valid input
		
		# not valid numeric range
		else { print "\n>>> invalid input, please ensure that your input is an integer from 1-65535\n"; pause(); }

	} # while ! $input_ok

	# return the fruits of our labor
	return $input;
	
} # get_db_port()


sub get_db_admin_user() {
# Name     : get_db_admin_user()
# Purpose  : prompts user for db admin account for schema installation
# Requires : prompt(), confirmed(), pause()
# Input    : none
# Output   : string of db admin account - only for install use
	
	# local variables
	my $input = '';
	my $input_ok = 0;

	# loop until valid input
	while (! $input_ok) {

		# clear the screen
		system($clear_screen);

		# prompt for the database type
		print "\n".
			"Please enter the database administrative account name, or x to exit:\n".
			"--------------------------------------------------------------------\n";

		# prompt the user with a default value of ovms
		$input = prompt(defined($DB_ADMIN_USER) ? $DB_ADMIN_USER : '');

		# if we're given a x|X exit immediately
		if ($input =~ /^x$/i) { return (defined($DB_ADMIN_USER) ? $DB_ADMIN_USER : undef); }

		# only allow alphanumerics, with the caveat that it does not start with a number
		if ($input =~ /^[a-zA-Z][a-zA-Z0-9]*$/i) { $input_ok = confirmed($input); } 
				
		# invalid input
		else { print "\n>>> Invalid input.\n"; pause(); }

	} # while ! $input_ok

	# return the fruits of our labor
	return $input;
	
} # get_db_admin_user()


sub get_db_admin_pass() {
# Name     : get_db_admin_pass()
# Purpose  : prompts user for db admin account passwrod for schema install
# Requires : prompt(), confirmed(), pause()
# Input    : none
# Output   : string of db admin account password - only for install use
	
	# local variables
	my $input = '';
	my $input_ok = 0;

	# loop until valid input
	while (! $input_ok) {

		# clear the screen
		system($clear_screen);

		# prompt for the database type
		print "\n".
			"Please enter the database administrative account password, or x to exit:\n".
			"------------------------------------------------------------------------\n";

		# prompt the user with a default value of ovms
		$input = prompt(defined($DB_ADMIN_PASS) ? $DB_ADMIN_PASS : '');

		# if we're given a x|X exit immediately
		if ($input =~ /^x$/i) { return (defined($DB_ADMIN_PASS) ? $DB_ADMIN_PASS : undef) }

		# do not allow blank passwords
		if ($input =~ /^$/) { print "\n>>> Invalid input.\n"; pause(); }

		# we make no presumption
		else {

			# confirm our in put if it was not a quit
			$input_ok = confirmed($input);

			# set our confirmed value
			if (($input_ok) && ($input eq '12345')) { 
				print "\n>>> 12345?? Quick! Somebody change the password to my luggage!"; sleep(1);
			}
				
		}

	} # while ! $input_ok

	# return the fruits of our labor
	return $input;
	
} # get_db_admin_pass()


sub get_app_db_name() {
# Name     : get_app_db_name()
# Purpose  : prompts user for application schema name
# Requires : prompt(), confirmed(), pause()
# Input    : none
# Output   : string of schema name to be used by the application
	
	# local variables
	my $input = '';
	my $input_ok = 0;

	# loop until valid input
	while (! $input_ok) {

		# clear the screen
		system($clear_screen);

		# prompt for the database type
		print "\n".
			"Please enter the OVMS database name, or x to exit:\n".
			"--------------------------------------------------\n";

		# prompt the user with a default value of ovms
		$input = prompt(defined($DB_NAME) ? $DB_NAME : DEFAULT_DB_NAME);

		# if we're given a x|X exit immediately
		if ($input =~ /^x$/i) { return (defined($DB_NAME) ? $DB_NAME : undef); }

		# do not allow blank input
		if ($input =~ /^$/) { print "\n>>> Invalid input.\n"; pause(); }

		# non-blank input
		else  { $input_ok = confirmed($input); }

	} # while ! $input_ok

	# return the fruits of our labor
	return $input;
	
} # get_app_db_name()


sub get_app_db_user() {
# Name     : get_app_db_user()  
# Purpose  : prompts user for application account name
# Requires : prompt(), confirmed(), pause()
# Input    : none
# Output   : string for application database account

	# local variables
	my $input = '';
	my $input_ok = 0;

	# loop until valid input
	while (! $input_ok) {

		# clear the screen
		system($clear_screen);

		# prompt for the database type
		print "\n".
			"Please enter the OVMS database account name, or x to exit:\n".
			"----------------------------------------------------------\n";

		# prompt the user with a default value of ovms
		$input = prompt(defined($DB_USER) ? $DB_USER : DEFAULT_DB_USER);

		# if we're given a x|X exit immediately
		if ($input =~ /^x$/i) { return (defined($DB_USER) ? $DB_USER : undef); }

		# do not allow blank input
		if ($input =~ /^$/) { print "\n>>> Invalid input.\n"; pause(); }

		# non-blank input
		else  { $input_ok = confirmed($input); }
		
	} # while ! $input_ok

	# return the fruits of our labor
	return $input;
	
} # get_app_db_user()


sub get_app_db_pass() {
# Name     : get_app_db_pass()
# Purpose  : prompts user for account password and checks for complexity
# Requires : prompt(), confirmed(), pause(), complex()
# Input    : none
# Output   : string for application account password

	# local variables
	my $input = '';
	my $input_ok = 0;

	# loop until valid input
	while (! $input_ok) {

		# clear the screen
		system($clear_screen);

		# prompt for the database type
		print "\n".
			"Please enter the OVMS database account password, or x to exit:\n".
			"--------------------------------------------------------------\n\n".
			"Password must:\n".
			"- be a minimum of 8 characters in length\n".
			"- contain one number [0-9]\n".
			"- contain one lower case letter [a-z]\n".
			"- contain one upper case letter [A-Z]\n".
			"- contain one special character [!@#\$%^&*()_]\n".
			"- only contain alphanumerics and listed special characters\n";

		# prompt the user with a default value of ovms
		$input = prompt(defined($DB_PASS) ? $DB_PASS : '');

		# if we're given a x|X exit immediately
		if ($input =~ /^x$/i) { return (defined($DB_PASS) ? $DB_PASS : undef); }

		# do not allow blank input
		if ($input =~ /^$/) { print "\n>>> Invalid input.\n"; pause(); }

		# non-blank input
		else  {

			# check our input for complexity
			if (complex($input)) { $input_ok = confirmed($input); }
			
			# does not meet complexity requirements
			else { print "\n>>> '$input' contains illegal characters or is not sufficiently complex\n"; pause(); }
			
		}

	} # while ! $input_ok
	
	# return the fruits of our labor
	return $input;
	
} # get_app_db_pass()


sub get_app_host() {
# Name     : get_app_host()
# Purpose  : prompts the user for the application install host name
# Requires : Sys::Hostname, prompt(), confirmed(), pause()
# Input    : none
# Output   : string of application host to be used for db connection

	# local variables
	my $input = '';
	my $input_ok = 0;
	
	# loop until valid input
	while (! $input_ok) {

		# clear the screen
		system($clear_screen);

		# display header
		print "\n".
			"Please enter the hostname where the application will be installed, or x to exit:\n".
			"--------------------------------------------------------------------------------\n\n";
		
		# detect the hostname
		my $detected_hostname = Sys::Hostname::hostname();			
		
		# display detected host
		print "We have determined your hostname to be '".$detected_hostname."'.\n\n";

		# prompt
		print "Use this?";
		$input = prompt('n');

		# use the detected name
		if ($input =~ /y/i) { return $detected_hostname; }
		
		# prompt for other name with default value
		else {

			# prompt the user with a default value of 'localhost'
			$input = prompt(defined($APP_HOST) ? $APP_HOST : DEFAULT_APP_HOST);

			# if we're given a x|X exit immediately
			if ($input =~ /^x$/i) { return (defined($APP_HOST) ? $APP_HOST : undef); }

			# prompt for approval of input
			else {

				# confirm our in put if it was not a quit
				$input_ok = confirmed($input);

			}
			
		}

	} # while ! $input_ok

	# return the fruits of our labor
	return $input;

} # get_app_host()


sub get_app_url() {
# Name     : get_app_url()
# Purpose  : prompts user for application url
# Requires : prompt(), confirmed(), pause()
# Input    : none
# Output   : string for application url used in config file

	# local variables
	my $input = '';
	my $input_ok = 0;

	# loop until valid input
	while (! $input_ok) {

		# clear the screen
		system($clear_screen);

		# prompt for the database type
		print "\n".
			"Please enter the OVMS application URL, or x to exit:\n".
			"----------------------------------------------------\n\n";
		
		# prompt the user with a default value of ovms
		$input = prompt(defined($APP_URL) ? $APP_URL : '');

		# if we're given a x|X exit immediately
		if ($input =~ /^x$/i) { return (defined($APP_URL) ? $APP_URL : undef); }

		# do not allow blank input
		if ($input =~ /^$/) { print "\n>>> Invalid input.\n"; pause(); }

		# confirm input
		else  { $input_ok = confirmed($input); }

	} # while ! $input_ok
	
	# return the fruits of our labor
	return $input;
	
} # get_app_url()


sub get_app_dir() {
# Name     : get_app_dir()
# Purpose  : prompts user for application install directory
# Requires : prompt(), confirmed(), pause()
# Input    : none
# Output   : string for application install directory

	# local variables
	my $input = '';
	my $input_ok = 0;

	# loop until valid input
	while (! $input_ok) {

		# clear the screen
		system($clear_screen);

		# prompt for the database type
		print "\n".
			"Please enter the OVMS application install directory, or x to exit:\n".
			"------------------------------------------------------------------\n\n";
		
		# prompt the user with a default value of ovms
		$input = prompt(defined($APP_DIR) ? $APP_DIR : DEFAULT_APP_DIR);

		# if we're given a x|X exit immediately
		if ($input =~ /^x$/i) { return (defined($APP_DIR) ? $APP_DIR : undef); }

		# do not allow blank input
		if ($input =~ /^$/) { print "\n>>> Invalid input.\n"; pause(); }

		# confirm input
		else  { $input_ok = confirmed($input); }

	} # while ! $input_ok
	
	# return the fruits of our labor
	return $input;
	
} # get_app_dir()


sub get_web_user() {
# Name     : get_web_user()
# Purpose  : prompts user web server user
# Requires : prompt(), confirmed(), pause()
# Input    : none
# Output   : string for web server user

	# local variables
	my $input = '';
	my $input_ok = 0;

	# loop until valid input
	while (! $input_ok) {

		# clear the screen
		system($clear_screen);

		# prompt for the database type
		print "\n".
			"Please enter the web server user, or x to exit:\n".
			"-----------------------------------------------\n\n";
		
		# prompt the user with a default value of ovms
		$input = prompt(defined($WEB_USER) ? $WEB_USER : '');

		# if we're given a x|X exit immediately
		if ($input =~ /^x$/i) { return (defined($WEB_USER) ? $WEB_USER : undef); }

		# do not allow blank input
		if ($input =~ /^(?:|[^0-9a-zA-Z_])$/) { print "\n>>> Invalid input.\n"; pause(); }

		# confirm input
		else  { $input_ok = confirmed($input); }

	} # while ! $input_ok
	
	# return the fruits of our labor
	return $input;
	
} # get_web_user()


sub get_web_group() {
# Name     : get_web_group()
# Purpose  : prompts user web server group
# Requires : prompt(), confirmed(), pause()
# Input    : none
# Output   : string for web server group

	# local variables
	my $input = '';
	my $input_ok = 0;

	# loop until valid input
	while (! $input_ok) {

		# clear the screen
		system($clear_screen);

		# prompt for the database type
		print "\n".
			"Please enter the web server group, or x to exit:\n".
			"------------------------------------------------\n\n";
		
		# prompt the user with a default value of ovms
		$input = prompt(defined($WEB_GROUP) ? $WEB_GROUP : '');

		# if we're given a x|X exit immediately
		if ($input =~ /^x$/i) { return (defined($WEB_GROUP) ? $WEB_GROUP : undef); }

		# do not allow blank input
		if ($input =~ /^(?:|[^0-9a-zA-Z_])$/) { print "\n>>> Invalid input.\n"; pause(); }

		# confirm input
		else  { $input_ok = confirmed($input); }

	} # while ! $input_ok
	
	# return the fruits of our labor
	return $input;
	
} # get_web_group()


# ---------------------------------------------------------------------
#
# CONFIGURATION SUBROUTINES
#
# ---------------------------------------------------------------------

sub clear_values() {
# Name     : clear_values()
# Purpose  : clears all stored values
# Requires : none 
# Input    : none
# Output   : none

	# database type and location
	$DB_TYPE 		= undef;
	$DB_HOST 		= undef;
	$DB_PORT 		= undef;

	# database admin account
	$DB_ADMIN_USER 	= undef;
	$DB_ADMIN_PASS 	= undef;

	# application database information
	$DB_NAME 		= undef;
	$DB_USER 		= undef;
	$DB_PASS 		= undef;

	# application environment information
	$APP_HOST		= undef;
	$APP_DIR 		= undef;
	$APP_URL 		= undef;

	# web server user and group
	$WEB_USER		= undef;
	$WEB_GROUP		= undef;

} # clear_values()


sub configure_all {
# Name     : configure_all()
# Purpose  : provides a wrapper around each configuration sub
# Requires : get_db_type(), get_db_host(), get_db_port(),
#            get_db_admin_user(), get_db_admin_pass(),
#            get app_db_name(), get_app_db_user(), get_app_db_pass()
#            get_web_user(), get_web_group()
# Input    : none
# Output   : none
	
	# NOTE: we fire off the first subroutine and then test the return values
	# to see if we defined anything (quitting in get_* subs return undef and
	# signal us to quit out to the main menu				
				 
	# database administrative information
	$DB_TYPE = get_db_type();
				
	# begin cascading checks
	if (defined($DB_TYPE)) 			{ $DB_HOST 			= get_db_host(); 		}
	if (defined($DB_HOST)) 			{ $DB_PORT 			= get_db_port(); 		}
	if (defined($DB_PORT)) 			{ $DB_ADMIN_USER 	= get_db_admin_user(); 	}				
	if (defined($DB_ADMIN_USER)) 	{ $DB_ADMIN_PASS 	= get_db_admin_pass(); 	}
	if (defined($DB_ADMIN_PASS)) 	{ $DB_NAME 			= get_app_db_name(); 	}				
	if (defined($DB_NAME))			{ $DB_USER 			= get_app_db_user(); 	}
	if (defined($DB_USER)) 			{ $DB_PASS 			= get_app_db_pass(); 	}
	if (defined($DB_PASS)) 			{ $APP_DIR			= get_app_dir(); 		}				
	if (defined($APP_HOST)) 		{ $APP_HOST			= get_app_host();		}
	if (defined($APP_DIR)) 			{ $APP_URL			= get_app_url();		}
	if (defined($APP_URL)) 			{ $WEB_USER 		= get_web_user();		}
	if (defined($WEB_USER)) 		{ $WEB_GROUP		= get_web_group();		}

} # configure_all()


sub configure_defaults {
# Name     : configure_defaults()
# Purpose  : configures default values
# Requires : none
# Input    : none
# Output   : none

	# configure default values
	$DB_TYPE 		= DEFAULT_DB_TYPE;
	$DB_HOST 		= DEFAULT_DB_HOST;
	$DB_PORT 		= DEFAULT_DB_PORT;				
	$DB_NAME 	= DEFAULT_DB_NAME;
	$DB_USER 	= DEFAULT_DB_USER;
	$DB_PASS 	= DEFAULT_DB_PASS;
	$APP_HOST 		= DEFAULT_APP_HOST;				
	$APP_DIR 		= DEFAULT_APP_DIR;

} # configure_defaults()


sub configure_remaining {
# Name     : configure_remaining()
# Purpose  : provides a wrapper to configure only undefined values
# Requires : get_db_type(), get_db_host(), get_db_port(),
#            get_db_admin_user(), get_db_admin_pass(),
#            get app_db_name(), get_app_db_user(), get_app_db_pass()
#            get_web_user(), get_web_group()
# Input    : none
# Output   : none

	# initialize to non-undef value
	my $last_return = 'oogadeeboogadee';

	if (defined($last_return) && ! defined($DB_TYPE)) 		{ $last_return = get_db_type(); 		$DB_TYPE = $last_return; 		}
	if (defined($last_return) && ! defined($DB_HOST)) 		{ $last_return = get_db_host(); 		$DB_HOST = $last_return; 		}
	if (defined($last_return) && ! defined($DB_PORT)) 		{ $last_return = get_db_port();			$DB_PORT = $last_return; 		}
	if (defined($last_return) && ! defined($DB_ADMIN_USER)) { $last_return = get_db_admin_user(); 	$DB_ADMIN_USER = $last_return; 	}
	if (defined($last_return) && ! defined($DB_ADMIN_PASS)) { $last_return = get_db_admin_pass(); 	$DB_ADMIN_PASS = $last_return; 	}				
	if (defined($last_return) && ! defined($DB_NAME))		{ $last_return = get_app_db_name(); 	$DB_NAME = $last_return; 		}
	if (defined($last_return) && ! defined($DB_USER)) 		{ $last_return = get_app_db_user(); 	$DB_USER = $last_return; 		}
	if (defined($last_return) && ! defined($DB_PASS)) 		{ $last_return = get_app_db_pass();		$DB_PASS = $last_return; 		}				
	if (defined($last_return) && ! defined($APP_HOST)) 		{ $last_return = get_app_host();		$APP_HOST = $last_return; 		}
	if (defined($last_return) && ! defined($APP_DIR)) 		{ $last_return = get_app_dir();			$APP_DIR = $last_return; 		}
	if (defined($last_return) && ! defined($APP_URL)) 		{ $last_return = get_app_url();			$APP_URL = $last_return; 		}
	if (defined($last_return) && ! defined($WEB_USER)) 		{ $last_return = get_web_user();		$WEB_USER  = $last_return; 		}
	if (defined($last_return) && ! defined($WEB_GROUP)) 	{ $last_return = get_web_group();		$WEB_GROUP = $last_return; 		}

} # configure_remaining()


sub configure {
# Name     : configure()
# Purpose  : provides a container menu for user to review and update db info
# Requires : prompt(), confirmed(), pause()
#            get_db_type(), get_db_host(), get_db_port(),
#            get_db_admin_user(), get_db_admin_pass(),
#            get app_db_name(), get_app_db_user(), get_app_db_pass()
# Input    : none
# Output   : none

	# variables for menu handling
	my $input = '';
	my $input_ok = 0;
	my $last_choice = undef;

	# loop until valid input
	while (! $input_ok) {

		# clear the screen
		system($clear_screen);

		# print to command
		print "\n".
			"Please select from the following options to configure your OVMS installation.\n\n";


		# print the menu options
		print 
			" 1) update database type                    [currently '$DB_TYPE']\n".
			" 2) update database host                    [currently '$DB_HOST']\n".
			" 3) update database port                    [currently '$DB_PORT']\n".
			" 4) update database administrator name      [currently '$DB_ADMIN_USER']\n".
			" 5) update database administrator password  [currently '$DB_ADMIN_PASS']\n".
			" 6) update OVMS database name               [currently '$DB_NAME']\n".
			" 7) update OVMS database account name       [currently '$DB_USER']\n".
			" 8) update OVMS database account password   [currently '$DB_PASS']\n".
			" 9) update OVMS application hostname        [currently '$APP_HOST']\n".
			"10) update OVMS installation directory      [currently '$APP_DIR']\n".
			"11) update OVMS web-accessible URL          [currently '$APP_URL']\n".
			"12) update web server user                  [currently '$WEB_USER']\n".
			"13) update web server group                 [currently '$WEB_GROUP']\n\n";

		# give contextual menu options
		print " a) update all values         c) clear all values\n";
		print " d) update with defaults      r) update remaining values\n\n";
		
		# give proceed and exit options if configured
		if (configured()) { print " p) proceed with install\n"; }
		
		# give exit option
		print " x) exit installation\n";

		# prompt the user to continue if configured
		if (configured()) { $input = prompt('p'); }
		
		# otherwise prompt depending on last choice
		else { 
			
			# if the last choice was the defaults, default to remaining values
			if ($last_choice eq 'd') { $input = prompt('r'); }
			
			# otherwise, default to default values prompt
			else { $input = prompt('d'); }
			
		}
	
		# test input
		if ($input =~ /^[1-9][0-9]?|[acdprx]$/i) {
			
			# store the last choice for prompting purposes
			$last_choice = $input;

			# update individual settings
			if ($input eq '1')  { $DB_TYPE 			= get_db_type(); 		}
			if ($input eq '2')  { $DB_HOST 			= get_db_host(); 		}
			if ($input eq '3')  { $DB_PORT 			= get_db_port(); 		}
			if ($input eq '4')  { $DB_ADMIN_USER 	= get_db_admin_user();	}
			if ($input eq '5')  { $DB_ADMIN_PASS 	= get_db_admin_pass();	}
			if ($input eq '6')  { $DB_NAME 			= get_app_db_name(); 	}
			if ($input eq '7')  { $DB_USER 			= get_app_db_user(); 	} 
			if ($input eq '8')  { $DB_PASS 			= get_app_db_pass(); 	}
			if ($input eq '9')  { $APP_HOST 		= get_app_host(); 		}
			if ($input eq '10') { $APP_DIR 			= get_app_dir(); 		}
			if ($input eq '11') { $APP_URL 			= get_app_url();	 	}
			if ($input eq '12') { $WEB_USER			= get_web_user();	 	}
			if ($input eq '13') { $WEB_GROUP		= get_web_group();	 	}
			
			# update all
			if ($input =~ /a/i) { configure_all(); }
			
			# continue with the installation
			if ($input =~ /c/i) { clear_values(); }
			
			# update with defaults
			if ($input =~ /d/i) { configure_defaults(); }			

			# update remaining
			if ($input =~ /r/i) { configure_remaining(); }

			# proceed with install
			if ($input =~ /p/i && configured()) { $input_ok = 1; }

			# simply exit if we're given an x|X
			if ($input =~ /x/i) { $input_ok = 1; }
		}

	} # while ! $input_ok
	
	# return the fruits of our labor
	return $input;
	
} # configure()


#----------------------------------------------------------------------
#
# INSTALLATION SUBROUTINES
#
#----------------------------------------------------------------------

sub create_app_schema {
# Name     : create_app_schema()
# Purpose  : creates the OVMS database schema, prompts for deletion if
#            schema already exists
# Requires : get_app_db_name()
# Input    : none
# Output   : none

	# give warning for
	print "create_app_schema()\n";

	# try to connect to the database as administrator


} # create_app_schema()


sub create_app_user() {
# Name     : create_app_user()
# Purpose  : creates the OVMS database user
# Requires : get_app_db_name()
# Input    : none
# Output   : none

	# 
	print "create_app_user()\n";

} # create_app_user()

#----------------------------------------------------------------------
#
# VARIABLE INITIALIZATION
#
#----------------------------------------------------------------------

sub crypto_comments {
# Name     : crypto_comments()
# Purpose  : provides the lovely comments for the crypto config section
# Requires : none
# Input    : none
# Output   : string of comments for conf/config file

	return
		"# \n".
		"# ENCRYPTION CONFIGURATION\n".
		"# \n".
		"# This section configures the encryption cipher algorithms used by the\n".
		"# application. Symmetric ciphers are implemented by PHP's interface to\n".
		"# the mcrypt (preferrably >2.5.5) command and library. See the php.net\n".
		"# manual page and the mcrypt home page for further information.\n".
		"# \n".
		"# http://mcrypt.sourceforge.net\n".
		"# http://www.php.net/manual/en/ref.mcrypt.php\n".
		"# \n".
		"# All federal information systems should refer to \"FIPS 140-2 Annex A:\n". 
		"# Approved Security Functions for FIPS PUB 140-2, Security Requirements\n".
		"# For Cryptographic Modules\" for acceptable cipher algorithms\n".
		"# \n".
		"# http://csrc.nist.gov/publications/fips/fips140-2/fips1402annexa.pdf\n".
		"# \n".
		"# CIPHER_HASH specifies one-way (hashing) encryption algorithm\n".
		"# - supported ciphers: SHA1, MD5, CRC32\n".
		"# \n".
		"# CYPHER_SYMMETRIC specifies the two-way encryption algorithm\n".
		"# - supported ciphers: MCRYPT_DES, MCRYPT_3DES, MCRYPT_SKIPJACK\n".
		"# - note: AES encryption is also acceptible under FIPS 140-2A but is not\n".
		"#   implemented in mcrypt for PHP\n".
		"# \n".
		"# CIPHER_MODE specifies the block cipher mode for symmetric encryption\n".
		"# - supported modes: MCRYPT_MODE_ECB, MCRYPT_MODE_CBC, MCRYPT_MODE_CFB,\n".
		"#   MCRYPT_MODE_OFB, MCRYPT_MODE_NOFB, MCRYPT_MODE_STREAM\n".
		"\n";

} # crypto_comments() 


sub db_comments {
# Name     : db_comments()
# Purpose  : provides the lovely comments for the DB config section
# Requires : none
# Input    : none
# Output   : string of comments for conf/config file

	return
		"# \n".
		"# DATABASE CONFIGURATION\n".
		"# \n".
		"# This section defines the basic information needed to connect to the\n".
		"# application's database backend.\n".
		"# \n".
		"# DB_TYPE - type of database connection\n".
		"# DB_HOST - location (IP or URL) of database connection\n".
		"# DB_PORT - port to connect to\n".
		"# DB_USER - user name with which to connect\n".
		"# DB_PASS - user password with which to connect\n".
		"# DB_NAME - database name to use\n".
		"\n";

} # db_comments()


sub env_comments {
# Name     : db_comments()
# Purpose  : provides the lovely comments for the environment config section
# Requires : none
# Input    : none
# Output   : string of comments for conf/config file
	
	return 
		"# \n".
		"# ENVIRONMENT CONFIGURATION\n".
		"# \n".
		"# Use this section to describe the application's directory structure\n".
		"# and locations, both physically on the server and from the web.\n".
		"# \n".
		"# APP_HOST specifies the local host name or IP address for the application\n".
		"# APP_URL specifies the www-accessible address for the application\n".
		"# APP_DIR specifies the installation directory of the application\n".
		"\n";

} # env_comments()


sub session_comments {
# Name     : session_comments()
# Purpose  : provides the lovely comments for the session config section
# Requires : none
# Input    : none
# Output   : string of comments for conf/config file

	return
		"# \n".
		"# SESSION CONFIGURATION\n".
		"# \n".
		"# This section defines the parameters for the user's session within\n".
		"# the application\n".
		"# \n".
		"# SESSION_TIMEOUT specifies the maximum idle time before a forced logout\n".
		"# SESSION_PATH specifies the path on the server which the cookie is valid\n".
		"# SESSION_DOMAIN specfies the domain over which the cookie is valid\n".
		"# SESSION_EXIPRATION specifies the length of time (in seconds) until the\n".
		"#   cookie expires in the browser if left open\n".
		"# SESSION_SECURE_ONLY specifies that the cookie should only be set over\n".
		"#    a secure connection\n".
		"\n";

} # session comments


sub write_config() {
# Name     : write_config()
# Purpose  : writes the configuration values out to conf/config
# Requires : none
# Input    : none
# Output   : conf/config configuration file

	# test for file existence 
	if (-e CONFIG_FILE) { print "configuration file exists, overwriting\n"; }
	else { print "configuration file does not exist, creating new\n"; }

	# open our configuration file
	open (CONFIG, '>'.CONFIG_FILE) || die("FATAL ERROR: unable to open config file for writing\n");

	# environment comments
	print CONFIG env_comments();
	print CONFIG 'APP_HOST='.$APP_HOST."\n";
	print CONFIG 'APP_DIR='.$APP_DIR."\n";
	print CONFIG 'APP_URL='.$APP_URL."\n";
	print CONFIG "\n";
	print CONFIG 'APP_CONF='.$APP_DIR."conf/\n";
	print CONFIG 'APP_LIB='.$APP_DIR."lib/\n";
	print CONFIG 'APP_LOG='.$APP_DIR."log/\n";
	print CONFIG 'APP_WWW='.$APP_DIR."www/\n";
	print CONFIG "\n";

	# crypto configuration
	print CONFIG crypto_comments();
	print CONFIG 'CIPHER_HASH='.CIPHER_HASH."\n";
	print CONFIG 'CIPHER_SYMMETRIC='.CIPHER_SYMMETRIC."\n";
	print CONFIG 'CIPHER_MODE='.CIPHER_MODE."\n";
	print CONFIG "\n";

	# session configuration
	print CONFIG session_comments();
	print CONFIG 'SESSION_TIMEOUT='.SESSION_TIMEOUT."\n";
	print CONFIG 'SESSION_PATH='.SESSION_PATH."\n";
	print CONFIG 'SESSION_DOMAIN='.SESSION_DOMAIN."\n";
	print CONFIG 'SESSION_EXPIRATION='.SESSION_EXPIRATION."\n";
	print CONFIG 'SESSION_SECURE_ONLY='.SESSION_SECURE_ONLY."\n";
	print CONFIG "\n";
	
	# database configuration
	print CONFIG db_comments();
	print CONFIG 'DB_TYPE='.$DB_TYPE."\n";
	print CONFIG 'DB_HOST='.$DB_HOST."\n";
	print CONFIG 'DB_PORT='.$DB_PORT."\n";
	print CONFIG 'DB_USER='.$DB_USER."\n";
	print CONFIG 'DB_PASS='.$DB_PASS."\n";
	print CONFIG 'DB_NAME='.$DB_NAME."\n";

	# close our configuration file
	close(CONFIG);

} # write_config()


#----------------------------------------------------------------------
#
# VARIABLE INITIALIZATION
#
#----------------------------------------------------------------------

# database type and location
my $DB_TYPE 		= undef;
my $DB_HOST 		= undef;
my $DB_PORT 		= undef;

# database admin account
my $DB_ADMIN_USER 	= undef;
my $DB_ADMIN_PASS 	= undef;

# application database information
my $DB_NAME 		= undef;
my $DB_USER 		= undef;
my $DB_PASS 		= undef;

# application environment information
my $APP_HOST 		= undef;
my $APP_DIR 		= undef;
my $APP_URL 		= undef;

# web server user and group
my $WEB_USER		= undef;
my $WEB_GROUP		= undef;


#----------------------------------------------------------------------
#
# DEBUG / TEST BLOCK
#
#----------------------------------------------------------------------

#write_config();
#exit;


#----------------------------------------------------------------------
#
# INSTALL SCRIPT BODY
#
#----------------------------------------------------------------------

# display the initial warning
display_warning();

# proceed if configured
if ((configure() =~ /p/i) && configured()) {

	# clear the screen
	system($clear_screen);

	# write the values to the config file
	write_config();

	# verify that we can connect to the database
	verify_db_admin();

	# try to create the schema
	#create_app_schema();
	
	# create the user
	#create_app_user();
	
	# verify that our target destination exists and is writable


}

# exit the installation application
exit;