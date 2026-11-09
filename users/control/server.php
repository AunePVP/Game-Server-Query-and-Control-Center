<?php
require_once '../../html/config.php';
require_once '../../html/lib.php';
require '../../query/SourceQuery/bootstrap.php';
require '../../query/minecraft/src/MinecraftPing.php';
require '../../query/minecraft/src/MinecraftPingException.php';
require '../../query/minecraft/src/MinecraftQuery.php';
require '../../query/minecraft/src/MinecraftQueryException.php';
require '../../html/type/minecraft/jsonconversion.php';
require '../../html/type/minecraft/minecraftcolor.php';
$controlpanel = true;
date_default_timezone_set('Europe/Berlin');
$timezone = date_default_timezone_get();
$time = date('H:i', time());
use xPaw\MinecraftPing;
use xPaw\MinecraftPingException;
use xPaw\MinecraftQuery;
use xPaw\MinecraftQueryException;
use xPaw\SourceQuery\SourceQuery;
const SQ_TIMEOUT = 1;
const SQ_ENGINE = SourceQuery::SOURCE;
gsq_session_start();
if (!isset($_SESSION['username'])) {
    $_SESSION['msg'] = "You have to log in first";
    redirect('../../?login=true');
}
$username = $_SESSION['username'];
$isadmin = is_admin($username);
$pageid = (string)($_GET['id'] ?? '');
$addserverpage = ($pageid === 'addserver');
$ServerID = ($pageid !== '' && !$addserverpage) ? (int)$pageid : 0;
include "../../html/tailcustom.php";
if ($isadmin) {
    $title = "Admin panel";
} else {
    $title = "Control panel";
}
$Server_selected = 'class="selected"';
$emarkdown = false;
$markdown = '';

///  Redirect, if server is not in account  ///
if ($ServerID && !$isadmin && !in_array($ServerID, user_server_ids($username), true)) {
    redirect('server.php');
}

///  Who is allowed to control this server?  ///
$allowcontrol = false;
$controlserverjson = [];
if ($ServerID) {
    $row = db_row("SELECT controlserver FROM serverconfig WHERE ID = ?", [$ServerID]);
    if ($row) {
        $controlserverjson = json_decode($row["controlserver"], true);
        if (!is_array($controlserverjson)) {
            $controlserverjson = [];
        }
        $allowcontrol = in_array($username, $controlserverjson, true);
    }
}

///  Server control configuration (html/server/<id>.php)  ///
$controlconfig = '../../html/server/' . $ServerID . '.php';
$hascontrolconfig = $ServerID && file_exists($controlconfig);
if ($hascontrolconfig) {
    include $controlconfig;
}
$logdisabled = !$hascontrolconfig;

///  Raw log output for the live console  ///
if (!empty($_GET['raw'])) {
    session_write_close();
    $logoutput = '';
    if (!$logdisabled && $allowcontrol) {
        if ($logtype == "path") {
            $logoutput = tailCustom($logpath, 150);
        } elseif ($logtype == "scp") {
            $connection = ssh2_connect($sip, $sport, array('hostkey' => 'ssh-rsa'));
            ssh2_auth_pubkey_file($connection, $susername, $keypathpub, $keypath);
            $stream = ssh2_exec($connection, $scpcommand);
            stream_set_blocking($stream, true);
            $logoutput = stream_get_contents(ssh2_fetch_stream($stream, SSH2_STREAM_STDIO));
        } elseif ($logtype == "rsync") {
            exec($rsynccommand);
            $logoutput = tailCustom($rsynclogpath, 150);
        }
    } else {
        $logoutput = "There was an error retrieving the logs from the server!";
    }
    // The log is inserted as HTML on the client, so escape it here
    echo h(str_replace('#015', '', (string)$logoutput));
    exit;
}

// Every state-changing request needs a valid CSRF token
csrf_verify();

