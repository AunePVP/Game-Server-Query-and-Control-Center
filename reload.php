<?php
$Timer = MicroTime(true);
// Get all files
require_once 'html/config.php';
require_once 'functions.php';
require 'query/SourceQuery/bootstrap.php';
require 'query/minecraft/src/MinecraftPing.php';
require 'query/minecraft/src/MinecraftPingException.php';
require 'query/minecraft/src/MinecraftQuery.php';
require 'query/minecraft/src/MinecraftQueryException.php';
require 'html/type/minecraft/jsonconversion.php';
require 'html/type/minecraft/minecraftcolor.php';
require 'html/tailcustom.php';
use xPaw\SourceQuery\SourceQuery;
const SQ_TIMEOUT = 1;
const SQ_ENGINE = SourceQuery::SOURCE;
$ServerID = (int)($_GET['id'] ?? 0);
// start session
gsq_session_start();
$username = current_user();
session_write_close();
// Only servers in the account of the current user (or the public account) may be queried
if (!$ServerID || !in_array($ServerID, user_server_ids($username), true)) {
    http_response_code(401);
    exit;
}
// Get server information from database
$row = db_row("SELECT IP, type, QueryPort, GamePort, Name FROM serverconfig WHERE ID = ?", [$ServerID]);
if (!$row) {
    http_response_code(404);
    exit;
}
$ip = $row['IP'];
$type = $row['type'];
$qport = $row['QueryPort'];
$gport = $row['GamePort'];
$name = $row['Name'];
$queryresult = null;
switch ($type) {
    case "csgo":
    case "valheim":
    case "protocol-valve":
    case "arkse":
    case "vrising":
    case "dayz":
    case "rust":
        include 'query/sourcequery.php';
        break;
    case "minecraft":
        include 'query/minecraftquery.php';
        break;
}
$serverstatus = json_decode($queryresult ?? 'null');
include 'html/type/query.php';
// Clean up query result.
$response['Username'] = $username;
$response['ID'] = $ServerID;
$response['Status'] = $status;
$response['Name'] = $title;
$response['IP'] = $ip;
$response['QueryPort'] = $qport;
$response['GamePort'] = $gport;
$response['Players'] = $countplayers;
$response['MaxPlayers'] = $maxplayers;
$response['raw']['Type'] = $type;
$response['raw']['Logo'] = $img;
$response['raw']['Version'] = $version;
$response['raw']['ConnectLink'] = $connectlink;
switch ($type) {
    case "csgo":
    case "valheim":
    case "protocol-valve":
    case "arkse":
    case "vrising":
    case "rust":
    case "dayz":
        //if ($password == "true") {$password = "True";} elseif ($password == "false") {$password = "False";}
        $response['raw']['OS'] = $Os;
        $response['raw']['Password'] = $password;
        $response['raw']['Map'] = $map;
        break;
    case "minecraft":
        $response['raw']['MOTD'] = $motd;
        $response['raw']['Players'] = $playerlist ?? 0;
        break;
}
switch ($type) {
    case "arkse":
        $modlistfile = __DIR__ . '/html/type/arkse/modlist.json';
        $modlist = json_cache_read($modlistfile);
        $response['raw']['Players'] = $playerlist ?? 0;
        $response['raw']['InGameDay'] = $ingameday;
        $response['raw']['ClusterID'] = $clusterid;
        $response['raw']['Battleye'] = $battleye;
        $response['raw']['PVP'] = $pvp;
        $response['raw']['HasMods'] = $hasmods;
        if ($hasmods) {
            $count = 0;
            foreach ($mods as $mod) {
                // Mod IDs come from the game server: only accept numeric IDs
                if (!ctype_digit((string)$mod)) {
                    continue;
                }
                $response['raw']['Mods'][$count]['ModID'] = $mod;
                $response['raw']['Mods'][$count]['ModLink'] = 'https://steamcommunity.com/sharedfiles/filedetails/?id='.$mod;
                $convertedmod = $modlist[$mod] ?? '';
                if ($convertedmod === '') {
                    $response['inmodlist'] = "not in list";
                    // If mod is not in modlist, get the mod from the steamcommunity website and store it in the (JSON) modlist
                    $convertedmod = substr(get_title("https://steamcommunity.com/sharedfiles/filedetails/?id=$mod"), 16);
                    if ($convertedmod !== '') {
                        json_cache_set($modlistfile, (string)$mod, $convertedmod);
                    }
                }
                $response['raw']['Mods'][$count]['ModName'] = $convertedmod;
                $count++;
            }
        }
        $response['raw']['MapLink'] = $maplink;
        break;
    case "csgo":
        $response['raw']['Players'] = $playerlist ?? 0;
        $response['raw']['MapLink'] = $maplink;
        if (isset($serverstatus->rules)) {
            $response['raw']['HasRules'] = 1;
            $response['raw']['Rules'] = $csgorule;
        }
        break;
    case "vrising":
        $response['raw']['Players'] = $playerlist ?? 0;
        $response['raw']['Description'] = $description;
        $response['raw']['Bloodbound'] = $bloodbound;
        $response['raw']['CastleHeartDamageMode'] = $castleheartdamagemode;
        $response['raw']['InGameDay'] = $ingameday;
        $response['raw']['Tags'] = $GameTags;
        $response['raw']['MapLink'] = $maplink;
        break;
    case "dayz":
        $response['raw']['Tags'] = $GameTags;
        $response['raw']['MapLink'] = $maplink;
        break;
    case "rust":
        $mapurl = "https://rustmaps.com/map/".rawurlencode((string)$worldsize)."_".rawurlencode((string)$seed)."?embed=img_i_l";
        $metas = @get_meta_tags($mapurl) ?: [];
        if (isset($metas['twitter:image'])) {
            $metas = ($metas['twitter:image']);
            $maplink = str_replace('FullLabeledMap.png', 'Thumbnail.png', $metas);
        } else {
            $maplink = "html/img/map/rustgenerate.webp";
        }
        $response['raw']['Players'] = $playerlist ?? 0;
        $response['raw']['Seed'] = $seed;
        $response['raw']['WorldSize'] = $worldsize;
        $response['raw']['RustUptime'] = $rustuptime;
        $response['raw']['MapLink'] = $maplink;
        $response['raw']['Website'] = $rustwebsite;
        $response['raw']['PVP'] = $pvp;
        $response['raw']['FPS'] = $rustfps;
        // Line breaks ("\n") are converted on the client after escaping
        $response['raw']['Description'] = $rustdesc ?? [];
        $response['raw']['Tags'] = $GameTags;
        break;
}
$response['QueryTime'] = Number_Format(MicroTime(true) - $Timer, 4, '.', '');
header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
