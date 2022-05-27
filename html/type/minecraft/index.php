<?php
use \Spirit55555\Minecraft\MinecraftColors;
if ($qport == 0) {
// This is where I'm querying all the data I need and storing it in variables.
    $countplayers = $serverstatus->players->online;
    $maxplayers = $serverstatus->players->max;
    if (isset($serverstatus->favicon)) {
        $img = $serverstatus->favicon;
    } else {
        $img = "https://cdn.muehlhaeusler.online/img/tracker/game-logos/minecraft.webp";
    }
    $connectlink = $ip . ":" . $gport;
    $version = $serverstatus->version;

// If the server is offline, the variable will be empty. That' how I check if the server is online.
    $status = $serverstatus->players->max;
    if (isset($status)) {
        $status = 1;
    } else {
        $status = 0;
    }

# Get MOTD / Server Name
    $titlename = "";
    foreach ($serverstatus->description->extra as $extra) {
        $titlename .= $extra->text;
    }
    if (empty($titlename)) {
        $titlename = $serverstatus->description;
    }
    if (is_object($titlename)) {
        $titlename = $serverstatus->description->text;
    }
    $title = $titlename;
} else {
    require_once 'minecraftcolor.php';
    $titleraw = $serverstatus->info->HostName;
    $titlestr = (str_replace("?","&", $titleraw));
    $title = MinecraftColors::clean($titlestr);
    $motd = MinecraftColors::convertToHTML($titlestr, true, true, 'mc-motd--');
    echo $motd;
    $titlename = $title;

}
?>
