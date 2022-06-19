<?php
use AunePVP\jsonconversion;
use Spirit55555\Minecraft\MinecraftColors;
if (isset($controlpanel)) {
    $controlpanellink = "../../";
} else {
    $controlpanellink = "";
}
//#// GLOBAL //#//
// Get the game logo
$img = "html/img/logo/$type.webp";
// Get last players for banner
$lastplayerlines = tailCustom($controlpanellink."query/cron/$ServerID.json", 7);
$playerlineexit = array();
$count = 0;
foreach(preg_split("/((\r?\n)|(\r\n?))/", $lastplayerlines) as $playerline){
    $playerlinedecode = json_decode($playerline);
    $lastplayers[$count] = $playerlinedecode->players;
    $count = $count + 1;
    $uptime = $playerlinedecode->uptime;
}
$uptimebanner = str_replace(".", ",", round($uptime, 1))."%";

// Switch to find out game type
switch ($type) {
    case "csgo":
    case "valheim":
    case "vrising":
    case "protocol-valve":
    case "rust":
    case "arkse":
        // Get the operating system and convert it to full name
        if (function_exists("convertos")) {
            $Os = $serverstatus->info->Os ?? '';
            $Os = convertos($Os);
        }
        // Get Players. Note Ark will use another method to get the players
        $countplayers = $serverstatus->info->Players ?? '0';
        // Get Maxplayers
        $maxplayers = $serverstatus->info->MaxPlayers ?? '0';
        // Get server version
        $version = $serverstatus->info->Version;
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
    case "csgo":
        $password = $serverstatus->rules->ServerPassword_b ?? '';
        if (isset($serverstatus->rules)) {
            $count = 0;
            foreach ($serverstatus->rules as $rulename => $rule) {
                $count++;
                $rulename = ucwords(str_replace("_"," ", $rulename));
                $csgorule[$count][1] = $rulename;
                $csgorule[$count][2] = $rule;
                if (empty($csgorule[$count][2])) {$csgorule[$count][2] = "0";}
            }
        }
        break;
    case "vrising":
        // Check if server has a password
        $password = $serverstatus->info->Password ?? '';
        if ($password == "True") {$password = $language[$lang][14];} else {$password = $language[$lang][15];}
        // Get the server description
        $description = $serverstatus->rules->desc0 ?? '';
        // Check if bloodhound is on or off and set the matching value (On|Off)
        $bloodbound = $serverstatus->rules->{'blood-bound-enabled'} ?? false;
        if ($bloodbound == "True") {$bloodbound = $language[$lang][14];} else {$bloodbound = $language[$lang][15];}
        // Check if Catle heart damage is enabled
        $castleheartdamagemode = $serverstatus->rules->{'castle-heart-damage-mode'} ?? '';
        // Get the ingame day
        $ingameday = $serverstatus->rules->{'days-running'} ?? '';
        // Get the game tags
        $GameTags = $serverstatus->info->GameTags;
        $GameTags =  str_replace(",", ", ", $GameTags);
        break;
    case "rust":
        // Get the seed and world size
        $seed = $serverstatus->rules->{'world.seed'};
        $worldsize = $serverstatus->rules->{'world.size'};
        // Check if the server has a password
        $password = $serverstatus->info->Password ?? '';
        if ($password == "True") {$password = $language[$lang][14];} else {$password = $language[$lang][15];}
        // Get the uptime of the server
        $rustuptime = $serverstatus->rules->uptime;
        // Get the url of the server
        $rustwebsite = $serverstatus->rules->url;
        // Check if the Rustserver has PVP or PVE enabled
        if ($serverstatus->rules->pve == "False") {
            $pvp = $language[$lang][14];
        } else {
            $pvp = $language[$lang][15];
        }
        // Get the fps of the server
        $rustfps = $serverstatus->rules->fps;
        // Get the description and store it in an array
        $keys = (array) $serverstatus->rules;
        $matched = preg_grep('/description_\d+/', array_keys($keys));
        $value = 0;
        foreach ($matched as $descriptionkey) {
            if(!strlen($serverstatus->rules->{$descriptionkey}))
                continue;
            $value++;
            $rustdescription = $serverstatus->rules->{$descriptionkey};
            $rustdesc[$value] = $rustdescription;
        }
        // Get the game tags
        $GameTags = $serverstatus->info->GameTags;
        $GameTags =  preg_replace("/,/", ", ", $GameTags);
}