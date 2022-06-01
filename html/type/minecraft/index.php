<?php
use AunePVP\jsonconversion;
use Spirit55555\Minecraft\MinecraftColors;
// function to get last lines of file
require 'html/lloffile.php';
require_once 'jsonconversion.php';
require_once 'minecraftcolor.php';
$uptime = file_get_contents("query/cron/uptime/$ServerID");
$uptimebanner = str_replace(".", ",", round($uptime, 1))."%";
$uptime .= "%";
// Get last players for banner
$lastplayerlines = tailCustom("query/cron/$ServerID.json", 25);
//$lastplayers = preg_split("/\r\n|\n|\r/", $lastplayerlines);
$playerlineexit = array();
$count = 0;
foreach(preg_split("/((\r?\n)|(\r\n?))/", $lastplayerlines) as $playerline){
    $playerlinedecode = json_decode($playerline);
    $lastplayers[$count] = $playerlinedecode->players;
    $count = $count + 1;
}
$seperatevar = "[";
foreach ($lastplayers as $lastplayer) {
    if ($seperatevar != "[") {
        $seperatevar .= ", ";
    }
    $seperatevar .= $lastplayer;
}
$seperatevar .= "]";
$json = "";
$connectlink = $ip . ":" . $gport;
if ($qport == 0) {
// This is where I'm querying all the data I need and storing it in variables.
    $countplayers = $serverstatus->players->online ?? '0';
    $maxplayers = $serverstatus->players->max ?? '0';
    $img = $serverstatus->favicon ?? "html/img/logo/minecraft.webp";
    $version = $serverstatus->version->name ?? "";

// If the server is offline, the variable will be empty. That' how I check if the server is online.
    $status = $serverstatus->players->max ?? false;
    if (!empty($status)) {
        $status = 1;
    } else {
        $status = 0;
    }

# Get MOTD / Server Name
    if (is_string($serverstatus->description)) {
        $title = MinecraftColors::clean($serverstatus->description);
        $motd = MinecraftColors::convertToHTML($serverstatus->description);
    } elseif (is_array($serverstatus->description->extra ?? false)) {
        foreach ($serverstatus->description->extra as $extra) {
            $title = $title ?? '';
            $title .= $extra->text ?? '';
            $color = $extra->color;
            $motd = $motd ?? '';
            $motd .= (new jsonconversion)->convertcolor($color);
            $motd .= $extra->text;
        }
        $motd = MinecraftColors::convertToHTML($motd);
    } elseif (isset($serverstatus->description->text)) {
        $title = $serverstatus->description->text;
        $motd = "<span style='color: #FFFFFF'>".$title."</span>";
    }
} else {
    $titleraw = $serverstatus->info->HostName ?? '';
    $titlestr = (str_replace("?", "&", $titleraw));
    $title = MinecraftColors::clean($titlestr);
    $motd = MinecraftColors::convertToHTML($titlestr);
    $version = $serverstatus->info->Version ?? '0';
    $countplayers = $serverstatus->info->Players ?? '0';
    $maxplayers = $serverstatus->info->MaxPlayers ?? '0';
    $img = "https://api.mcsrvstat.us/icon/" . $ip;
    // If the server is offline, the variable will be empty. That' how I check if the server is online.
    $status = $serverstatus->info->MaxPlayers ?? false;
    if (!empty($status)) {
        $status = 1;
    } else {
        $status = 0;
    }
}
// Check if title was set if not use cached title from db
if (empty($title)) {
    $title = $name;
}
