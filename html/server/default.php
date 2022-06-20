<?php
$sip = 'example.com'; // IP or Domain of the game-server
$sport = 22; // SSH Port of the game-server
$susername = 'gameserver'; // User, who controls the server
$keypathpub = '/var/www/.ssh/idgsever_rsa.pub'; // Path to local ssh public key
$keypath = '/var/www/.ssh/idgsever_rsa'; // Path to local ssh private key
// Commands \\
$sstart = "./vhserver start"; // Command to start the server
$sstop = "./vhserver stop"; // Command to stop the server
$srestart = "./vhserver restart"; // Command to restart the server
$sbackup = "./vhserver backup"; // Command to back up the server
$supdate = "./vhserver update"; // Command to update the server

// LOG \\
$logtype = "path"; // Choose scp or path
# $logpath = ""; // Remove the #, if you get the log from a log file (/var/log/remotelogs/vhserver-console.log)
#$scpcommand = ""; // Remove the #, if you get the log with scp (cat log/console/vhserver-console.log)