<?php
$configfile = '../../html/config.php';
require_once $configfile;
session_start();
if (!isset($_SESSION['username'])) {
    $_SESSION['msg'] = "You have to log in first";
    header('location: ../../?login=true');
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

// Update profile picture
if (array_key_exists('chpp', $_POST)) {
    $seed = preg_replace('/[^A-Za-z0-9\-]/', '', $_POST["seed"]);
    $sprite = $_POST["slctsprite"];
    if ($sprite == "human") {
        $sprite = $_POST["selectsph"];
    }
    $sql = "UPDATE users SET sprite='$sprite', seed='$seed' WHERE username='$username'";
    mysqli_query($conn, $sql);
}

$sql = "SELECT server, sprite, seed FROM users WHERE username='$username'";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $serverjson = $row['server'];
        $sprite = $row['sprite'];
        $seed = $row['seed'];
    }
}
if ($username != "admin") {
    $serverjson = json_decode($serverjson);
    if ($serverjson[0] == 0) {
        $servercount = 0;
    } else {
        $servercount = count($serverjson);
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
    $dbscript = "<script>$(document).ready( function () {"."$('#dbsummary').click();});</script>";
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
    $dbscript = "<script>$(document).ready( function () {"."$('#dbsummary').click();});</script>";
}
// Choose a theme
if (array_key_exists('submittheme', $_POST)) {
    $css_template = $_POST["themeinput"];
    $notification['theme'] = "<p>Theme updated<p>";
    $themescript = "<script>$(document).ready( function () {"."$('#themesummary').click();});</script>";
    $configcontent=file_get_contents($configfile);
    $configcontent = preg_replace('/\$css_template = \"(.*?)\";/', '$css_template = "'.$css_template.'";', $configcontent);
    file_put_contents($configfile, $configcontent);
}
// Edit API Keys
if (array_key_exists('apikey', $_POST)) {
    $steamwebapi_key = $_POST["steam-api"];
    $rustmapsapi_key = $_POST["rustmaps-api"];
    $notification['apikey'] = "<p>API Keys updated<p>";
    $configcontent=file_get_contents($configfile);
    $apikeyscript = "<script>$(document).ready( function () {"."$('#apikeysummary').click();});</script>";
    $configcontent = preg_replace('/\$steamwebapi_key = \"(.*?)\";/', '$steamwebapi_key = "'.$steamwebapi_key.'";', $configcontent);
    $configcontent = preg_replace('/\$rustmapsapi_key = \"(.*?)\";/', '$rustmapsapi_key = "'.$rustmapsapi_key.'";', $configcontent);
    file_put_contents($configfile, $configcontent);
}
// Edit other settings
if (array_key_exists('other', $_POST)) {
    $register = $_POST["registration"];
    $notification['other'] = "<p>Settings updated<p>";
    $configcontent=file_get_contents($configfile);
    $otherscript = "<script>$(document).ready( function () {"."$('#othersummary').click();});</script>";
    $configcontent = preg_replace('/\$register = (.*?);/', '$register = '.$register.';', $configcontent);
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
<body onload="<?php if ($username=="admin"){echo"startTime();";}else{echo"selectstyle();";}?>changetabacc('username')">
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
            <div id="dropdownsettingschild">
                <details id="database">
                    <summary id="dbsummary">Database</summary>
                    <?php if (isset($dbscript)){echo $dbscript;}?>
                    <div class="detailscontent">
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                            <div class="flex">
                                <div class="left">
                                    <div class="input">
                                        <label for="input-ip">IP:</label>
                                        <input id="input-ip" class="input" name="input-ip" type="text" minlength="3" maxlength="40" required="required" placeholder="xxx.xxx.xxx.xx" autocomplete="off" <?php if (isset($DB_SERVER)){echo "value=$DB_SERVER";}?>>
                                    </div>
                                    <div class="input">
                                        <label for="input-dbname">DB Name:</label>
                                        <input id="input-dbname" class="input" name="input-dbname" type="text" minlength="3" placeholder="xxxxxxx" autocomplete="off" <?php if (isset($DB_NAME)){echo "value=$DB_NAME";}?>>
                                    </div>
                                </div>
                                <div class="right">
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
                <details id="design">
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
                                    <div id="vorschauserver">
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
                            <div style="display: flex;justify-content: flex-end;line-height: 34px;">
                                <?php
                                if (isset($notification['theme'])) {echo $notification['theme'];}
                                ?>
                                <input class="addsrv" type="submit" name="submittheme" value="Submit">
                            </div>
                        </form>
                    </div>
                </details>
                <details id="api">
                    <summary id="apikeysummary">API Keys</summary>
                    <?php if (isset($apikeyscript)){echo $apikeyscript;}?>
                    <div class="detailscontent">
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                            <div class="flex">
                                <div class="left">
                                    <div class="input">
                                        <label for="input-ip" style="display: flex">Steam Web API Key<a target="_blank" href="https://docs.iguaserver.de/settings#steam-web-api-key" title="Find out more about the Steam Web API Key" style="height: 0;"><img src="../../html/img/questionmark.svg" height="19px" alt="" style="margin: 2px 0 0 5px;cursor: pointer;"></a></label>
                                        <input id="input-ip" class="input" name="steam-api" type="text" minlength="32" maxlength="32" placeholder="xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx" autocomplete="off" <?php if (isset($steamwebapi_key)){echo "value=$steamwebapi_key";}?>>
                                    </div>
                                </div>
                                <div class="right">
                                    <div class="input">
                                        <label for="input-ip" style="display: flex">RustMaps.com API Key<a target="_blank" href="https://docs.iguaserver.de/settings#rustmaps.com-api-key" title="Find out more about the RustMaps.com API Key" style="height: 0;"><img src="../../html/img/questionmark.svg" height="19px" alt="" style="margin: 2px 0 0 5px;cursor: pointer;"></a></label>
                                        <input id="input-ip" class="input" name="rustmaps-api" type="text" minlength="25" maxlength="45" placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx" autocomplete="off" <?php if (isset($rustmapsapi_key)){echo "value=$rustmapsapi_key";}?>>
                                    </div>
                                </div>
                            </div>
                            <div style="display:flex; justify-content: flex-end;margin-right: 3px;line-height: 34px;">
                                <?php if (isset($notification['apikey'])) {echo $notification['apikey'];} ?>
                                <input class="addsrv" type="submit" name="apikey" value="Submit">
                            </div>
                        </form>
                    </div>
                </details>
                <details>
                    <summary id="othersummary">Other</summary>
                    <?php if (isset($otherscript)){echo $otherscript;}?>
                    <div class="detailscontent">
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                            <div class="flex">
                                <label for="selectregistration">Registration:</label>
                                <select required="required" name="registration" id="selectregistration">
                                    <option <?php if($register){echo "selected ";}?>value="TRUE">Enable</option>
                                    <option <?php if(!$register){echo "selected ";}?>value="FALSE">Disable</option>
                                </select>
                                <a target="_blank" href="https://docs.iguaserver.de/settings#registration" title="Find out more about the registration." style="height: 0;"><img src="../../html/img/questionmark.svg" height="19px" alt="" style="margin: 2px 0 0 5px;cursor: pointer;"></a>
                            </div>
                            <div class="flex" style="justify-content: flex-end;margin-right: 3px;line-height: 34px;">
                                <?php if (isset($notification['other'])) {echo $notification['other'];} ?>
                                <input class="addsrv" type="submit" name="other" value="Submit">
                            </div>
                        </form>
                    </div>
                </details>
            </div>
        </div>
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
        <?php else:?>
        <div id="dropdownsettings">
            <h1 id="settingsh1">Settings</h1>
            <div id="dropdownsettingschild">
                <details id="account">
                    <summary id="accountsummary">Account</summary>
                    <?php if (isset($accountscript)){echo $accountscript;}?>
                    <div class="detailscontent">
                        <div class="flex">
                            <div class="tabs-left">
                                <button class="tablinksacc active" onclick="changetabacc('username')" id="btndark">Username</button>
                                <button class="tablinksacc" onclick="changetabacc('password')" id="btnlight">Password</button>
                                <button class="tablinksacc" onclick="changetabacc('delete')" id="btnsnight">Delete account</button>
                            </div>
                            <div class="tabacc">Coming Soon!</div>
                            <div class="tabacc">Coming Soon!</div>
                            <div class="tabacc">Coming Soon!</div>
                        </div>
                    </div>
                </details>
            </div>
        </div>
        <div id="zusammenfassung">
            <div id="ppicture"><?php echo "<img id='ppictureimg' src='https://api.dicebear.com/7.x/$sprite/svg?seed=$seed'>"?></div>
            <div id="customizecontent">
                <details id="customizedetails">
                    <summary id="customizesummary">Customize</summary>
                    <div class="detailscontent">
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                            <select required="required" id="style" class="sstyle" name="slctsprite" onchange="selectstyle()">
                                <option disabled selected <?php if(!isset($sprite)){echo "selected ";}?> style="display:none">select a sprite</option>
                                <option <?php if($sprite=="adventurer"){echo "selected ";}?>value="adventurer">Adventurer</option>
                                <option <?php if($sprite=="adventurer-neutral"){echo "selected ";}?>value="adventurer-neutral">Adventurer-Neutral</option>
                                <option <?php if($sprite=="notionists"){echo "selected ";}?>value="notionists">Notonionist</option>
                                <option <?php if($sprite=="avataaars"){echo "selected ";}?>value="avataaars">Human</option>
                                <option <?php if($sprite=="bottts"){echo "selected ";}?>value="bottts">Bottts</option>
                                <option <?php if($sprite=="croodles"){echo "selected ";}?>value="croodles">Croodles</option>
                                <option <?php if($sprite=="identicon"){echo "selected ";}?>value="identicon">Identicon</option>
                                <option <?php if($sprite=="pixel-art"){echo "selected ";}?>value="pixel-art">Pixel Art</option>
                                <option <?php if($sprite=="pixel-art-neutral"){echo "selected ";}?>value="pixel-art-neutral">Pixel Art Neutral</option>
                            </select>
                            <select required="required" id="selectsph" class="sstyle" name="selectsph" onchange="selectstyle()" style="display: none">
                                <option <?php if($sprite=="male"){echo "selected ";}?>value="male">Male</option>
                                <option <?php if($sprite=="female"){echo "selected ";}?>value="female">Female</option>
                            </select>
                            <input type="text" id="seed" name="seed" onkeyup="selectstyle()" value="<?php echo $seed?>">

                            <div style="display:flex">
                                <div class="mk6" style="justify-content: flex-start;">
                                    <div id="newseed" onclick="newseed()" style="padding: 0;background-color: rgba(255, 255, 255);border: none; width: 50px;height: 32px;border-radius: 4px;cursor:pointer">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" style="margin-top:2px"><path fill="#000" d="M9 12l-4.463 4.969-4.537-4.969h3c0-4.97 4.03-9 9-9 2.395 0 4.565.942 6.179 2.468l-2.004 2.231c-1.081-1.05-2.553-1.699-4.175-1.699-3.309 0-6 2.691-6 6h3zm10.463-4.969l-4.463 4.969h3c0 3.309-2.691 6-6 6-1.623 0-3.094-.65-4.175-1.699l-2.004 2.231c1.613 1.526 3.784 2.468 6.179 2.468 4.97 0 9-4.03 9-9h3l-4.537-4.969z"/></svg>
                                    </div>
                                </div>
                                <div class="mk6" style="justify-content: flex-end;">
                                    <input class="addsrv" type="submit" name="chpp" value="Submit">
                                </div>
                            </div>

                        </form>
                    </div>
                </details>
            </div>
            <div class="info"><div class="attribute">Name</div><div style="word-break: keep-all;"><?php echo $username?></div></div>
            <div class="info"><div class="attribute">Game Server</div><div style="word-break: keep-all;"><?php echo $servercount?></div></div>
            <div class="info"><div class="attribute">Users</div><div style="word-break: keep-all;"><?php echo $usercount?></div></div>
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