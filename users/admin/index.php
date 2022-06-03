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
        <button class="selected" onclick="window.location.href='index.php';">Overview</button>
        <button onclick="window.location.href='server.php';">Server</button>
        <button onclick="window.location.href='user.php';">User</button>
        <button onclick="window.location.href='settings.php';">Settings</button>
        <button onclick="window.location.href='../../index.php';">Home</button>
        <button class="bottom" onclick="window.open('https://github.com/AunePVP/Game-Server-Query-and-Control-Center');">Github</button>
    </div>
    <div id="topbar">
        <span id="open-span" onclick="openNav()">☰</span>
    </div>
</nav>
<main>
    <div class="padding15">
        <div id="myNav" class="overlay" style="width: 0;">
            <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">×</a>
            <div class="overlay-content">
                <button class="selected" onclick="window.location.href='index.php';">Overview</button>
                <button onclick="window.location.href='server.php';">Server</button>
                <button onclick="window.location.href='user.php';">User</button>
                <button onclick="window.location.href='settings.php';">Settings</button>
                <button onclick="window.location.href='../../index.php';">Home</button>
                <button class="bottom" onclick="window.open('https://github.com/AunePVP/Game-Server-Query-and-Control-Center');">Github</button>
            </div>
        </div>
        <div class="server inlineflex flex-wrap">
            <?php
            // Display Get Server from DB and Display Server
            $conn = mysqli_connect($DB_SERVER, $DB_USERNAME, $DB_PASSWORD, $DB_NAME);
            if (!$conn) {
                die("Connection failed: " . mysqli_connect_error());
            }
            $sql = "SELECT ID, IP, type FROM serverconfig";
            $result = mysqli_query($conn, $sql);
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $battlemetricsid = $row["BatPort"];
                    $type = $row["type"];
                    $ip = $row["IP"];
                    $id = $row["ID"];

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
        </div>
        <div class="extra inlineflex flex-wrap">
            <div class="itemdiv">
                <div class="itemtitle">Users</div>
                <div class="item">
                    <?php
                    // Get all Users from Database
                    $conn = mysqli_connect($DB_SERVER, $DB_USERNAME, $DB_PASSWORD, $DB_NAME);
                    if (!$conn) {
                        die("Connection failed: " . mysqli_connect_error());
                    }
                    $sql = "SELECT username FROM users";
                    $result = mysqli_query($conn, $sql);
                    if (mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)) {
                            echo "<p>".$row["username"]."</p>";
                        }
                    } else {
                        echo "0 results";
                    }
                    mysqli_close($conn);
                    ?>
                </div>
            </div>
            <div class="itemdiv">
                <div class="itemtitle">Users</div>
                <div class="item"></div>
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
                    $github = json_decode(file_get_contents('https://api.github.com/repos/AunePVP/Game-Server-Query-and-Control-Center/releases/latest', false, $context));
                    $releasename = $github->name;
                    $markdown = $github->body;
                    #$markdown = preg_replace("/\r\n|\r|\n/", '\r\n', $markdown);
                    $versionfile = fopen("../../version.txt", "r") or die("Unable to open file!");
                    $version = fgets($versionfile);
                    fclose($versionfile);
                    if ($version == $releasename) {
                        echo "<div style='margin: auto;font-family: Helvetica Neue,sans-serif;font-size: 22px;}'>No new release found</div>";
                    } else {
                        echo "<div id='markdown'></div>";
                        $emarkdown = TRUE;
                    }
                    ?>
                </div>
            </div>
            <div class="itemdiv">
                <div class="itemtitle">Events</div>
                <div class="item events">
                    <?php
                    $file = file("events.txt");
                    $file = array_reverse($file);
                    foreach($file as $f){
                        echo "<p>".$f."</p>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</main>
<textarea class="plain"><?php echo $markdown ?></textarea>
<?php
if($emarkdown):
?>
    <script type="text/javascript" src="drawdown.js"></script>
    <script type="text/javascript">
        var ein = document.querySelector(".plain");
        var eout = document.querySelector("#markdown");
        function update() {
            eout.innerHTML = markdown(ein.value);
        }
        ein.addEventListener("input", update);
        update();
    </script>
<?php
endif;
?>
</body>
</html>