// Store users, who can control the server (admin only)
if (array_key_exists('updatepermission', $_POST) && $ServerID) {
    require_admin();
    $requested = array_filter(array_map('trim', explode(",", (string)($_POST["responsepermitted"] ?? ''))));
    $existing = array_column(db_query("SELECT username FROM users"), 'username');
    $permittedusers = array_values(array_intersect($requested, $existing));
    db_query("UPDATE serverconfig SET controlserver = ? WHERE ID = ?", [json_encode($permittedusers), $ServerID]);
    $controlserverjson = $permittedusers;
    $allowcontrol = in_array($username, $controlserverjson, true);
}
// Delete server (admin only)
if (array_key_exists('delete', $_POST) && $ServerID) {
    require_admin();
    $cronfile = "../../query/cron/" . $ServerID . ".json";
    if (file_exists($cronfile)) {
        unlink($cronfile);
    }
    db_query("DELETE FROM serverconfig WHERE ID = ?", [$ServerID]);
    foreach (db_query("SELECT id, server FROM users") as $user) {
        $ids = json_decode($user["server"], true) ?: [];
        store_user_server_ids((int)$user["id"], array_diff(array_map('intval', $ids), [$ServerID]));
    }
    redirect('server.php');
}
// Remove server from own account
if (array_key_exists('removeserverfromaccount', $_POST) && $ServerID) {
    $user = db_row("SELECT id, server FROM users WHERE username = ?", [$username]);
    if ($user) {
        $ids = json_decode($user["server"], true) ?: [];
        store_user_server_ids((int)$user["id"], array_diff(array_map('intval', $ids), [$ServerID]));
    }
    redirect('server.php');
}
//-----------------\\
// Form Add Server \\
//-----------------\\
function addserver_error(string $message): void
{
    echo "<script>alert(" . json_encode($message) . ");window.location.href = 'server.php?id=addserver';</script>";
    exit();
}
function valid_port($port, bool $allowZero): bool
{
    return ctype_digit($port) && (int)$port <= 65535 && ($allowZero || (int)$port > 0);
}
if (array_key_exists('AddServer', $_POST)) {
    $addtype = test_input($_POST["type"] ?? '');
    $addip = test_input($_POST["ip"] ?? '');
    $addgport = test_input($_POST["gport"] ?? '');
    $addqport = test_input($_POST["qport"] ?? '') ?: "0";
    $addrport = test_input($_POST["rport"] ?? '') ?: "0";
    if (!in_array($addtype, GSQ_SERVER_TYPES, true)) {
        addserver_error('ERROR! Unknown game type.');
    }
    if (!preg_match('/^[A-Za-z0-9.\-:\[\]]{1,60}$/', $addip)) {
        addserver_error('ERROR! Invalid IP address or domain.');
    }
    if (!valid_port($addgport, false) || !valid_port($addqport, true) || !valid_port($addrport, true)) {
        addserver_error('ERROR! Invalid port.');
    }
    $addgport = (int)$addgport;
    $addqport = (int)$addqport;
    $addrport = (int)$addrport;
    $noresponse = "ERROR! Server doesn't respond to query. Please try adding the server again.";
    switch ($addtype) {
        case "csgo":
        case "valheim":
        case "vrising":
        case "protocol-valve":
        case "rust":
        case "dayz":
        case "arkse":
            $Query = new SourceQuery();
            try {
                $Query->Connect($addip, $addqport, SQ_TIMEOUT, SQ_ENGINE);
                $queryresult["info"] = $Query->GetInfo();
            } // If error occured, send back to add server
            catch (Exception $e) {
                addserver_error($noresponse);
            } // Disconnect from Server
            finally {
                $Query->Disconnect();
            }
            break;
        case "minecraft":# Minecraft query
            if ($addqport == 0) { // If queryprotocol is not an option
                $Query = null;
                try {
                    $Query = new MinecraftPing($addip, $addgport);
                    $queryresult = $Query->Query();
                } // If error occured, send back to add server
                catch (MinecraftPingException $e) {
                    addserver_error($noresponse);
                } // Disconnect from Server
                finally {
                    $Query?->Close();
                }
            } else { // If queryprotocol is an option
                $Query = new MinecraftQuery();
                try {
                    $Query->Connect($addip, $addqport);
                    $queryresult["info"] = ($Query->GetInfo());
                } // Check if an error occurred
                catch (MinecraftQueryException $e) {
                    addserver_error($noresponse);
                }
            }
            break;
    }
    // Set Server ID: reuse an existing entry, otherwise take the next free ID
    $existingserver = db_row("SELECT ID FROM serverconfig WHERE IP = ? AND type = ? AND QueryPort = ? AND GamePort = ? AND RconPort = ?", [$addip, $addtype, $addqport, $addgport, $addrport]);
    if ($existingserver) {
        $addid = (int)$existingserver["ID"];
        $alreadyindb = true;
    } else {
        $last = db_row("SELECT ID FROM serverconfig ORDER BY ID DESC LIMIT 1");
        $addid = $last ? (int)$last["ID"] + 1 : 1;
        $alreadyindb = false;
    }
    // Add server ID to user
    $user = db_row("SELECT id, server FROM users WHERE username = ?", [$username]);
    if ($user) {
        $ids = array_map('intval', json_decode($user["server"], true) ?: []);
        if (in_array($addid, $ids, true)) {
            echo "<script>alert('You already have access to this server!');window.location.href = 'server.php';</script>";
            exit;
        }
        $ids[] = $addid;
        store_user_server_ids((int)$user["id"], $ids);
    }
    if (!$alreadyindb) {
        // Add server data to serverconfig table
        db_query("INSERT INTO serverconfig (ID, IP, type, QueryPort, GamePort, RconPort, Name, controlserver) VALUES (?, ?, ?, ?, ?, ?, '0', ?)", [$addid, $addip, $addtype, $addqport, $addgport, $addrport, json_encode([GSQ_ADMIN_USER])]);
    }
}
// Control Server: only whitelisted actions, never the raw POST value
$controlcommands = [
    'start' => $sstart ?? null,
    'stop' => $sstop ?? null,
    'restart' => $srestart ?? null,
    'backup' => $sbackup ?? null,
    'update' => $supdate ?? null,
];
if (array_key_exists('control', $_POST) && $hascontrolconfig && $allowcontrol) {
    $action = (string)$_POST['control'];
    $command = $controlcommands[$action] ?? null;
    if (!empty($command)) {
        switch ($action) {
            case "start":$startdisabled=" disabled";break;
            case "stop":$stopdisabled=" disabled";$restartdisabled=" disabled";break;
            case "restart":$startdisabled=" disabled";break;
        }
        $connection = ssh2_connect($sip, $sport, array('hostkey'=>'ssh-rsa'));
        ssh2_auth_pubkey_file($connection, $susername, $keypathpub, $keypath);
        $stream = ssh2_exec($connection, $command);
        stream_set_blocking($stream, true);
        $streamout = stream_get_contents(ssh2_fetch_stream($stream, SSH2_STREAM_STDIO));
        $re = '/|\[K|\[ \.\.\.\. ] |\[\d\dm  OK  \[\dm\] | \[\d\dmOK\[\dm|\[\d\dmOK|\[|\dm|\d INFO \]/';
        $streamout = preg_replace($re, '', $streamout);
        $streamout = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $streamout);
        file_put_contents('events.txt', $time . " Server " . $action . " " . $ServerID . "\n", FILE_APPEND | LOCK_EX);
    }
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
        <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js" integrity="sha384-vtXRMe3mGCbOeY7l30aIg8H9p3GdeSe4IFlP6G8JMa7o7lXvnz3GFKzPxzJdPfGK" crossorigin="anonymous"></script>
        <title><?php echo $title?></title>
    </head>
    <body>
    <div id="confpopupparent">
        <div onclick="exitdelete()" style="height: 100vh;width: 100%;position: fixed;z-index: 0;"></div>
        <div id="confpopup">
            <div class="areyousure">Are you sure you want to delete this Server?</div>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ."?id=$ServerID"; ?>">
                <?php echo csrf_field() ?>
                <div style="padding: 10px 10px 0">
                    <p style="padding-bottom: 15px;overflow: auto;">This action <strong>cannot</strong> be undone. This will permanently delete the server and all historical data from the database.</p>
                    <p>Please type <strong>Delete-Server-<?php echo $ServerID?></strong> to confirm.</p>
                    <input id="deleteinput" type="text" onkeyup="checkpattern()" required="required" autocomplete="off" pattern="[dD][eE][lL][eE][tT][eE]-[sS][eE][rR][vV][eE][rR]-<?php echo $ServerID?>">
                </div>
                <div class="yesno">
                    <button id="submitdelete" type="submit" name="delete" disabled>Delete</button>
                </div>
            </form>
        </div>
    </div>
    <div id="consolepopupparent">
        <div onclick="exitpopuplog()" style="height: 100vh;width: 100%;position: fixed;z-index: 0;"></div>
        <div id="consolepopup">
            <div id="popuplog"></div>
        </div>
    </div>
    <nav>
        <div id="sidebar">
            <button onclick="window.location.href='server.php';" class="selected">Overview</button>
            <!--<button onclick="window.location.href='user.php';">User</button>-->
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
                    <?php require_once 'smallserver.php' ?>
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
            <?php if ($isadmin):?>
                <div class="extra inlineflex flex-wrap">
                    <div class="itemdiv users">
                        <div class="itemtitle">Users</div>
                        <div class="item" style="width: 190px">
                            <?php
                            // Get all Users from Database
                            $userrows = db_query("SELECT username FROM users");
                            foreach ($userrows as $row) {
                                echo "<p>".h($row["username"])."</p>";
                            }
                            if (!$userrows) {
                                echo "0 results";
                            }
                            ?>
                        </div>
                    </div>
                    <div class="itemdiv">
                        <div class="itemtitle">Events</div>
                        <div class="item events">
                            <?php
                            $file = file("events.txt") ?: [];
                            $file = array_reverse($file);
                            foreach($file as $f){
                                echo "<p>".h($f)."</p>";
                            }
                            ?>
                        </div>
                    </div>
                    <div class="itemdiv width40">
                        <div class="itemtitle">Releases</div>
                        <div class="item" style="width: auto; display: flex;">
                            <?php
                            $context = stream_context_create(
                                array(
                                    "http" => array(
                                        "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36"
                                    )
                                )
                            );
                            $github = json_decode((string)@file_get_contents('https://api.github.com/repos/AunePVP/Game-Server-Query-and-Control-Center/releases/latest', false, $context));
                            $releasename = (string)($github->name ?? '');
                            $markdown = (string)($github->body ?? '');
                            $versionfile = fopen("../../version.txt", "r") or die("Unable to open file!");
                            $version = fgets($versionfile);
                            fclose($versionfile);
                            $version = preg_replace('/\s+/', '', $version);
                            $releasename = preg_replace('/\s+/', '', $releasename);
                            if ($releasename === '' || $version == $releasename) {
                                echo "<div style='margin: auto;font-family: Helvetica Neue,sans-serif;font-size: 22px;}'>No new release found</div>";
                            } else {
                                echo "<div id='markdown'></div>";
                                $emarkdown = TRUE;
                            }
                            ?>
                        </div>
                    </div>
                    <div class="itemdiv news">
                        <div class="itemtitle">News</div>
                        <div class="item news"></div>
                    </div>
                    <textarea id="markdownsource" style="display:none"><?php echo h($markdown) ?></textarea>
                    <?php if($emarkdown):?>
                        <script src="https://cdnjs.cloudflare.com/ajax/libs/showdown/2.1.0/showdown.min.js" integrity="sha512-LhccdVNGe2QMEfI3x4DVV3ckMRe36TfydKss6mJpdHjNFiV07dFpS2xzeZedptKZrwxfICJpez09iNioiSZ3hA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
                        <script type="text/javascript">
                            let converter = new showdown.Converter(),
                                text      = document.getElementById('markdownsource').value,
                                html      = converter.makeHtml(text);
                            document.getElementById('markdown').innerHTML = html;
                        </script>
                    <?php endif;?>
            <!-- __%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%__ -->
            <!-- _------------_Specific Server_------------_ -->
            <!-- __%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%__ -->
            <?php
            endif;
            endif;
            if (!empty($_GET['id'])):
            if ($_GET['id'] != "addserver") {
                $serverrows = db_query("SELECT * FROM serverconfig WHERE ID = ?", [$ServerID]);
                if ($serverrows) {
                    foreach ($serverrows as $row) {
                        $queryresult = null;
                        $ServerID = $row["ID"];
                        $ip = $row["IP"];
                        $type = $row["type"];
                        $qport = $row["QueryPort"];
                        $gport = $row["GamePort"];
                        $rport = $row["RconPort"];
                        $name = $row["Name"];
                        switch ($type) {
                            case "csgo":
                            case "valheim":
                            case "protocol-valve":
                            case "arkse":
                            case "vrising":
                            case "dayz":
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
            }
            ?>
            <!-- _------------‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾------------_ -->
            <!-- _------------_Server Control Panel_-----------_ -->
            <!-- _------------_____________________------------_ -->
            <?php
            if ($_GET['id'] != "addserver"): ?>
            <div id="Hostname">
                <?php if (!empty($motd)) {echo $motd; /* HTML generated and escaped by MinecraftColors */} else {echo h($title ?? '');}?>
            </div>
            <div class="ctrlsett">
                <a class="control" onclick="tab(this.id)" id="tab1">Control</a>
                <a class="settings" onclick="tab(this.id)" id="tab2">Settings</a>
            </div>
            <div id="controlsettingsparent">
                <div id="control">
                    <div class='left'>left</div>
                    <div class='right'>
                        <div style="position: relative;">
                            <?php if (!file_exists('../../html/server/'.$ServerID.'.php') || !$allowcontrol):?>
                                <div class="nocontrol">You can't control this server!</div>
                            <?php endif;?>
                            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ."?id=$ServerID&page=control"; ?>">
                                <?php echo csrf_field() ?>
                                <div id="servercontrol">
                                    <?php
                                    if ($status && !isset($startdisabled)){
                                        $startdisabled = " disabled";
                                    }
                                    if (!$status && !isset($startdisabled)){
                                        $stopdisabled = " disabled";
                                        $restartdisabled = " disabled";
                                    } elseif (isset($restartdisabled)) {
                                        $startdisabled = " disabled";
                                        $restartdisabled = "";
                                    }
                                    ?>
                                    <button class="button<?php echo $startdisabled ?? ''?>" type="submit" value="start" name="control"<?php echo $startdisabled ?? ''?>>Start</button>
                                    <button class="button<?php echo $stopdisabled ?? ''?>" type="submit" value="stop" name="control"<?php echo $stopdisabled ?? ''?>>Stop</button>
                                    <button class="button<?php echo $restartdisabled ?? ''?>" type="submit" value="restart" name="control"<?php echo $restartdisabled ?? ''?>>Restart</button>
                                    <button class="button<?php echo $backupdisabled ?? ''?>" type="submit" value="backup" name="control"<?php echo $backupdisabled ?? ''?>>Backup</button>
                                </div>
                            </form>
                            <div class="log"><?php echo h($streamout ?? "")?></div>
                            <div id="console">
                                <div onclick="consolepopup()" style="cursor: pointer;">
                                    <svg id="2rt" width="100%" height="100%" viewBox="0 0 20 20" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;"><path d="M1.7,1C1.313,1 1,1.313 1,1.7L1,7L3,7L3,3L7,3L7,1L1.7,1ZM3,13L1,13L1,18.3C1,18.687 1.313,19 1.7,19L7,19L7,17L3,17L3,13ZM17,17L13,17L13,19L18.3,19C18.687,19 19,18.687 19,18.3L19,13L17,13L17,17ZM19,1.7C19,1.313 18.687,1 18.3,1L13,1L13,3L17,3L17,7L19,7L19,1.7Z" style="fill:white;"/></svg>
                                </div>
                                <details id="togglelog">
                                    <summary style="font-family: 'JetBrains Mono'">Live console</summary>
                                    <div id="consolelog"></div>
                                </details>
                            </div>
                            <!-- Display Server Console -->
                            <script>
                                window.onload = readLogFile;
                                details = document.getElementById("togglelog");
                                $('#togglelog').on('toggle', function() {
                                    let attr = $(this).attr('open');
                                    if (typeof attr !== 'undefined' && attr !== false) {
                                        startlog();
                                    } else {
                                        stoplog();
                                    }
                                });
                                function consolepopup(){
                                    document.getElementById("consolepopupparent").style.display = "flex";
                                }
                                function exitpopuplog() {
                                    document.getElementById("consolepopupparent").style.display = "none";
                                }
                                function startlog() {
                                    myInterval = setInterval(readLogFile, 3000);
                                    document.getElementById('2rt').style.display = 'block';
                                }
                                function stoplog() {
                                    clearInterval(myInterval);
                                    document.getElementById('2rt').style.display = 'none';
                                }
                                let pathname = window.location.pathname + "?id=<?php echo $ServerID?>";
                                function readLogFile(){
                                    $.get(pathname, {raw:"true"},function(data) {
                                        $("#consolelog").html(data);
                                        $("#popuplog").html(data);
                                    });
                                    let consolelog = $('#consolelog');
                                    consolelog.animate({
                                        scrollTop: consolelog[0].scrollHeight
                                    }, 1000);
                                    let popuplog = $('#popuplog');
                                    popuplog.animate({
                                        scrollTop: popuplog[0].scrollHeight
                                    }, 1000);
                                }
                            </script>
                        </div>
                    </div>
                </div>
                <div id="settings">
                    <script>
                        function confirmdelete() {
                            document.getElementById("confpopupparent").style.display = "flex";
                        }
                        function exitdelete() {
                            document.getElementById("confpopupparent").style.display = "none";
                        }
                        function checkpattern() {
                            let checkinput = document.getElementById("deleteinput");
                            document.getElementById("submitdelete").disabled=!checkinput.checkValidity();
                        }
                    </script>
                    <!-- _----------‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾‾----------_ -->
                    <!-- _----------_Server Settings_----------_ -->
                    <!-- _----------_________________----------_ -->
                    <?php if($isadmin||$allowcontrol):?>
                        <div id="serverinf">
                            <table>
                                <tr>
                                    <th class="tablehide">ID</th>
                                    <th class="tablehide">Type</th>
                                    <th>IP</th>
                                    <th>Game Port</th>
                                    <th>Query Port</th>
                                    <th>Rcon Port</th>
                                </tr>
                                <tr>
                                    <td class="tablehide"><?php echo h($ServerID)?></td>
                                    <td class="tablehide"><?php echo h($type)?></td>
                                    <td><?php echo h($ip)?></td>
                                    <td><?php echo h($gport)?></td>
                                    <td><?php echo h($qport)?></td>
                                    <td><?php echo h($rport)?></td>
                                </tr>
                            </table>
                            <div>
                                <?php
                                switch ($type) {
                                    case "csgo":
                                    case "valheim":
                                    case "vrising":
                                    case "protocol-valve":
                                    case "rust":
                                    case "dayz":
                                    case "arkse":
                                        echo '<span style="font-weight: 500;">System:</span>'.h($Os ?? '').'<br>';
                                        break;
                                    case "minecraft":
                                        break;
                                }
                                // Select users who can control this server
                                $users = array_column(db_query("SELECT username FROM users"), 'username');
                                $notallowedyet = array_diff($users, $controlserverjson);
                                ?>
                                <script>
                                    function allowDrop(ev) {
                                        ev.preventDefault();
                                    }
                                    function drag(ev) {
                                        ev.dataTransfer.setData("text", ev.target.id);
                                    }
                                    function drop(ev, el) {
                                        ev.preventDefault();
                                        let data = ev.dataTransfer.getData("text");
                                        el.appendChild(document.getElementById(data));
                                    }
                                    function getusernames() {
                                        const parent = document.getElementById('dropform');
                                        const children = Array.from(parent.children);
                                        document.getElementById("responsepermitted").value = children.map(element => {
                                            return element.id;
                                        });
                                        document.getElementById("updatepermission").click();
                                    }
                                </script>
                                <div id="dragdropform">
                                    <?php if($isadmin):?>
                                    <div class="ddparent left">
                                        <p>Users</p>
                                        <div id="dragform" ondrop="drop(event, this)" ondragover="allowDrop(event)">
                                            <?php
                                            foreach ($notallowedyet as $maybeallowed){
                                                if ($maybeallowed=="public")
                                                    continue;
                                                echo '<div draggable="true" ondragstart="drag(event)" ondrop="return false" ondragover="return false" class="draggable" id="'.h($maybeallowed).'">'.h($maybeallowed).'</div>';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <?php endif;?>
                                    <div class="ddparent right">
                                        <p>Users with permission, to control this server.</p>
                                        <div id="dropform" ondrop="drop(event, this)" ondragover="allowDrop(event)">
                                            <?php
                                            foreach ($controlserverjson as $allowed){
                                                echo '<div draggable="true" ondragstart="drag(event)" ondrop="return false" ondragover="return false" class="draggable" id="'.h($allowed).'">'.h($allowed).'</div>';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <?php if($isadmin):?>
                                <button id="updatecontrolperm" onclick='getusernames()'>Update</button>
                                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ."?id=$ServerID&page=settings"; ?>" style="display:none">
                                    <?php echo csrf_field() ?>
                                    <input id="responsepermitted" name="responsepermitted">
                                    <button id="updatepermission" type="submit" name="updatepermission"></button>
                                </form>
                                <?php endif;?>
                                <?php if($isadmin):?>
                                <!-- Delete this server (admin only) -->
                                <button type='button' id="deletebtn" onclick="confirmdelete()">Delete this server</button>
                                <?php endif;?>
                            </div>
                        </div>
                    <?php else:?>
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ."?id=$ServerID"; ?>">
                            <?php echo csrf_field() ?>
                            <button type='submit' id="deletebtn" name="removeserverfromaccount">Remove server from account</button>
                        </form>
                    <?php endif;?>
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
                            <?php echo csrf_field() ?>
                            <div class="cAx">Game:</div>
                            <label>
                                <select required="required" name="type" onchange="selecttype()" id="type">
                                    <option disabled selected value style="display:none">select a game</option>
                                    <option value="arkse">ARK Survival Evolved</option>
                                    <option value="csgo">Counter-Strike: Global Offensive</option>
                                    <option value="dayz">DayZ</option>
                                    <option value="minecraft">Minecraft</option>
                                    <option value="rust">Rust</option>
                                    <option value="valheim">Valheim</option>
                                    <option value="vrising">Vrising</option>

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
                        </form>
                    </div>
                </div>
            </div>
        <?php endif;?>
        <?php endif;?>
    </main>
    </body>
</html>
