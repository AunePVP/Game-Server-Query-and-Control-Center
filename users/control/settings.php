<?php
$configfile = '../../html/config.php';
require_once $configfile;
session_start();
if (!isset($_SESSION['username'])) {
    $_SESSION['msg'] = "You have to log in first";
    header('location: ../login.php');
    exit;
}
$username = $_SESSION['username'];
if ($username == "admin") {
    $title = "Settings | Admin panel";
} else {
    $title = "Settings | Control panel";
}
$Settings_selected = 'class="selected"';
// Get the current version
$versionfile = fopen("../../version.txt", "r") or die("Unable to open file!");
$version = fgets($versionfile);
fclose($versionfile);
// Connect to database
$conn = mysqli_connect($DB_SERVER, $DB_USERNAME, $DB_PASSWORD, $DB_NAME);
$sql = "SELECT * FROM serverconfig";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    $servercount = 0;
    while ($row = mysqli_fetch_assoc($result)) {
        $servercount++;
    }
}
$sql = "SELECT * FROM users";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    $usercount = 0;
    while ($row = mysqli_fetch_assoc($result)) {
        $usercount++;
    }
}
function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
// Test Database Connection
$displaysubmit = "none";
$displaytestconn = "block";
if (array_key_exists('validate', $_POST)) {
    $DB_SERVERI = test_input($_POST["input-ip"]);
    $DB_USERNAMEI = test_input($_POST["input-username"]);
    $DB_PASSWORDI = test_input($_POST["input-password"]);
    $DB_NAMEI = test_input($_POST["input-dbname"]);
    $DB_SERVER = $DB_SERVERI;
    $DB_USERNAME = $DB_USERNAMEI;
    $DB_PASSWORD = $DB_PASSWORDI;
    $DB_NAME = $DB_NAMEI;
    $sqlvalidatemessage = "Connection successful!";
    $displaysubmit = "block";
    $displaytestconn = "none";
    try {
        $conn = mysqli_connect($DB_SERVERI, $DB_USERNAMEI, $DB_PASSWORDI, $DB_NAMEI);
        mysqli_close($conn);
    } catch(mysqli_sql_exception $e) {
        $sqlvalidatemessage = "Connection failed!";
        $displaysubmit = "none";
        $displaytestconn = "block";
    }
}
// Edit Database Configuration
if (array_key_exists('submit', $_POST)) {
    $DB_SERVER = test_input($_POST["input-ip"]);
    $DB_USERNAME = test_input($_POST["input-username"]);
    $DB_PASSWORD = test_input($_POST["input-password"]);
    $DB_NAME = test_input($_POST["input-dbname"]);
    $configcontent=file_get_contents($configfile);
    $configcontent = preg_replace('/\$DB_SERVER = \"(.*?)\";/', '$DB_SERVER = "'.$DB_SERVER.'";', $configcontent);
    $configcontent = preg_replace('/\$DB_USERNAME = \"(.*?)\";/', '$DB_USERNAME = "'.$DB_USERNAME.'";', $configcontent);
    $configcontent = preg_replace('/\$DB_PASSWORD = \"(.*?)\";/', '$DB_PASSWORD = "'.$DB_PASSWORD.'";', $configcontent);
    $configcontent = preg_replace('/\$DB_NAME = \"(.*?)\";/', '$DB_NAME = "'.$DB_NAME.'";', $configcontent);
    file_put_contents($configfile, $configcontent);
}
// Choose a theme
if (array_key_exists('submittheme', $_POST)) {
    $css_template = $_POST["themeinput"];
    $notification = "<p>Theme updated<p>";
    $themescript = "<script>$(document).ready( function () {"."$('#themesummary').click();});</script>";
    $configcontent=file_get_contents($configfile);
    $configcontent = preg_replace('/\$css_template = \"(.*?)\";/', '$css_template = "'.$css_template.'";', $configcontent);
    file_put_contents($configfile, $configcontent);
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
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6/dist/jquery.min.js" integrity="sha384-vtXRMe3mGCbOeY7l30aIg8H9p3GdeSe4IFlP6G8JMa7o7lXvnz3GFKzPxzJdPfGK" crossorigin="anonymous"></script>
    <title><?php echo $title?></title>
    <script>
    </script>
</head>
<body onload="startTime()">
<nav>
    <div id="sidebar">
        <button onclick="window.location.href='server.php';">Overview</button>
        <!--<button onclick="window.location.href='user.php';">User</button>-->
        <button onclick="window.location.href='settings.php';" class="selected">Settings</button>
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
    <?php include 'overlaynav.php' ?>
    <div id="einstellungen">
        <!-- Admin Section-->
        <?php if ($username == "admin"):?>
        <div id="dropdownsettings">
            <h1 id="settingsh1">Settings</h1>
            <div style="overflow-y:scroll;max-height:calc(100vh - 128px);padding-right: 5px;">
                <details>
                    <summary>Database</summary>
                    <div class="detailscontent">
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                            <div class="flex">
                                <div id="left">
                                    <div class="input">
                                        <label for="input-ip">IP:</label>
                                        <input id="input-ip" class="input" name="input-ip" type="text" minlength="3" maxlength="40" required="required" placeholder="xxx.xxx.xxx.xx" autocomplete="off" <?php if (isset($DB_SERVER)){echo "value=$DB_SERVER";}?>>
                                    </div>
                                    <div class="input">
                                        <label for="input-dbname">DB Name:</label>
                                        <input id="input-dbname" class="input" name="input-dbname" type="text" minlength="3" placeholder="xxxxxxx" autocomplete="off" <?php if (isset($DB_NAME)){echo "value=$DB_NAME";}?>>
                                    </div>
                                </div>
                                <div id="right">
                                    <div class="input">
                                        <label for="input-username">Username:</label>
                                        <input id="input-username" class="input" name="input-username" type="text" minlength="2" maxlength="15" placeholder="xxxxx" autocomplete="off" <?php if (isset($DB_USERNAME)){echo "value=$DB_USERNAME";}?>>
                                    </div>
                                    <div class="input">
                                        <label for="input-password">Password:</label>
                                        <input id="input-password" class="input" name="input-password" type="password" minlength="3" placeholder="xxxxxxxxxxxx" autocomplete="off" <?php if (isset($DB_PASSWORD)){echo "value=$DB_PASSWORD";}?>>
                                    </div>
                                </div>
                            </div>
                            <div class="flex" style="justify-content: flex-end;margin-right: 3px;line-height: 34px;">
                                <?php if (isset($sqlvalidatemessage)) {echo $sqlvalidatemessage;}?>
                                <input class="addsrv" type="submit" name="validate" value="Test Connection" style="display: <?php echo $displaytestconn ?>">
                                <input class="addsrv" type="submit" name="submit" value="Submit" style="display: <?php echo $displaysubmit ?>">
                            </div>
                        </form>
                    </div>
                </details>
                <details>
                    <summary id="themesummary" onclick="currentTheme()">Design</summary>
                    <div class="detailscontent">
                        <div class="flex">
                            <div class="tabs-left">
                                <button class="tablinks active" onclick="changetheme('btndark')" id="btndark">Dark</button>
                                <button class="tablinks" onclick="changetheme('btnlight')" id="btnlight">Light</button>
                                <button class="tablinks" onclick="changetheme('btnsnight')" id="btnsnight">Summer Night</button>
                                <button class="tablinks" onclick="changetheme('btnsnightinv')" id="btnsnightinv">Summer Night inv.</button>
                                <button class="tablinks" onclick="changetheme('btnmidnight')" id="btnmidnight">Midight</button>
                            </div>
                            <div class="tab-content">
                                <div class="tab-pane" id="themevl">
                                    <div style="width:80%;margin:auto">
                                        <div id="vorschauparent">
                                            <table id="server_list_table">
                                                <tbody>
                                                    <tr class="server_onl">
                                                        <td class="status_cell">
                                                            <span class="status_icon_onl" style="background-color: #00FF17;"></span>
                                                        </td>
                                                        <td title="Iguaserver" class="servername_cell"><div class="servername_nolink">Iguaserver - (v346.16)</div></td>
                                                        <td class="players_cell"><div class="outer_bar"><div class="inner_bar"><span class="players_numeric">43/70</span></div></div></td>
                                                        <td class="img-cell"><img src="../../html/img/logo/arkse.webp" width="80px" height="80px" style="float:right;margin-right: 8px;" alt="Ark Game Logo"></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php if (isset($themescript)){echo $themescript;}?>
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" style="margin-top: 5px;">
                            <input type="text" id="themeinput" name="themeinput" style="display: none">
                            <div class="flex" style="justify-content: flex-end;line-height: 34px;">
                                <?php
                                if (isset($notification)) {echo $notification;}
                                ?>
                                <input class="addsrv" type="submit" name="submittheme" value="Submit">
                            </div>
                        </form>
                    </div>
                </details>
                <details>
                    <summary>API Keys</summary>
                    <div class="detailscontent"></div>
                </details>
                <details>
                    <summary>Other</summary>
                    <div class="detailscontent"></div>
                </details>
            </div>
        </div>
        <?php include 'notification.php'?>
        <!-- Information about system | right-->
        <div id="zusammenfassung">
            <div id="clock"></div>
            <div class="info"><div class="attribute">Version</div><div style="word-break: keep-all;"><?php echo $version?></div></div>
            <div class="info"><div class="attribute">Game Server</div><div style="word-break: keep-all;"><?php echo $servercount?></div></div>
            <div class="info"><div class="attribute">Users</div><div style="word-break: keep-all;"><?php echo $usercount?></div></div>
            <br>
            <div class="info"><div class="attribute" title="Web Server User">WS User</div><div style="word-break: keep-all;"><?php echo exec('whoami'); ?></div></div>
            <div class="info"><div class="attribute">PHP Version</div><div style="word-break: keep-all;"><?php echo phpversion(); ?></div></div>
            <div class="info"><div class="attribute">Web Server</div><div style="word-break: keep-all;"><?php echo $_SERVER["SERVER_SOFTWARE"] ?></div></div>
        </div>
        <?php endif;?>
    </div>
</main>
<script src="detailsdropdown.js"></script>
<script src="script.js"></script>
<script>
    function currentTheme() {
        let currenttheme = "<?php echo $css_template?>";
        if (currenttheme === "dark") {
            document.getElementById("btndark").click();
        } else if (currenttheme === "light") {
            document.getElementById("btnlight").click();
        } else if (currenttheme === "summer-night") {
            document.getElementById("btnsnight").click();
        } else if (currenttheme === "summer-night-inverted") {
            document.getElementById("btnsnightinv").click();
        } else if (currenttheme === "midnight") {
            document.getElementById("btnmidnight").click();
        }
    }
</script>
</body>
</html>