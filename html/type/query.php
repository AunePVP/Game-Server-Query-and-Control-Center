<?php
/*
 * Turn the raw query result ($serverstatus) into display variables.
 * Expects: $type, $ip, $gport, $qport, $name, $serverstatus, $language, $lang.
 * This file is included once per server (also inside loops), so every
 * variable it sets is reset first.
 */
use AunePVP\jsonconversion;
use Spirit55555\Minecraft\MinecraftColors;

unset($title, $motd, $Os, $version, $map, $mapname, $maplink, $connectlink, $password, $playerlist,
    $ingameday, $clusterid, $battleye, $pvp, $hasmods, $mods, $csgorule, $description, $bloodbound,
    $castleheartdamagemode, $GameTags, $seed, $worldsize, $rustuptime, $rustwebsite, $rustfps, $rustdesc);
$yes = $language[$lang][14];
$no = $language[$lang][15];
$info = $serverstatus->info ?? null;
$rules = $serverstatus->rules ?? null;

//#// GLOBAL //#//
// Get the game logo
$img = "html/img/logo/$type.webp";
$countplayers = 0;
$maxplayers = 0;
$status = 0;
if (is_source_type($type)) {
    // Get the operating system and convert it to full name
    $Os = convertos($info->Os ?? '');
    $countplayers = $info->Players ?? 0;
    $maxplayers = $info->MaxPlayers ?? 0;
    $version = $info->Version ?? '';
    // If the server is offline, it won't respond. Therefore, in this case, we use the cached title from the database
    $title = ($info->HostName ?? '') ?: $name;
    $connectlink = "steam://connect/$ip:$gport";
    $map = $info->Map ?? '';
    // If the server is offline, the protocol is empty. That's how I check if the server is online.
    $status = empty($info->Protocol) ? 0 : 1;
    // Password: A2S_INFO reports it for every Source game, some games also set a rule
    $password = (gsq_truthy($info->Password ?? false) || gsq_truthy($rules->ServerPassword_b ?? false)) ? $yes : $no;
    $playerlist = source_playerlist($serverstatus->players ?? []);
}

