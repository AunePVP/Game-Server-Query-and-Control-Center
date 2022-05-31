<?php
// TODO Remove this usercheck before publishing the Website. Why? Because this page is meant for the installation. When people install the Website, they have no Admin account and will create one.
//session_start();
//if (!isset($_SESSION['username'])) {
//    $_SESSION['backURI'] = $_SERVER['REQUEST_URI'];
//    echo $_SESSION['backURI'];
//    header('location: ../users/login.php');
//}
//
$addservers = 0;
$confname = "config.php";

require_once $confname;
if (isset($install) && !$install) {
    header("Location: /");
}
$path = $confname;
$title = "Install";
$configfail = "Writeable";
$logfail = "Readable";
$configfailclass = 0;
if (!is_writable($confname)) {
    $configfailclass = "red";
    $configfail = "Not Writeable";
}
if (!is_readable("/var/log")) {
    $logfailclass = "red";
    $logfail = "Not Writeable";
}

//
//
if (array_key_exists('validate', $_POST)) {
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
if (array_key_exists('submit', $_POST)) {
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
        $addservers = TRUE;
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
            '$addservers = TRUE;' . "\n" .
            "?>";
        echo '<script>';
        echo 'alert("Database updated!")';
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
function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link rel="stylesheet" href="../css/install.css">
    <title><?php
        echo $title ?></title>
</head>
<body>
<section id="top-section">
    <div id="top">
        <h1>GAME SERVER QUERY</h1>
    </div>
    <div id="bottom">
        <div class="side">
            <div>config/config.php:&nbsp</div>
            <div class="green <?php
            if (isset($configfailclass)) {
                echo $configfailclass;
            } ?>"><?php
                echo $configfail ?></div>
        </div>
        <div class="side">
            <div>/var/log:&nbsp</div>
            <div class="green <?php
            if (isset($logfailclass)) {
                echo $logfailclass;
            } ?>"><?php
                echo $logfail ?></div>
        </div>
        <div class="content">
            <div class="conf">
                <div id="left">
                    <h2>Settings</h2>
                    <form method="post" autocomplete="off" action="<?php
                    echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <div class="sub-section">
                            <div class="element">
                                <div class="top-text">CSS Template</div>
                                <label>
                                    <select name="css_templateI">
                                        <option value="light" <?php
                                        if ($css_template == "light") {
                                            echo "selected='selected'";
                                        } ?>>Light
                                        </option>
                                        <option value="dark"<?php
                                        if ($css_template == "dark") {
                                            echo "selected='selected'";
                                        } ?>>Dark
                                        </option>
                                    </select>
                                </label>
                            </div>
                            <div class="element">
                                <div class="top-text">User authentication</div>
                                <label>
                                    <select name="authenticationI">
                                        <option value="1" <?php
                                        if ($authentication == 1) {
                                            echo "selected='selected'";
                                        } ?>>On
                                        </option>
                                        <option value="0"<?php
                                        if ($authentication == 0) {
                                            echo "selected='selected'";
                                        } ?>>Off
                                        </option>
                                    </select>
                                </label>
                            </div>
                            <div class="element">
                                <div class="top-text">Create new accounts</div>
                                <label>
                                    <select name="create_usersI">
                                        <option value="1" <?php
                                        if ($create_users == 1) {
                                            echo "selected='selected'";
                                        } ?>>On
                                        </option>
                                        <option value="0"<?php
                                        if ($create_users == 0) {
                                            echo "selected='selected'";
                                        } ?>>Off
                                        </option>
                                    </select>
                                </label>
                            </div>
                        </div>
                        <div class="sub-section">
                            <div class="element">
                                <div class="top-text">Steam WebAPI Key:</div>
                                <div class="content-box">
                                    <label for="steamwebkey"></label><input type="password" id="steamwebkey"
                                                                            name="steamwebapi_keyI" size="17"
                                                                            value="<?php
                                                                            if (isset($steamwebapi_key)) {
                                                                                echo $steamwebapi_key;
                                                                            } ?>">
                                    <label class="marginauto0">
                                        <input type="checkbox" onclick="steamwebapikeyFuntion()">
                                    </label>
                                    <div class="marginauto0">
                                        Show Key
                                    </div>
                                </div>
                                <br><br>
                            </div>
                        </div>
                </div>
                <div id="right">
                    <h2>Database</h2>
                    <div class="sub-section">
                        <div class="slide">
                            <div class="element">
                                <div class="top-text">Ip adress or localhost:</div>
                                <div class="content-box">
                                    <input type="text" name="DB_SERVERI" value="<?php
                                    echo $DB_SERVER; ?>"
                                           pattern="^(localhost|(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]))?$"
                                           placeholder="localhost" title="IP adress or localhost">
                                </div>
                                <br><br>
                            </div>
                            <div class="element">
                                <div class="top-text">Username:</div>
                                <div class="content-box">
                                    <label class="marginauto0">
                                        <input type="text" name="DB_USERNAMEI" value="<?php
                                        echo $DB_USERNAME; ?>">
                                    </label>
                                </div>
                                <br><br>
                            </div>
                        </div>
                        <div class="slide">
                            <div class="element">
                                <div class="top-text">Password:</div>
                                <div class="content-box">
                                    <label for="password"></label><input type="password" id="password"
                                                                         autocomplete="new-password" name="DB_PASSWORDI"
                                                                         value="<?php
                                                                         echo $DB_PASSWORD; ?>">
                                    <label class="marginauto0">
                                        <input type="checkbox" onclick="passwordFunction()">
                                    </label>
                                    <div class="marginauto0">
                                        Show password
                                    </div>
                                </div>
                                <br><br>
                            </div>
                            <div class="element">
                                <div class="top-text">DB Name:</div>
                                <div class="content-box">
                                    <label>
                                        <input type="text" name="DB_NAMEI" value="<?php
                                        echo $DB_NAME; ?>">
                                    </label>
                                </div>
                            </div>
                            <br><br>
                        </div>
                    </div>
                    <div>
                        <input type="submit" name="submit" value="Submit">
                        <input type="submit" name="validate" value="Validate">
                    </div>
                    <div id="sqlvme">
                        <?php
                        if (isset($sqlvalidatemessage)) {
                            echo $sqlvalidatemessage;
                        } ?></div>
                </div>
            </div>
        </div>
    </div>
    <script src="script.js"></script>
</section>
<section id="bottom-section">
    <table>
        <?php
        if (array_key_exists('submitserver', $_POST)) {
            $ID = test_input($_POST["ID"]);
            $owner = test_input($_POST["owner"]);
            $IP = test_input($_POST["IP"]);
            $type = test_input($_POST["type"]);
            $QueryPort = test_input($_POST["QueryPort"]);
            $GamePort = test_input($_POST["GamePort"]);
            $RconPort = test_input($_POST["RconPort"]);
            $BatPort = test_input($_POST["BatPort"]);
            $enabled = TRUE;

            $conn = new mysqli($DB_SERVER, $DB_USERNAME, $DB_PASSWORD, $DB_NAME);
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
            $sql = "INSERT INTO serverconfig (ID, owner, IP, type, QueryPort, GamePort, RconPort, BatPort, enabled) VALUES ('$ID', '$owner', '$IP', '$type', '$QueryPort', '$GamePort', '$RconPort', '$BatPort', '1')";
            if ($conn->query($sql) === TRUE) {
                echo "New record created successfully";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
            $conn->close();
        }
        if ($addservers):
        $conn = new mysqli($DB_SERVER, $DB_USERNAME, $DB_PASSWORD, $DB_NAME);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $sql = "SELECT * FROM serverconfig ORDER BY ID DESC LIMIT 1";
        $result = $conn->query($sql);
        $newid = "1";
        if ($result->num_rows > 0) {
            // output data of each row
            while ($row = $result->fetch_assoc()) {
                $newid = $row["ID"] + 1;
            }
        } else {
            echo "0 results";
        }
        $sql = "SELECT * FROM serverconfig";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $register = TRUE;
            echo "<tr>";
            echo "<th scope='col'>ID</th>";
            echo "<th>Owner</th>";
            echo "<th>IP</th>";
            echo "<th>Type</th>";
            echo "<th>Query Port</th>";
            echo "<th>Game Port</th>";
            echo "<th>Rcon Port</th>";
            echo "<th>Battlemetrics ID</th>";
            echo "<th>Enabled</th>";
            echo "</tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["ID"] . "</td>";
                echo "<td>" . $row["owner"] . "</td>";
                echo "<td>" . $row["IP"] . "</td>";
                echo "<td>" . $row["type"] . "</td>";
                echo "<td>" . $row["QueryPort"] . "</td>";
                echo "<td>" . $row["GamePort"] . "</td>";
                echo "<td>" . $row["RconPort"] . "</td>";
                echo "<td>" . $row["BatPort"] . "</td>";
                echo "<td>" . $row["enabled"] . "</td>";
                echo "</tr>";
            }
        }
        $conn->close();
        ?>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <tr>
                <td>
                    <div id="id"><input class="addservers" type="text" name="ID" size="2" value="<?php echo $newid ?>" readonly="readonly"></div>
                </td>
                <td>
                    <div id="owner"><label><select name="owner">
                                <option value="public">Public</option>
                                <option value="admin">Admin</option>
                            </select></label></div>
                </td>
                <td>
                    <div id="ip"><input class="addservers" type="text" name="IP" size="16"></div>
                </td>
                <td>
                    <div><label><select name="type" id="type" onchange="servertype()">
                                <option value="arkse">ARK Survival Evolved</option>
                                <option value="minecraft">Minecraft</option>
                            </select></label></div>
                </td>
                <td>
                    <div id="QueryPort"><input class="addservers" type="text" name="QueryPort" size="8" pattern="^[0-9]{3,5}$"></div>
                </td>
                <td>
                    <div id="GamePort"><input class="addservers" type="text" name="GamePort" size="8" pattern="^[0-9]{3,5}$"></div>
                </td>
                <td>
                    <div id="RconPort"><input class="addservers" type="text" name="RconPort" size="8" pattern="^[0-9]{3,5}$"></div>
                </td>
                <td>
                    <div id="BatPort"><input class="addservers" type="text" name="BatPort" size="12" pattern="^[0-9]{4,12}$"></div>
                </td>
                <td>
                    <div id="enabled">1</div>
                </td>
            </tr>
    </table>
    <div style="display: flex">
        <input style="margin: 5px 40px auto auto;" type="submit" name="submitserver" value="Submit">
    </div>
        <?php
        endif;
        ?>
</section>
<?php
if ($register):
    $username = "";
    $errors = array();
    $_SESSION['success'] = "";
    $db = mysqli_connect($DB_SERVER, $DB_USERNAME, $DB_PASSWORD, $DB_NAME);
    if (isset($_POST['reg_user'])) {
        $username = mysqli_real_escape_string($db, $_POST['username']);
        $password_1 = mysqli_real_escape_string($db, $_POST['password_1']);
        $password_2 = mysqli_real_escape_string($db, $_POST['password_2']);
        if (empty($username)) {
            array_push($errors, "Username is required");
        }
        if (empty($password_1)) {
            array_push($errors, "Password is required");
        }
        if ($password_1 != $password_2) {
            array_push($errors, "The two passwords do not match");
            // Checking if the passwords match
        }
        // If the form is error free, then register the user
        if (count($errors) == 0) {
            // Password encryption to increase data security
            $password = md5($password_1);
            // Inserting data into table
            $query = "INSERT INTO users (username, password) VALUES('$username', '$password')";
            mysqli_query($db, $query);
            // Storing username of the logged in user,
            // in the session variable
            $_SESSION['username'] = $username;

            // Welcome message
            $_SESSION['success'] = "You have logged in";
            // Page on which the user will be
            // redirected after logging in
            unset($_SESSION['backURI']);
            //header("location:".$backURL);
            echo "You created the user ".$username."!";
        }
    }
?>
<section id="register">
    <div style="margin: auto;">
        <form method="post" action=<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>>
            <div class="input-group">
                <label>Enter Username</label>
                <input type="text" name="username"
                       value="<?php if(isset($username)) {echo $username;} ?>">
            </div>
            <div class="input-group">
                <label>Enter Password</label>
                <input type="password" name="password_1">
            </div>
            <div class="input-group">
                <label>Confirm password</label>
                <input type="password" name="password_2">
            </div>
            <div class="input-group">
                <button type="submit" class="btn"
                        name="reg_user">
                    Register
                </button>
            </div>
        </form>
    </div>
</section>
<?php
endif;
?>
<?php
if (array_key_exists('finish', $_POST)) {

    $file = __FILE__;
    if(file_exists($file)) {
        unlink($file);
    }
    header("Location: ../users/login.php");
}?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <div style="display: flex">
        <input id="destroy" type="submit" name="finish" value="Finish">
    </div>
</body>
