<?php 
$var1 = ('gamedig --type "');
$var2 = ('" --host "');
$var3 = ('" --port "');
$var4 = ('"');
$var5 = "--requestRules: true";
$command = $var1 . $type . $var2 . $ip . $var3 . $qport .  $var4;
$serverstatus = shell_exec($command);
?>