//#// GAME SPECIFIC //#//
switch ($type) {
    case "arkse":
        // ARK reports wrong player numbers in A2S_INFO, so count the named players
        $countplayers = count($playerlist);
        $ingameday = $rules->DayTime_s ?? '';
        $clusterid = $rules->ClusterId_s ?? 'Not cluster';
        $battleye = gsq_truthy($rules->SERVERUSESBATTLEYE_b ?? false) ? $yes : $no;
        $pvp = gsq_truthy($rules->SESSIONISPVE_i ?? false) ? $no : $yes;
        // If the server has mods get the mod id's and store them in an array
        $hasmods = $rules->HASACTIVEMODS_i ?? '';
        $mods = [];
        if ($hasmods) {
            foreach (preg_grep('/MOD\d+_s/', array_keys((array)$rules)) as $modkey) {
                $mods[] = strstr($rules->{$modkey}, ':', true);
            }
        }
        $officialmaps = array("Aberration", "CrystalIsles", "Gen2", "Gen", "LostIsland", "Ragnarok", "ScorchedEarth", "TheCenter", "TheIsland", "Valguero", "Fjordur", "Valhalla", "TheVolcano");
        $maplink = in_array($map, $officialmaps, true) ? "html/img/map/$map.webp" : "html/img/map/modmap.webp";
        break;
    case "minecraft":
        $connectlink = $ip . ":" . $gport;
        $img = "https://api.mcsrvstat.us/icon/" . rawurlencode($ip);
        if ($qport == 0) {
            // Server list ping
            $countplayers = $serverstatus->players->online ?? 0;
            $maxplayers = $serverstatus->players->max ?? 0;
            $version = $serverstatus->version->name ?? '0';
            $status = empty($serverstatus->players->max) ? 0 : 1;
            # Get MOTD / Server Name
            $descriptionraw = $serverstatus->description ?? '';
            if (is_string($descriptionraw)) {
                $title = html_entity_decode(MinecraftColors::clean($descriptionraw), ENT_QUOTES, 'UTF-8');
                $motd = MinecraftColors::convertToHTML($descriptionraw);
            } elseif (is_array($descriptionraw->extra ?? false)) {
                $title = '';
                $motd = '';
                foreach ($descriptionraw->extra as $extra) {
                    $title .= $extra->text ?? '';
                    $motd .= (new jsonconversion)->convertcolor($extra->color ?? '');
                    $motd .= $extra->text ?? '';
                }
                $motd = MinecraftColors::convertToHTML($motd);
            } elseif (isset($descriptionraw->text)) {
                $title = $descriptionraw->text;
                $motd = "<span style='color: #FFFFFF'>" . h($title) . "</span>";
            }
        } else {
            // Query protocol
            $titlestr = str_replace("?", "&", $info->HostName ?? '');
            $title = html_entity_decode(MinecraftColors::clean($titlestr), ENT_QUOTES, 'UTF-8');
            $motd = MinecraftColors::convertToHTML($titlestr);
            $version = $info->Version ?? '0';
            $countplayers = $info->Players ?? 0;
            $maxplayers = $info->MaxPlayers ?? 0;
            $status = empty($info->MaxPlayers) ? 0 : 1;
            // Get list of players (the UUID cache only exists on the public page)
            $playerlist = [];
            if (function_exists("minecraftcache") && !empty($serverstatus->players)) {
                foreach ($serverstatus->players as $player) {
                    $playerlist[] = ['Name' => $player, 'Skin' => "https://crafatar.com/avatars/" . minecraftcache($player)];
                }
            }
        }
        // If the server is offline, use the cached title from the database
        if (empty($title)) {
            $title = $name;
        }
        if (empty($motd)) {
            $motd = h($title);
        }
        break;
    case "csgo":
        if ($rules) {
            $csgorule = [];
            $count = 0;
            foreach ($rules as $rulename => $rule) {
                $count++;
                $csgorule[$count][1] = ucwords(str_replace("_", " ", $rulename));
                $csgorule[$count][2] = empty($rule) ? "0" : $rule;
            }
        }
        $officialmaps = array("ar_baggage", "ar_dizzy", "ar_lunacy", "ar_monastery", "ar_shoots", "cs_agency", "cs_assault", "cs_climb", "cs_italy", "cs_militia", "cs_office", "de_ancient", "de_bank", "de_cache", "de_canals", "de_cbble", "de_crete", "de_dust2", "de_hive", "de_inferno", "de_iris", "de_lake", "de_mirage", "de_nuke", "de_overpass", "de_safehouse", "de_shortdust", "de_shortnuke", "de_stmarc", "de_sugarcane", "de_train", "de_vertigo", "dz_blacksite", "dz_ember", "dz_sirocco", "dz_vineyard", "ze_Bathroom_v2_5");
        if (in_array($map, $officialmaps, true)) {
            $maplink = "html/img/map/$map.webp";
            $mapname = convertcsgomapname($map) ?? $map;
        } else {
            $shortmap = preg_match('/^[a-zA-Z]{2}_/', $map) ? substr($map, 3) : $map;
            $mapname = ucwords(str_replace("_", " ", $shortmap));
            $maplink = "html/img/map/csgo_modmap.webp";
        }
        break;
    case "vrising":
        $description = $rules->desc0 ?? '';
        $bloodbound = gsq_truthy($rules->{'blood-bound-enabled'} ?? false) ? $yes : $no;
        $castleheartdamagemode = $rules->{'castle-heart-damage-mode'} ?? '';
        $ingameday = $rules->{'days-running'} ?? '';
        $GameTags = str_replace(",", ", ", $info->GameTags ?? '');
        $maplink = "html/img/map/" . rawurlencode($map) . ".webp";
        break;
    case "dayz":
        $GameTags = str_replace(",", ", ", $info->GameTags ?? '');
        $maplink = in_array($map, ["chernarusplus", "chernarus"], true) ? "html/img/map/chernarusplus.webp" : "html/img/map/modmap.webp";
        break;
    case "rust":
        $seed = $rules->{'world.seed'} ?? '';
        $worldsize = $rules->{'world.size'} ?? '';
        $rustuptime = $rules->uptime ?? '';
        $rustwebsite = $rules->url ?? '';
        $pvp = (($rules->pve ?? '') == "False") ? $yes : $no;
        $rustfps = $rules->fps ?? '';
        // Get the description (split into description_00, description_01, ...)
        $rustdesc = [];
        foreach (preg_grep('/description_\d+/', array_keys((array)$rules)) as $descriptionkey) {
            if (strlen($rules->{$descriptionkey})) {
                $rustdesc[] = $rules->{$descriptionkey};
            }
        }
        $GameTags = str_replace(",", ", ", $info->GameTags ?? '');
        break;
}
