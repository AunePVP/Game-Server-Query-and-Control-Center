<?php
// This is where I'm querying all the data I need and storing it in variables.
$countplayers = $serverstatus->players->online;
$maxplayers = $serverstatus->players->max;
$titlename = "";
foreach ($serverstatus->description->extra as $extra) {
    $titlename.= $extra->text;
}
if (empty($titlename)) {
    $titlename = $serverstatus->description;
}
$title = $titlename;
$img = $serverstatus->favicon;
$connectlink = $ip . ":" . $gport;
$version = $serverstatus->version;

// If the server is offline, the variable will be empty. That' how I check if the server is online.
$status = $serverstatus->players->max;
if (isset($status)) {
    $status = 1;
} else {
    $status = 0;
}
?>
