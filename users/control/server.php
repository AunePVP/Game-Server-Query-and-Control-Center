<?php
require_once '../../html/config.php';
require '../../query/SourceQuery/bootstrap.php';
require '../../query/minecraft/src/MinecraftPing.php';
require '../../query/minecraft/src/MinecraftPingException.php';
require '../../query/minecraft/src/MinecraftQuery.php';
require '../../query/minecraft/src/MinecraftQueryException.php';
require '../../html/type/minecraft/jsonconversion.php';
require '../../html/type/minecraft/minecraftcolor.php';
$controlpanel = true;
use xPaw\MinecraftPing;
use xPaw\MinecraftPingException;
use xPaw\MinecraftQuery;
use xPaw\MinecraftQueryException;
use xPaw\SourceQuery\SourceQuery;

const SQ_TIMEOUT = 1;
const SQ_ENGINE = SourceQuery::SOURCE;
session_start();
if (!isset($_SESSION['username'])) {
    $_SESSION['msg'] = "You have to log in first";
    header('location: ../login.php');
    exit;
}
function convertos($Os)
{
    $Opers = array(
        'l' => 'Linux',
        'w' => 'Windows',
        'm' => 'Mac'
    );
    return $Opers[$Os];
}
include "../../html/tailcustom.php";
$username = $_SESSION['username'];
if ($username == "admin") {
    $title = "Admin panel";
} else {
    $title = "Control panel";
}
$Server_selected = 'class="selected"';
//--// Add server //--//
function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
// Form Add Server
if (array_key_exists('AddServer', $_POST)) {
    $addtype = test_input($_POST["type"]);
    $addip = test_input($_POST["ip"]);
    $addgport = test_input($_POST["gport"]);
    $addqport = test_input($_POST["qport"]) ?? "0";
    $addrport = test_input($_POST["rport"]) ?? "0";
    switch ($addtype) {
        case "csgo":
        case "valheim":
        case "vrising":
        case "protocol-valve":
        case "rust":
        case "arkse":
            $Query = new SourceQuery();
            try {
                $Query->Connect($addip, $addqport, SQ_TIMEOUT, SQ_ENGINE);
                $queryresult["info"] = $Query->GetInfo();
            } // If error occured, send back to add server
            catch (Exception $e) {
                echo "<script>alert('ERROR! Server doesn\'t respond to query. Please try adding the server again.');window.location.href = window.location.href + '?id=addserver';</script>";
                exit();
            } // Disconnect from Server
            finally {
                $Query->Disconnect();
            }
            break;
        # Minecraft query
        case "minecraft":
            // If queryprotocol is not an option
            if ($addqport == 0) {
                try {
                    $Query = new MinecraftPing($addip, $addgport);
                    $queryresult = $Query->Query();
                } // If error occured, send back to add server
                catch (MinecraftPingException $e) {
                    echo "<script>alert('ERROR! Server doesn\'t respond to query. Please try adding the server again.');window.location.href = window.location.href + '?id=addserver';</script>";
                    exit();
                } // Disconnect from Server
                finally {
                    $Query->Close();
                }
            } // If queryprotocol is an option
            else {
                $Query = new MinecraftQuery();
                try {
                    $Query->Connect($addip, $addqport);
                    $queryresult["info"] = ($Query->GetInfo());
                } // Check if an error occurred
                catch (MinecraftQueryException $e) {
                    echo "<script>alert('ERROR! Server doesn\'t respond to query. Please try adding the server again.');window.location.href = window.location.href + '?id=addserver';</script>";
                    exit();
                }
            }
            break;
    }
    // Connect to db and upload server data
    $conn = mysqli_connect($DB_SERVER, $DB_USERNAME, $DB_PASSWORD, $DB_NAME);
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    // Set Server ID
    $sql = "SELECT ID FROM serverconfig WHERE IP='$addip' AND type='$addtype' AND QueryPort='$addqport' AND GamePort='$addgport' AND RconPort='$addrport'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $addid = $row['ID'];
            echo "<script>alert('You alredy have access to the server!');window.location.reload();</script>";
            exit;
        }
    } else {
        $sql = "SELECT ID FROM serverconfig ORDER BY ID DESC LIMIT 1";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $addid = $row["ID"] + 1;
            }
        }
    }
    // Add server ID to user
    if (empty($addid)) {
        $addid = 1;
    }
    $sql = "SELECT server FROM users WHERE username='$username'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $serverjson = json_decode($row["server"], TRUE);
            $serverjson[$addid] = array();
            $serverjson[$addid] = $addid;
            $serverjson = json_encode($serverjson);
        }
    }
    if (!empty($serverjson)) {
        $sql = "UPDATE users SET server='$serverjson' WHERE username='$username'";
        if (mysqli_query($conn, $sql)) {
            echo "<script>console.log('Record updated successfully')</script>";
        } else {
            echo "Error updating record: " . mysqli_error($conn);
        }
    }
    // Add server data to serverconfig table
    $sql = "INSERT INTO serverconfig (ID, IP, type, QueryPort, GamePort, RconPort, enabled, Name) VALUES ('$addid', '$addip', '$addtype', '$addqport', '$addgport', '$addrport', '1', '0')";
    if (mysqli_query($conn, $sql)) {
        echo "<script>console.log('Record updated successfully')</script>";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
    $conn->close();
}
// Control Server
if (array_key_exists('control', $_POST)) {
    $command = $_POST['control'];
    $ServerID = (int)$_GET['id'];
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta name="theme-color" content="#151f34" media="(prefers-color-scheme: light)">
    <meta name="theme-color" content="#151f34" media="(prefers-color-scheme: dark)">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="style.css">
    <script src="script.js"></script>
    <title>Admin panel</title>
</head>
<body>
<nav>
    <div id="sidebar">
        <button onclick="window.location.href='index.php';">Overview</button>
        <button onclick="window.location.href='server.php';" class="selected">Server</button>
        <button onclick="window.location.href='user.php';">User</button>
        <button onclick="window.location.href='settings.php';">Settings</button>
        <button onclick="window.location.href='../../index.php';">Home</button>
        <button class="bottom"
                onclick="window.open('https://github.com/AunePVP/Game-Server-Query-and-Control-Center');">Github
        </button>
    </div>
    <div id="topbar">
        <span id="open-span" onclick="openNav()">☰</span>
    </div>
</nav>
<main>
    <div class="padding30" <?php
    if (!empty($_GET['id'])) {
        if ($_GET['id'] == "addserver") {
            echo "style='padding:0!important'";
        }
    }?>>
        <?php include 'overlaynav.php' ?>

        <!-- __%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%__ -->
        <!-- _------------_ Display Servers_------------_ -->
        <!-- __%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%__ -->

        <?php
        if (empty($_GET['id'])):
            ?>
            <div class="server inlineflex flex-wrap">
                <?php
                require_once 'smallserver.php' ?>
                <div class="serversnippet flex" onclick="location.href='server.php?id=addserver';">
                    <div class="content">
                        <div class="name" style="font-size: 32px;padding-top: 25px;">Add a server</div>
                        <div style="height: 111px;justify-content: center;display: flex;">
                            <svg class="svg-circleplus" viewBox="0 0 100 100">
                                <line x1="22.5" y1="50" x2="77.5" y2="50" stroke-width="7"></line>
                                <line x1="50" y1="22.5" x2="50" y2="77.5" stroke-width="7"></line>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        <?php
        endif; ?>

        <!-- __%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%__ -->
        <!-- _------------_Specific Server_------------_ -->
        <!-- __%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%__ -->

        <?php
        if (!empty($_GET['id'])):
        if ($_GET['id'] != "addserver") {
            $ServerID = (int)$_GET['id'];
            $conn = mysqli_connect($DB_SERVER, $DB_USERNAME, $DB_PASSWORD, $DB_NAME);
            if (!$conn) {
                die("Connection failed: " . mysqli_connect_error());
            }
            $sql = "SELECT * FROM serverconfig WHERE ID='$ServerID'";
            $result = mysqli_query($conn, $sql);
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $ServerID = $row["ID"];
                    $ip = $row["IP"];
                    $type = $row["type"];
                    $qport = $row["QueryPort"];
                    $gport = $row["GamePort"];
                    $rport = $row["RconPort"];
                    switch ($type) {
                        case "csgo":
                        case "valheim":
                        case "protocol-valve":
                        case "arkse":
                        case "vrising":
                        case "rust":
                            include '../../query/sourcequery.php';
                            break;
                        case "minecraft":
                            include '../../query/minecraftquery.php';
                            break;
                    }
                    $serverstatus = json_decode($queryresult ?? false);
                    include '../../html/type/query.php';
                    // Correct image link if necessary
                    $imgcheck = $rest = substr($img, 0, 5);
                    if ($imgcheck == "html/") {
                        $img = "../../$img";
                    }
                }
            } else {
                echo "0 results";
            }
            $conn->close();
        }
        ?>
        <!-- _------------‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾------------_ -->
        <!-- _------------_Server Control Panel_-----------_ -->
        <!-- _------------_____________________------------_ -->
        <?php
        if ($_GET['id'] != "addserver"): ?>
        <div id="Hostname"><?php
            if (isset($title)) {
                echo $title;
            } ?></div>
        <div class="ctrlsett">
            <a class="control" onclick="tab(this.id)" id="tab1">Control</a>
            <a class="settings" onclick="tab(this.id)" id="tab2">Settings</a>
        </div>
        <div id="controlsettingsparent">
            <div id="control">
                <div class='left'>left</div>
                <div class='right'>
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?'.http_build_query($_GET); ?>">
                        <div id="servercontrol">
                            <?php
                            if ($status){
                                $startdisabled = " disabled";
                            }
                            if (!$status){
                                $stopdisabled = " disabled";
                                $restartdisabled = " disabled";
                            }
                            ?>
                            <button class="button<?php echo $startdisabled ?? ''?>" type="submit" value="start" name="control"<?php echo $startdisabled ?? ''?>>Start</button>
                            <button class="button<?php echo $stopdisabled ?? ''?>" type="submit" value="stop" name="control"<?php echo $stopdisabled ?? ''?>>Stop</button>
                            <button class="button<?php echo $restartdisabled ?? ''?>" type="submit" value="restart" name="control<?php echo $restartdisabled ?? ''?>">Restart</button>
                            <button class="button<?php echo $backupdisabled ?? ''?>" type="submit" value="backup" name="control"<?php echo $backupdisabled ?? ''?>>Backup</button>
                        </div>
                </div>
            </div>
            <div id="settings">
                <!-- Display server information -->
                <div id="serverinf">
                    <table>
                        <tr>
                            <th class="tableid">ID</th>
                            <th>Type</th>
                            <th>IP</th>
                            <th>Game Port</th>
                            <th>Query Port</th>
                            <th>Rcon Port</th>
                        </tr>
                        <tr>
                            <td class="tableid"><?php echo $ServerID?></td>
                            <td><?php echo $type?></td>
                            <td><?php echo $ip?></td>
                            <td><?php echo $gport?></td>
                            <td><?php echo $qport?></td>
                            <td><?php echo $rport?></td>
                        </tr>
                    </table>
                    <div class="settings-inputline">
                        <label for="control-path">Server control path</label><input id="control-path" class="settingsinput" type="text"><a target="_blank" href="https://github.com/AunePVP/Game-Server-Query-and-Control-Center/wiki/Configuration#server-control-path" title="What does that mean? Check out the wiki!" style="height: 0;"><img src="../../html/img/questionmark.svg" height="19px" alt="" style="margin: 4px 2px;cursor: pointer;"></a>
                    </div>
                    <div><?php
                        switch ($type) {
                        case "csgo":
                        case "valheim":
                        case "vrising":
                        case "protocol-valve":
                        case "rust":
                        case "arkse":
                            echo '<span style="font-weight: 500;">System:</span>'.$Os.'<br>';
                            break;
                        case "minecraft":
                            break;
                        }?>
                        <a href="#0" class="cd-popup-trigger">Delete this server</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%//
    //-------------_Add Server_-------------//
    //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%//
    elseif ($_GET['id'] == "addserver"):?>
        <div class="centeraddserverdiv">
            <div class="addserverdiv">
                <div class="padding25">
                    <h2 style="margin: 0 0 10px;font-family: Helvetica,sans-serif;">Add a server</h2>
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                    <div class="cAx">Game:</div>
                    <label>
                        <select required="required" name="type" onchange="selecttype()" id="type">
                            <option disabled selected value style="display:none">select a game</option>
                            <option value="arkse">ARK Survival Evolved</option>
                            <option value="csgo">Counter-Strike: Global Offensive</option>
                            <option value="minecraft">Minecraft</option>
                            <option value="valheim">Valheim</option>
                            <option value="vrising">Vrising</option>
                            <option value="rust">Rust</option>
                        </select>
                    </label>
                        <div class="input">
                            <label for="input-domain-ip">IP/Domain:</label><input id="input-domain-ip" name="ip" type="text" required="required" minlength="4" maxlength="30" placeholder="xxx.xxx.xxx.xx" autocomplete="off">
                        </div>
                        <div class="input">
                            <label for="input-gport">Game Port:</label><input id="input-gport" name="gport" type="text" minlength="1" required="required" maxlength="5" placeholder="xxxx" autocomplete="off" pattern="^[0-9]*$">
                        </div>
                        <div class="input">
                            <label for="input-qport">Query Port:</label><input id="input-qport" name="qport" type="text" minlength="1" required="required" maxlength="5" placeholder="xxxx" autocomplete="off" pattern="^[0-9]*$">
                        </div>
                        <div class="input">
                            <label for="input-rport">Rcon Port:</label><input id="input-rport" name="rport" type="text" required="required" minlength="1" maxlength="5" placeholder="xxxx" autocomplete="off" pattern="^[0-9]*$">
                        </div>
                        <div>
                            <div id="notes"></div>
                        </div>
                        <div style="display:flex;justify-content: flex-end;">
                            <input class="addsrv" type="submit" name="AddServer" value="AddServer">
                        </div>
                </div>
            </div>
        </div>
    <?php
    endif;
    endif;
    ?>
