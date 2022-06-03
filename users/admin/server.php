<?php
require_once '../../html/config.php';
session_start();
if (!isset($_SESSION['username'])) {
    $_SESSION['msg'] = "You have to log in first";
    header('location: login.php');
}
if ($_SESSION['username'] != "admin") {
    header("location: ../login.php");
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="style.css">
    <script src="script.js"></script>
    <title>Admin panel</title>
</head>
<body>
<nav>
    <div id="sidebar">
        <button onclick="window.location.href='index.php';">Overview</button>
        <button class="selected" onclick="window.location.href='server.php';">Server</button>
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
<div class="padding30">
    <div id="myNav" class="overlay" style="width: 0;">
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">×</a>
        <div class="overlay-content">
            <button class="selected" onclick="window.location.href='index.php';">Overview</button>
            <button onclick="window.location.href='server.php';">Server</button>
            <button onclick="window.location.href='user.php';">User</button>
            <button onclick="window.location.href='settings.php';">Settings</button>
            <button onclick="window.location.href='../../index.php';">Home</button>
            <button class="bottom"
                    onclick="window.open('https://github.com/AunePVP/Game-Server-Query-and-Control-Center');">Github
            </button>
        </div>
    </div>
    <?php
    if (!isset($_GET['id']) || empty($_GET['id'])):
    ?>
    <div class="server inlineflex flex-wrap">
        <?php
        // Display Get Server from DB and Display Server
        $conn = mysqli_connect($DB_SERVER, $DB_USERNAME, $DB_PASSWORD, $DB_NAME);
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }
        $sql = "SELECT ID, IP, type FROM serverconfig WHERE owner='admin' AND enabled='1'";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0) {
            $serverhelper = '{"serverowner":[';
            while ($row = mysqli_fetch_assoc($result)) {
                $battlemetricsid = $row["BatPort"];
                $type = $row["type"];
                $ip = $row["IP"];
                $id = $row["ID"];
                $battlemetrics = json_decode(file_get_contents("https://api.battlemetrics.com/servers/" . $battlemetricsid));
                $name = $battlemetrics->data->attributes->name;
                $status = $battlemetrics->data->attributes->status;
                $players = $battlemetrics->data->attributes->players;
                $maxplayers = $battlemetrics->data->attributes->maxPlayers;
                switch ($type) {
                    case "arkse":
                        $img = "https://cdn.muehlhaeusler.online/img/tracker/game-logos/ark.png";
                        break;
                    case "minecraft":
                        $img = "https://api.mcsrvstat.us/icon/".$ip;
                        break;
                    case "valheim":
                        $img = "https://cdn.muehlhaeusler.online/img/tracker/game-logos/valheim.png";
                        break;
                    case "csgo":
                        $img = "https://cdn.muehlhaeusler.online/img/tracker/game-logos/csgo.png";
                        break;
                    default:
                        $img = "";
                }
                echo "<div class='serversnippet flex' ".'onclick="'."location.href='server.php?id=$id';".'"'."><div class='status $status'></div><div class='content'><div class='name'>$name</div><div class='logo flex'><img src='$img' width='85px' height='85px'></div><div class='player'>$players/$maxplayers</div></div></div>";
            }
        } else {
            echo "0 results";
        }
        mysqli_close($conn);
        ?>
        <div class="serversnippet flex" onclick="if (!window.__cfRLUnblockHandlers) return false; location.href='server.php?id=addserver';"><div class="content"><div class="name" style="font-size: 32px;padding-top: 25px;">Add a server</div><div style="font-size: 120px;text-align: center;font-weight: 800;font-family: Andale Mono;">+</div></div></div>
    </div>
    <?php
    endif;
    if (isset($_GET['id']) || !empty($_GET['id'])):
        $ServerID = (int) $_GET['id'];
        $conn = mysqli_connect($DB_SERVER, $DB_USERNAME, $DB_PASSWORD, $DB_NAME);
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }
        $sql = "SELECT * FROM serverconfig WHERE ID='$ServerID'";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                $ip = $row["IP"];
                $type = $row["type"];
                $qport = $row["QueryPort"];
                $gport = $row["GamePort"];
                $rport = $row["RconPort"];
                $banner = $row["BatPort"];
            }
        } else {
            echo "0 results";
        }
        $conn->close();
    ?>
        <?php
        $logFile = "/var/log/nginx/error.log";
        $interval = 1000;
        $color = "";

        if(!$color) {$textColor = "white";};
        if($interval < 100)  {$interval = 100;};
            ?>

            <style>
                @import url(https://fonts.googleapis.com/css?family=Ubuntu);
                body{
                    background-color: black;
                    color: <?php echo $textColor; ?>;
                    font-family: 'Ubuntu', sans-serif;
                    font-size: 16px;
                    line-height: 20px;
                }
                h4{
                    font-size: 18px;
                    line-height: 22px;
                    color: #353535;
                }
                #log {
                    position: relative;
                    top: -34px;
                }
                #scrollLock{
                    width:2px;
                    height: 2px;
                    overflow:visible;
                }
            </style>
            <script>
                setInterval(readLogFile, <?php echo $interval; ?>);
                window.onload = readLogFile;
                var pathname = window.location.pathname;
                var scrollLock = true;

                $(document).ready(function(){
                    $('.disableScrollLock').click(function(){
                        $("html,body").clearQueue()
                        $(".disableScrollLock").hide();
                        $(".enableScrollLock").show();
                        scrollLock = false;
                    });
                    $('.enableScrollLock').click(function(){
                        $("html,body").clearQueue()
                        $(".enableScrollLock").hide();
                        $(".disableScrollLock").show();
                        scrollLock = true;
                    });
                });
                function readLogFile(){
                    $.get(pathname, { getLog : "true" }, function(data) {
                        data = data.replace(new RegExp("\n", "g"), "<br />");
                        $("#log").html(data);

                        if(scrollLock == true) { $('html,body').animate({scrollTop: $("#scrollLock").offset().top}, <?php echo $interval; ?>) };
                    });
                }
            </script>
            <body>
            <h4><?php echo $logFile; ?></h4>
            <div id="log">

            </div>
            <div id="scrollLock"> <input class="disableScrollLock" type="button" value="Disable Scroll Lock" /> <input class="enableScrollLock" style="display: none;" type="button" value="Enable Scroll Lock" /></div>



        <div id="Hostname"><?php echo $titlename ?></div>
    <div class="ctrlsett">
        <a class="control">Control</a>
        <a class="settings">Settings</a>
    </div>
    <div style="height:80vh;background-color: #383B4F;">
        <div></div>
        <div></div>
    </div>
</div>
    <?php
    endif;
    ?>