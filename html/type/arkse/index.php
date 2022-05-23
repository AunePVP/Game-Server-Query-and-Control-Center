<?php
// This is where I'm querying all the data I need and storing it in variables.
$countplayers = 0;
foreach ($serverstatus->players as $player) {
    if(!strlen($player->Name))
        continue;
    $countplayers = $countplayers + 1;
}
//$countplayers = $battlemetrics_serverstatus->data->attributes->players;
$maxplayers = $serverstatus->info->MaxPlayers;
$title = $serverstatus->info->HostName;
$titlename = $serverstatus->info->HostName;
$img = "https://cdn.muehlhaeusler.online/img/tracker/game-logos/ark.png";
$connectlink = "steam://connect/$ip:$gport";
$map = $serverstatus->info->Map;
// If the server is offline, the variable will be empty. That' how I check if the server is online.
$status = $serverstatus->info->Players;
if (isset($status)) {
    $status = 1;
} else {
    $status = 0;
}
?>
