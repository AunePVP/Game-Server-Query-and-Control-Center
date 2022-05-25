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
$keys = (array) $serverstatus->rules;
$matched = preg_grep('/MOD\d+_s/', array_keys($keys));
foreach ($matched as $modkey) {
    $modcontent = $serverstatus->rules->{$modkey};
    if (!isset($value)) {
        $value = 0;
    } else {
        $value = $value + 1;
    }
    $modcontent = substr($modcontent, 0, 10);
    $mods[$value] = $modcontent;
}
// If the server is offline, the variable will be empty. That' how I check if the server is online.
$status = $serverstatus->info->Players;
if (isset($status)) {
    $status = 1;
} else {
    $status = 0;
}
?>
