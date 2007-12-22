<?php

require('../lib/Config.class.php');
require('../lib/Encryption.class.php');
require('../lib/Template.config.php');
require('../lib/User.class.php');


$user = new User($_DB, $_E);

print "username? ";
$username = trim (fgets(STDIN));

print "password? ";
$password = trim (fgets(STDIN));

$user->createUser();
$user->setUserName($username);
$user->setUserPassword($password);

?>
