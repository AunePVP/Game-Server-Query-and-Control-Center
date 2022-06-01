<?php
use AunePVP\jsonconversion;
use Spirit55555\Minecraft\MinecraftColors;
require 'html/lloffile.php';
require 'html/type/jsonconversion.php';
require 'html/type/minecraftcolor.php';

// function to convert shortend os name to full name
function convertos($Os)
{
    $Opers = array(
        'l' => 'linux',
        'w' => 'windows',
        'm' => 'mac'
    );
    return $Opers[$Os];
}

//#// GLOBAL //#//

// Get uptime and round uptime for banner
$uptime = file_get_contents("query/cron/uptime/$ServerID");
$uptimebanner = str_replace(".", ",", round($uptime, 1))."%";

// Get the game logo
$img = "html/img/logo/$type.webp";
// Get last players for banner
$lastplayerlines = tailCustom("query/cron/$ServerID.json", 7);
$playerlineexit = array();
$count = 0;
foreach(preg_split("/((\r?\n)|(\r\n?))/", $lastplayerlines) as $playerline){
    $playerlinedecode = json_decode($playerline);
    $lastplayers[$count] = $playerlinedecode->players;
    $count = $count + 1;
}

// Switch to find out game type
switch ($type) {
    case "csgo":
    case "valheim":
    case "protocol-valve":
    case "arkse":
        // Get the operating system and convert it to full name
        $Os = $serverstatus->info->Os ?? '';
        $Os = convertos($Os);
        // Get Players. Note Ark will use another method to get the players
        $countplayers = $serverstatus->info->Players;
        // Get Maxplayers
        $maxplayers = $serverstatus->info->MaxPlayers ?? '0';
        // Get the server name
        $title = $serverstatus->info->HostName;
        // If the server is offline, it won't respond. Therefore, in this case, we must use the cached title from the database
        if (empty($title)) {
        $title = $name;
        }
        // Create the connect link
        $connectlink = "steam://connect/$ip:$gport";
        // Get the map.
        $map = $serverstatus->info->Map ?? '';
        // If the server is offline, the variable will be empty. That' how I check if the server is online.
        $status = $serverstatus->info->Protocol ?? false;
        if (!empty($status)) {
            $status = 1;
        } else {
            $status = 0;
        }
        break;
}



//#// GAME SPECIFIC //#//

switch ($type) {
    case "arkse":
        // Get the real player number
        $countplayers = 0;
        if (isset($serverstatus->players)) {
            foreach ($serverstatus->players as $player) {
                if(!strlen($player->Name))
                    continue;
                $countplayers = $countplayers + 1;
            }
        }
        // Get the current game-day
        $ingameday = $serverstatus->rules->DayTime_s ?? '';
        // Get the cluster id
        $clusterid = $serverstatus->rules->ClusterId_s ?? 'Not cluster';
        // Check if server has a password
        $password = $serverstatus->rules->ServerPassword_b ?? '';
        // Check if battleye is enabled
        $battleye = $serverstatus->rules->SERVERUSESBATTLEYE_b ?? '';
        // Check if pve is enabled
        $pve = $serverstatus->rules->SESSIONISPVE_i ?? '';
        // CHeck if server has mods
        $hasmods = $serverstatus->rules->HASACTIVEMODS_i ?? '';
        // If the server has mods get the mod id's and store them in an array
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
        break;
    case "minecraft":
        // Create connect link
        $connectlink = $ip . ":" . $gport;
        // Check which protocol has been used
        if ($qport == 0) {
            // Get players
            $countplayers = $serverstatus->players->online ?? '0';
            // Get Max Players
            $maxplayers = $serverstatus->players->max ?? '0';
            // Get the game logo
            $img = $serverstatus->favicon ?? $img;
            // Get the server version
            $version = $serverstatus->version->name ?? '0';
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
            # Get MOTD / Server Name
            $titleraw = $serverstatus->info->HostName ?? '';
            $titlestr = (str_replace("?", "&", $titleraw));
            $title = MinecraftColors::clean($titlestr);
            $motd = MinecraftColors::convertToHTML($titlestr);
            // Get server Version
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
        break;
}