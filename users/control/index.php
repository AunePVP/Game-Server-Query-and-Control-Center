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
use xPaw\SourceQuery\SourceQuery;
const SQ_TIMEOUT = 1;
const SQ_ENGINE = SourceQuery::SOURCE;
session_start();
if (!isset($_SESSION['username'])) {
    $_SESSION['msg'] = "You have to log in first";
    header('location: ../login.php');
    exit;
}
include "../../html/tailcustom.php";
$username = $_SESSION['username'];
if ($username == "admin") {
    $title = "Admin panel";
} else {
    $title = "Control panel";
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
    <title><?php echo $title ?></title>
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
        <?php include 'overlaynav.php'?>
        <div class="server inlineflex flex-wrap">
            <?php
            require_once 'smallserver.php';
            if ($username == "admin") :
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
endif;
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
