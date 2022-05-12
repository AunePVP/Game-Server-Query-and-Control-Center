<?php
// TODO Remove this usercheck before publishing the Website. Why? Because this page is meant for the installation. When people install the Website, they have no Admin account and will create one.
session_start();
if (!isset($_SESSION['username'])) {
    $_SESSION['backURI'] = $_SERVER['REQUEST_URI'];
    echo $_SESSION['backURI'];
    header('location: ../users/login.php');
}
//

$addservers = 0;
require_once 'config.php';
if (isset($install) && !$install) {
    header("Location: /");
}
$path = "config.php";
$title = "Install";
$configfail = "Writeable";
$logfail = "Readable";
$configfailclass = 0;
if (!is_writable("config.php")) {
    $configfailclass = "red";
    $configfail = "Not Writeable";
}
if (!is_readable("/var/log")) {
    $logfailclass = "red";
    $logfail = "Not Writeable";
}

//
//

if(array_key_exists('validate', $_POST)) {
    $DB_SERVERI = test_input($_POST["DB_SERVERI"]);
    $DB_USERNAMEI = test_input($_POST["DB_USERNAMEI"]);
    $DB_PASSWORDI = test_input($_POST["DB_PASSWORDI"]);
    $DB_NAMEI = test_input($_POST["DB_NAMEI"]);
    $DB_SERVER = $DB_SERVERI;
    $DB_USERNAME = $DB_USERNAMEI;
    $DB_PASSWORD = $DB_PASSWORDI;
    $DB_NAME = $DB_NAMEI;

    $conn = new mysqli($DB_SERVERI, $DB_USERNAMEI, $DB_PASSWORDI, $DB_NAMEI);
    // Check connection
    if ($conn->connect_error) {
        $sqlvalidatemessage = "Connection Failed!";
    } else {
        $sqlvalidatemessage = "Connection verified!";
    }
}
function test_input($data){
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if(array_key_exists('submit', $_POST)) {
    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        $authenticationI = test_input($_POST["authenticationI"]);
        $create_usersI = test_input($_POST["create_usersI"]);
        $steamwebapi_keyI = test_input($_POST["steamwebapi_keyI"]);
        $DB_SERVERI = test_input($_POST["DB_SERVERI"]);
        $DB_USERNAMEI = test_input($_POST["DB_USERNAMEI"]);
        $DB_PASSWORDI = test_input($_POST["DB_PASSWORDI"]);
        $DB_NAMEI = test_input($_POST["DB_NAMEI"]);
        $css_templateI = test_input($_POST["css_templateI"]);
        $submit = test_input($_POST["submit"]);
    }
    if (!empty($DB_SERVERI) && !empty($DB_USERNAMEI) && !empty($DB_PASSWORDI) && !empty($DB_NAMEI) && !empty($steamwebapi_keyI) && !empty($submit)) {
        // TODO Remove the # when this project is released.
        #$conn = new mysqli($DB_SERVERI, $DB_USERNAMEI, $DB_PASSWORDI);
        #if ($conn->connect_error) {
        #    die("Connection failed: " . $conn->connect_error);
        #}

        $myfile = fopen($path, "w") or die("Unable to open file!");
        $write = "<?php" . "\n" .
            'include ("langconf.php");' . "\n" .
            '$install = 1;' . "\n" .
            '$authentication = ' . $authenticationI . ';' . "\n" .
            '$create_users = ' . $create_usersI . ';' . "\n" .
            '$steamwebapi_key = "' . $steamwebapi_keyI . '";' . "\n" . "\n" .
            '$DB_SERVER = "' . $DB_SERVERI . '";' . "\n" .
            '$DB_USERNAME = "' . $DB_USERNAMEI . '";' . "\n" .
            '$DB_PASSWORD = "' . $DB_PASSWORDI . '";' . "\n" .
            '$DB_NAME = "' . $DB_NAMEI . '";' . "\n" .
            '$css_template = "' . $css_templateI . '";' . "\n" .
            '$steamapi = "https://api.steampowered.com/";' . "\n" .
            '$lang = "en";' . "\n" .
            "?>";
        echo '<script>';
        echo 'alert("Please Delete this file.")';
        echo '</script>';
        $create_users = $create_usersI;
        $authentication = $authenticationI;
        $css_template = $css_templateI;
        $DB_SERVER = $DB_SERVERI;
        $DB_USERNAME = $DB_USERNAMEI;
        $DB_PASSWORD = $DB_PASSWORDI;
        $DB_NAME = $DB_NAMEI;
        fwrite($myfile, $write);
        fclose($myfile);
    }
}
//
//
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <title><?php echo $title?></title>
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap");
        html, body {
            margin: 0;
            padding: 0;
        }
        body {
            width: 70%;
            margin: auto;
            height: 100vh;
        }
	#top-section {
	    <?php if($addservers) {
            echo "display: none";
        } else {
            echo "height: 100%";
        }?>
	}
        #top {
            min-height: 20%;
            display: flex;
            background-color: #dddddd;
            border-bottom: solid;
            border-width: 1px;
        }
        #left {
            display: flex;
            justify-content: center;
        }
        #bottom {
            background-color: #f1f1f1;
            padding: 30px;
            min-height: 80%;
        }
        #center {
            width: 100%;
        }
        #title {
            margin: auto 20px;
            font-family: 'Montserrat', sans-serif;
            font-weight: bold;
            font-size: 27px;
	    white-space: nowrap;
        }
        #right {
	display: flex;
	}
	#right-top {
            white-space: nowrap;
            font-size: 16px;
	    margin: auto 15px;
        }
        .side {
            display: flex;
            font-family: Montserrat, sans-serif;
        }
        .green {
            color: #1cc602;
        }
        .red {
            color: #fa251e!important;
            font-weight: bold;
        }
        .top-text {
            font-family: Arial, sans-serif;
            font-weight: bold;
            padding: 20px 0 0;
        }
        .content-box {
            display: flex;
            float: left;
            justify-content: center;
        }
        .slide {
            margin: 0 15px;
        }
        h2 {
            margin-bottom: 0;
        }
        input {
            margin: 5px 8px;
            padding: 8px;
        }
        @media only screen and (max-width: 900px) {
            body {
                width: 80%;
            }
        }
        @media only screen and (max-width: 700px) {
            body {
                width: 90%;
            }
        }
        @media only screen and (max-width: 650px) {
            body {
                width: 100%;
            }
        }
        @media only screen and (max-width: 625px) {
            .sub-section {
                display: block !important;
            }
	    #title {
	    white-space: normal !important;
	    }
        }
    </style>
