<?php
// function to get last lines of file
require 'html/lloffile.php';
// This is where I'm querying all the data I need and storing it in variables.
$countplayers = 0;
foreach ($serverstatus->players as $player) {
    if(!strlen($player->Name))
        continue;
    $countplayers = $countplayers + 1;
}
$Os = $serverstatus->info->Os;
switch ($Os) {
    case 'l':
        $Os = "Linux";
        break;
    case 'w':
        $Os = "Windows";
        break;
    case 'm':
        $Os = "mac";
        break;
}
// Get uptime and round uptime for banner
$uptime = file_get_contents("query/cron/uptime/$ServerID");
$uptimebanner = str_replace(".", ",", round($uptime, 1))."%";
$uptime .= "%";
// Get last players for banner
$lastplayerlines = tailCustom("query/cron/$ServerID.json", 7);
//$lastplayers = preg_split("/\r\n|\n|\r/", $lastplayerlines);
$playerlineexit = array();
$count = 0;
foreach(preg_split("/((\r?\n)|(\r\n?))/", $lastplayerlines) as $playerline){
    $playerlinedecode = json_decode($playerline);
    $lastplayers[$count] = $playerlinedecode->players;
    $count = $count + 1;
}
$ingameday = $serverstatus->rules->DayTime_s;
$clusterid = $serverstatus->rules->ClusterId_s;
$maxplayers = $serverstatus->info->MaxPlayers;
$title = $serverstatus->info->HostName;
$titlename = $serverstatus->info->HostName;
$img = "html/img/logo/ark.webp";
$connectlink = "steam://connect/$ip:$gport";
$map = $serverstatus->info->Map;
$password = $serverstatus->rules->ServerPassword_b;
$battleye = $serverstatus->rules->SERVERUSESBATTLEYE_b;
$pve = $serverstatus->rules->SESSIONISPVE_i;
$hasmods = $serverstatus->rules->HASACTIVEMODS_i;
if ($hasmods) {
    $keys = (array) $serverstatus->rules;
    $matched = preg_grep('/MOD\d+_s/', array_keys($keys));
    foreach ($matched as $modkey) {
        $modcontent = $serverstatus->rules->{$modkey};
        if (!isset($value)) {
            $value = 0;
        } else {
            $value = $value + 1;
        }
        $modcontent = strstr($modcontent, ':', true);
        $mods[$value] = $modcontent;
    }
}
// If the server is offline, the variable will be empty. That' how I check if the server is online.
$status = $serverstatus->info->Players;
if (isset($status)) {
    $status = 1;
} else {
    $status = 0;
}
?>
