<?php
// This is where I'm querying all the data I need and storing it in variables.
$countplayers = $battlemetrics_serverstatus->data->attributes->players;
$maxplayers = $serverstatus->maxplayers;
$title = $serverstatus->name;
$img = "html/img/game-logos/ark.png";
$connectlink = "steam://connect/$ip:$gport";
$map = $serverstatus->map;
$ping = $serverstatus->ping;
// If the server is offline, the variable will be empty. That' how I check if the server is online.
$status = $serverstatus->raw->numplayers;
if (isset($status)) {
    $status = 1;
} else {
    $status = 0;
}
?>
