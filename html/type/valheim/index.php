<?php
// This is where I'm querying all the data I need and storing it in variables.
$countplayers = $serverstatus->info->Players;
$maxplayers = $serverstatus->info->MaxPlayers;
$title = $serverstatus->info->HostName;
$titlename = $serverstatus->info->HostName;
$img = "https://cdn.muehlhaeusler.online/img/tracker/game-logos/valheim.webp";
$connectlink = "steam://connect/$ip:$gport";

// If the server is offline, the variable will be empty. That' how I check if the server is online.
$status = $serverstatus->info->Protocol;
if (isset($status)) {
    $status = 1;
} else {
    $status = 0;
}
?>