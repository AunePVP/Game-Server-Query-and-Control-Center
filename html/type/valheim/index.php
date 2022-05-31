<?php
// function to get last lines of file
require 'html/lloffile.php';
// Get uptime and round uptime for banner
$uptime = file_get_contents("query/cron/uptime/$ServerID");
$uptimebanner = str_replace(".", ",", round($uptime, 1))."%";
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
// This is where I'm querying all the data I need and storing it in variables.
$uptime = file_get_contents("query/cron/uptime/$ServerID")."%";
$countplayers = $serverstatus->info->Players;
$maxplayers = $serverstatus->info->MaxPlayers;
$title = $serverstatus->info->HostName;
$img = "html/img/logo/valheim.webp";
$connectlink = "steam://connect/$ip:$gport";

// If the server is offline, the variable will be empty. That' how I check if the server is online.
$status = $serverstatus->info->Protocol;
if (isset($status)) {
    $status = 1;
} else {
    $status = 0;
}
?>