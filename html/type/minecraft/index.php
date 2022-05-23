<?php
// This is where I'm querying all the data I need and storing it in variables.
$countplayers = $serverstatus->players->online;
$maxplayers = $serverstatus->players->max;
$titlename = $serverstatus->motd->html[0];
$title = $serverstatus->motd->clean[0];
$img = "https://api.mcsrvstat.us/icon/$ip";
$connectlink = $ip . ":" . $port;
$version = $serverstatus->version;

// If the server is offline, the variable will be empty. That' how I check if the server is online.
$status = $serverstatus->online;
if (isset($status)) {
    $status = 1;
} else {
    $status = 0;
}
?>
