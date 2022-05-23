<?php
// This is where I'm querying all the data I need and storing it in variables.
$countplayers = $battlemetrics_serverstatus->data->attributes->players;
$maxplayers = $battlemetrics_serverstatus->data->attributes->maxPlayers;
$title = $battlemetrics_serverstatus->data->attributes->name;
$titlename = $battlemetrics_serverstatus->data->attributes->name;
$img = "https://cdn.muehlhaeusler.online/img/tracker/game-logos/valheim.png";
$connectlink = "steam://connect/$ip:$port";

// If the server is offline, the variable will be empty. That' how I check if the server is online.
$status = $battlemetrics_serverstatus->data->attributes->status;
if (isset($status)) {
    $status = 1;
} else {
    $status = 0;
}
?>