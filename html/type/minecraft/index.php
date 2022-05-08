<?php
// This is where I'm querying all the data I need and storing it in variables.
$countplayers = $serverstatus->raw->vanilla->raw->players->online;
$maxplayers = $serverstatus->raw->vanilla->raw->players->max;
$title = $serverstatus->name;
$img = "https://api.mcsrvstat.us/icon/$ip";
$connectlink = $ip . ":" . $port;
$version = $serverstatus->raw->vanilla->raw->version->name;

// If the server is offline, the variable will be empty. That' how I check if the server is online.
$status = $serverstatus->raw->vanilla->raw->players->online;
if (isset($status)) {
    $status = 1;
} else {
    $status = 0;
}
?>