</head>
<body>
<section id="top-section">
<div id="top">
    <div id="left"><div id="title">GAME SERVER QUERY</div></div>
    <div id="center"></div>
    <div id="right">
        <div id="right-top">
            <div id="current_time"></div>
            <div><?php echo 'PHP version: ' . phpversion(); ?></div>
        </div>
    </div>
</div>
<div id="bottom">
    <div class="section">
        <div class="side">
            <div>config/config.php:&nbsp</div>
            <div class="green <?php if(isset($configfailclass)){echo $configfailclass;} ?>"><?php echo $configfail ?></div>
        </div>
        <div class="side">
            <div>/var/log:&nbsp</div>
            <div class="green <?php if(isset($logfailclass)){echo $logfailclass;}?>"><?php echo $logfail ?></div>
        </div>
    </div>
    <div class="content">
        <form method="post" autocomplete="off" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <div class="sub-section">
                <div class="element">
                    <div class="top-text">CSS Template</div>
                    <label>
                        <select name="css_templateI">
                            <option value="light" <?php if($css_template == "light"){echo "selected='selected'";} ?>>Light</option>
                            <option value="dark"<?php if($css_template == "dark"){echo "selected='selected'";} ?>>Dark</option>
                        </select>
                    </label>
                </div>
                <div class="element">
                    <div class="top-text">User authentication</div>
                    <label>
                        <select name="authenticationI">
                            <option value="1" <?php if($authentication == 1){echo "selected='selected'";} ?>>On</option>
                            <option value="0"<?php if($authentication == 0){echo "selected='selected'";} ?>>Off</option>
                        </select>
                    </label>
                </div>
                <div class="element">
                    <div class="top-text">Create new accounts</div>
                    <label>
                        <select name="create_usersI">
                            <option value="1" <?php if($create_users == 1){echo "selected='selected'";} ?>>On</option>
                            <option value="0"<?php if($create_users == 0){echo "selected='selected'";} ?>>Off</option>
                        </select>
                    </label>
                </div>
            </div>
            <div class="sub-section">
                <div class="element">
                    <div class="top-text">Steam WebAPI Key:</div>
                    <div class="content-box">
                        <label for="steamwebkey"></label><input type="password" id="steamwebkey" name="steamwebapi_keyI" size="17" value="<?php if(isset($steamwebapi_key)){echo $steamwebapi_key;}?>">
                        <label>
                            <input type="checkbox" onclick="steamwebapikeyFuntion()" style="margin: auto;">
                        </label>
                        <div style="margin: auto 0 auto 10px;font-family: Arial, sans-serif;font-size:14px;">Show Key</div>
                    </div>
                    <br><br>
                </div>
            </div><h2>Database</h2>
            <div class="sub-section" style="display: flex;">
                <div class="slide">
                    <div class="element">
                        <div class="top-text">Ip adress or localhost:</div>
                        <div class="content-box">
                            <input type="text" name="DB_SERVERI" value="<?php echo $DB_SERVER;?>" pattern="^(localhost|(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]))?$" placeholder="localhost" title="IP adress or localhost">
                        </div>
                        <br><br>
                    </div>
                    <div class="element">
                        <div class="top-text">Username:</div>
                        <div class="content-box">
                            <label>
                                <input type="text" name="DB_USERNAMEI" value="<?php echo $DB_USERNAME;?>">
                            </label>
                        </div>
                        <br><br>
                    </div></div>
                <div class="slide">
                    <div class="element">
                        <div class="top-text">Password:</div>
                        <div class="content-box" style="display: flex;">
                            <label for="password"></label><input type="password" id="password" autocomplete="new-password" name="DB_PASSWORDI" value="<?php echo $DB_PASSWORD;?>">
                            <label>
                                <input type="checkbox" onclick="passwordFunction()" style="margin: auto;">
                            </label>
                            <div style="font-family: Arial, sans-serif;font-size:14px;margin: auto 0 auto 10px;">Show password</div>
                        </div>
                        <br><br>
                    </div>
                    <div class="element">
                        <div class="top-text">DB Name:</div>
                        <div class="content-box">
                            <label>
                                <input type="text" name="DB_NAMEI" value="<?php echo $DB_NAME;?>">
                            </label>
                        </div></div>
                    <br><br>
                </div>
            </div>
            <div style="float:left;width:100%;">
                <input type="submit" name="submit" value="submit">
                <input type="submit" name="validate" value="validate">
            </div>
            <div id="sqlvme">
                <?php
                if (isset($sqlvalidatemessage)) {
                    echo $sqlvalidatemessage;
                } ?></div>
    </div>
</div>

<script src="script.js"></script>
</section>
<section id="bottom-section">
<?php
if($addservers){
    include 'addservers.php';
}
?>
</section>
</body>
