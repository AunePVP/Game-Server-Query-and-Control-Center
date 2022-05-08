<?php 
$var1 = ('gamedig --type "');
$var2 = ('" --host "');
$var3 = ('" --port "');
$var4 = ('"');
$var5 = "--requestRules: true";
$type = $_GET['type'];
$ip = $_GET['ip'];
$qport = $_GET['qport'];
$command = $var1 . $type . $var2 . $ip . $var3 . $qport .  $var4;
$output = shell_exec($command);
echo $output;
?>

