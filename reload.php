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
$ServerID = (int)$_GET['id'];
// start session
session_start();
$username = $_SESSION['username'] ?? 'public';
session_write_close();
// Get the allowed servers
$conn = mysqli_connect($DB_SERVER, $DB_USERNAME, $DB_PASSWORD, $DB_NAME);
$sql = "SELECT server FROM users WHERE username='$username'";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        $id = $row["server"];
    }
}
$id = json_decode($id);
if (!in_array($ServerID, $id)) {
    http_response_code(401);
    var_dump(http_response_code());
    exit;
}
// Get server information from database
$sql = "SELECT IP, type, Queryport, GamePort, Name FROM serverconfig WHERE ID='$ServerID'";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        $ip = $row['IP'];
        $type = $row['type'];
        $qport = $row['Queryport'];
        $gport = $row['GamePort'];
        $name = $row['Name'];
    }
}
mysqli_close($conn);
switch ($type) {
    case "csgo":
    case "valheim":
    case "protocol-valve":
    case "arkse":
    case "vrising":
    case "rust":
        include 'query/sourcequery.php';
        break;
    case "minecraft":
        include 'query/minecraftquery.php';
        break;
}
$serverstatus = json_decode($queryresult ?? false);
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
        //if ($password == "true") {$password = "True";} elseif ($password == "false") {$password = "False";}
        $response['raw']['OS'] = $Os;
        $response['raw']['Password'] = $password;
        $response['raw']['Map'] = $map;
        break;
    case "minecraft":
        $response['raw']['MOTD'] = $motd;
        $response['raw']['Players'] = $playerlist;
        break;
}
switch ($type) {
    case "arkse":
        include ('html/type/arkse/modlist.php');
        $response['raw']['Players'] = $playerlist;
        $response['raw']['InGameDay'] = $ingameday;
        $response['raw']['ClusterID'] = $clusterid;
        $response['raw']['Battleye'] = $battleye;
        $response['raw']['PVP'] = $pvp;
        $response['raw']['HasMods'] = $hasmods;
        if ($hasmods) {
            foreach ($mods as $mod) {
                if (!isset($count)){$count=0;}
                $response['raw']['Mods'][$count]['ModID'] = $mod;
                $response['raw']['Mods'][$count]['ModLink'] = 'https://steamcommunity.com/sharedfiles/filedetails/?id='.$mod;
                $convertedmod = $modlist['ArkModName'][$mod];
                if (!empty($convertedmod)) {
                    $response['raw']['Mods'][$count]['ModName'] = $convertedmod;
                } else {
                    // If mod is not in modlist, get the mod from the steamcommunity website and store the mod in the modlist file
                    $convertedmod = substr(get_title("https://steamcommunity.com/sharedfiles/filedetails/?id=$mod"), 16);
                    $response['raw']['Mods'][$count]['ModName'] = $convertedmod;
                    $convertedmod = str_replace("'", "\'", $convertedmod);
                    $convertedmod = '$modlist[\'ArkModName\']'."[$mod] = '$convertedmod';";
                    file_put_contents("html/type/arkse/modlist.php", $convertedmod . "\n", FILE_APPEND);
                }
                $count++;
            }
        }
        $response['raw']['MapLink'] = $maplink;
        break;
    case "csgo":
        $response['raw']['Players'] = $playerlist;
        $response['raw']['MapLink'] = $maplink;
        if (isset($serverstatus->rules)) {
            $response['raw']['HasRules'] = 1;
            $response['raw']['Rules'] = $csgorule;
        }
        break;
    case "vrising":
        $response['raw']['Players'] = $playerlist;
        $response['raw']['Description'] = $description;
        $response['raw']['Bloodbound'] = $bloodbound;
        $response['raw']['CastleHeartDamageMode'] = $bloodbound;
        $response['raw']['InGameDay'] = $ingameday;
        $response['raw']['Tags'] = $GameTags;
        $response['raw']['MapLink'] = $maplink;
        break;
    case "rust":
        $mapurl = "https://rustmaps.com/map/".$worldsize."_".$seed."?embed=img_i_l";
        $metas = get_meta_tags($mapurl);
        if (isset($metas['twitter:image'])) {
            $metas = ($metas['twitter:image']);
            $maplink = str_replace('FullLabeledMap.png', 'Thumbnail.png', $metas);
        } else {
            $maplink = "html/img/map/rustgenerate.webp";
        }
        $response['raw']['Players'] = $playerlist;
        $response['raw']['Seed'] = $seed;
        $response['raw']['WorldSize'] = $worldsize;
        $response['raw']['RustUptime'] = $rustuptime;
        $response['raw']['MapLink'] = $maplink;
        $response['raw']['Website'] = $rustwebsite;
        $response['raw']['PVP'] = $pvp;
        $response['raw']['FPS'] = $rustfps;
        $rustdesc = str_replace("\\n", "<br>",$rustdesc);
        $response['raw']['Description'] = $rustdesc;
        $response['raw']['Tags'] = $GameTags;
        break;
}
$response['QueryTime'] = Number_Format(MicroTime(true) - $Timer, 4, '.', '');
echo json_encode($response);
