<?php
use AunePVP\jsonconversion;
use Spirit55555\Minecraft\MinecraftColors;
require_once 'jsonconversion.php';
require_once 'minecraftcolor.php';
$json = "";
if ($qport == 0) {
// This is where I'm querying all the data I need and storing it in variables.
    $uptime = file_get_contents("query/cron/uptime/$ServerID")."%";
    $countplayers = $serverstatus->players->online;
    $maxplayers = $serverstatus->players->max;
    if (isset($serverstatus->favicon)) {
        $img = $serverstatus->favicon;
    } else {
        $img = "html/img/logo/minecraft.webp";
    }
    $connectlink = $ip . ":" . $gport;
    $version = $serverstatus->version->name;

// If the server is offline, the variable will be empty. That' how I check if the server is online.
    $status = $serverstatus->players->max;
    if (isset($status)) {
        $status = 1;
    } else {
        $status = 0;
    }

# Get MOTD / Server Name
    if (is_string($serverstatus->description)) {
        $titlename = MinecraftColors::clean($serverstatus->description);
        $motd = MinecraftColors::convertToHTML($serverstatus->description);
    } elseif (is_array($serverstatus->description->extra)) {
        foreach ($serverstatus->description->extra as $extra) {
            $titlename .= $extra->text;
            $color = $extra->color;
            $motd .= (new jsonconversion)->convertcolor($color);
            $motd .= $extra->text;
        }
        $motd = MinecraftColors::convertToHTML($motd);
    } elseif (isset($serverstatus->description->text)) {
        $titlename = $serverstatus->description->text;
        $motd = "<span style='color: #FFFFFF'>".$titlename."</span>";
    }
    $title = $titlename;
} else {
    $uptime = file_get_contents("query/cron/uptime/$ServerID")."%";
    $titleraw = $serverstatus->info->HostName;
    $titlestr = (str_replace("?", "&", $titleraw));
    $title = MinecraftColors::clean($titlestr);
    $motd = MinecraftColors::convertToHTML($titlestr);
    $version = $serverstatus->info->Version;
    $countplayers = $serverstatus->info->Players;
    $maxplayers = $serverstatus->info->MaxPlayers;
    $img = "https://api.mcsrvstat.us/icon/" . $ip;
    $titlename = $title;
    $status = $serverstatus->info->MaxPlayers;
    if (isset($status)) {
        $status = 1;
    } else {
        $status = 0;
    }

}
?>
