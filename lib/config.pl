#/usr/bin/perl

# ----------------------------------------------------------------------------
# FILE    : config.pl
# PURPOSE : reads in configuration values from conf/ovms.conf
# ----------------------------------------------------------------------------

package CONFIG;


# ----------------------------------------------------------------------------
# PRAGMAS
# ----------------------------------------------------------------------------

require strict;
require warnings;
require diagnostics;


# ----------------------------------------------------------------------------
# PRIVATE VARIABLES
# ----------------------------------------------------------------------------

#my $CONFIG_FILE = $ENV{'OVMS_ROOT'}."/conf/ovms.conf";
my $CONFIG_FILE = "c:/documents\ and\ settings/brian.gant/desktop/workspace/ovms/trunk/conf/ovms.conf";


# ----------------------------------------------------------------------------
# PUBLIC VARIABLES
# ----------------------------------------------------------------------------

# directory values
our $DIR_CONF;
our $DIR_LIB;
our $DIR_LOG;

# database values
our $DB_TYPE;
our $DB_HOST;
our $DB_NAME;
our $DB_USER;
our $DB_PASS;


# ----------------------------------------------------------------------------
# FUNCTIONS
# ----------------------------------------------------------------------------

sub read_config {
# NAME    : read_config
# PURPOSE : reads in configuration values from configuration file
# INPUTS  : none
# OUTPUTS : none

	# open up the file for reading
	open(FILE, $CONFIG_FILE) or die('unable to open configuration file');

	# loop through the file
	while (<FILE>) {

		# remove newlines and store the line
		$line = $_;
		chomp $line;

		# skip comment lines and empty lines
		unless ($line =~ /^#|^$/) {

			# strip out spaces
			$line =~ s/[ ]//g;
			
			# match our desired database values
			if ($line =~ /DB_TYPE=(.+)/) { $DB_TYPE = $1; }
			if ($line =~ /DB_HOST=(.+)/) { $DB_HOST = $1; }
			if ($line =~ /DB_PORT=(.+)/) { $DB_PORT = $1; }
			if ($line =~ /DB_NAME=(.+)/) { $DB_NAME = $1; }
			if ($line =~ /DB_USER=(.+)/) { $DB_USER = $1; }
			if ($line =~ /DB_PASS=(.+)/) { $DB_PASS = $1; }

			# match our desired directory values
			if ($line =~ /LUMEN_CONF=(.+)/) { $DIR_CONF = $1; }
			if ($line =~ /LUMEN_LIB=(.+)/)  { $DIR_LIB  = $1; }
			if ($line =~ /LUMEN_LOG=(.+)/)  { $DIR_LOG  = $1; }
	
		} # unless
		
	} # while FILE

	# close the file
	close FILE;

} # read_config()


sub print_config {
# NAME    : print_config
# PURPOSE : prints configuration values to the screen (DEBUGGING)
# INPUTS  : none
# OUTPUTS : configuration values

	print "DIR_CONF = " . $DIR_CONF . "\n";
	print "DIR_LIB  = " . $DIR_LIB . "\n";
	print "DIR_LOG  = " . $DIR_LOG . "\n";
	print "DB_TYPE  = " . $DB_TYPE . "\n";
	print "DB_HOST  = " . $DB_HOST . "\n";
	print "DB_PORT  = " . $DB_PORT . "\n";
	print "DB_NAME  = " . $DB_NAME . "\n";
	print "DB_USE   = " . $DB_USER . "\n";
	print "DB_PASS  = " . $DB_PASS . "\n";

} # print_config()


# ----------------------------------------------------------------------------
# MAIN LOGIC BLOCK
# ----------------------------------------------------------------------------

# read from the configuration file
read_config();
