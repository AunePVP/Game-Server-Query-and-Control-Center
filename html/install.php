<?php
$configfile = 'config.php';
require_once $configfile;
if (isset($install) && !$install) {
    header("Location: /");
}
function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
$displaysubmit = "none";
// Get post and check if connection to mysql db is successful
if (array_key_exists('validate', $_POST)) {
    $STEAMWEBAPI_KEYI = test_input($_POST["steamwebapi-key"]);
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
    try {
        $conn = mysqli_connect($DB_SERVERI, $DB_USERNAMEI, $DB_PASSWORDI, $DB_NAMEI);
        mysqli_close($conn);
    } catch(mysqli_sql_exception $e) {
        $sqlvalidatemessage = "Connection failed!";
        $displaysubmit = "none";
    }
}
// Write the data into the config file and create the server table
if (array_key_exists('submit', $_POST)) {
    $STEAMWEBAPI_KEYI = test_input($_POST["steamwebapi-key"]);
    $DB_SERVERI = test_input($_POST["input-ip"]);
    $DB_USERNAMEI = test_input($_POST["input-username"]);
    $DB_PASSWORDI = test_input($_POST["input-password"]);
    $DB_NAMEI = test_input($_POST["input-dbname"]);
    $STEAMWEBAPI_KEY = $STEAMWEBAPI_KEYI;
    $DB_SERVER = $DB_SERVERI;
    $DB_USERNAME = $DB_USERNAMEI;
    $DB_PASSWORD = $DB_PASSWORDI;
    $DB_NAME = $DB_NAMEI;
    // Create table
    $conn = mysqli_connect($DB_SERVERI, $DB_USERNAMEI, $DB_PASSWORDI, $DB_NAMEI);
    $sql = "CREATE TABLE `serverconfig` (
    `ID` int NOT NULL,
    `IP` varchar(60) NOT NULL,
    `type` text NOT NULL,
    `QueryPort` int NOT NULL,
    `GamePort` int NOT NULL,
    `RconPort` int NOT NULL,
    `Name` text NOT NULL,
    `controlserver` JSON NOT NULL
    )";
    if (!mysqli_query($conn, $sql)) {
        echo "Error creating table: " . mysqli_error($conn);
    }
    mysqli_close($conn);
    // Write data into config file
    $myfile = fopen($configfile, "w") or die("Unable to open file!");
    $write = "<?php" . "\n" .
        'include ("langconf.php");' . "\n" .
        '$install = 0;' . "\n" .
        '$STEAMWEBAPI_KEY = "' . $STEAMWEBAPI_KEYI . '";' . "\n\n" .
        '$DB_SERVER = "' . $DB_SERVERI . '";' . "\n" .
        '$DB_USERNAME = "' . $DB_USERNAMEI . '";' . "\n" .
        '$DB_PASSWORD = "' . $DB_PASSWORDI . '";' . "\n" .
        '$DB_NAME = "' . $DB_NAMEI . '";' . "\n" .
        '$steamapi = "https://api.steampowered.com/";' . "\n" .
        '$lang = "en";' . "\n" .
        '$register = TRUE;' . "\n" .
        "?>";
    echo '<script>';
    echo 'alert("Config file created!")';
    echo '</script>';
    fwrite($myfile, $write);
    fclose($myfile);
    $register = TRUE;

}

?>
<style>
    .adddbdiv {
        margin: auto;
        width: 500px;
        height: 500px;
        background-color: #2B2F43;
        border-radius: 14px;
        color: white;
    }
    .padding25 {
        padding: 25px;
    }
    .cAx {
        font-family: Helvetica,sans-serif;
        border-bottom: solid;
        margin: 15px 0 9px;
        letter-spacing: 3px;
        font-size: 23px;
    }
    .inputstyle {
        width: -webkit-fill-available;
        padding: 0 5px;
        border-radius: 4px;
        border: none;
        display: inline-block;
        font-size: 16px;
        height: 34px;
        font-family: Helvetica, sans-serif;
        outline: none;
    }
    .input label {
        font-family: Helvetica,sans-serif;
        font-size: 20px;
    }
    .padding25 .input {
        margin: 0 0 10px 0;
    }
    .button {
        border: none;
        background-color: white;
        border-radius: 4px;
        float: right;
        height: 34px;
        font-family: Helvetica, sans-serif;
        font-size: 14px;
        padding: 2px 10px;
        cursor: pointer;
        margin-left: 5px;
    }
    }
</style>
<div class="adddbdiv">
    <div class="padding25">
        <?php if (!isset($register)):?>
        <h2 style="margin: 0 0 10px;font-family: Helvetica,sans-serif;">Settings</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <div class="input"><label for="steamwebapi-key">Steam WebAPI Key:</label><input id="steamwebapi-key" class="inputstyle" name="steamwebapi-key" type="password" required="required" minlength="32" maxlength="32" placeholder="xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx" autocomplete="off" <?php if (isset($STEAMWEBAPI_KEY)){echo "value=$STEAMWEBAPI_KEY";}?>></div>
            <p class="cAx">Database:</p>
            <div class="input"><label for="input-ip">IP:</label><input id="input-ip" class="inputstyle" name="input-ip" type="text" minlength="3" maxlength="40" required="required" placeholder="xxx.xxx.xxx.xx" autocomplete="off" <?php if (isset($DB_SERVER)){echo "value=$DB_SERVER";}?>></div>
            <div class="input"><label for="input-username">Username:</label><input id="input-username" class="inputstyle" name="input-username" type="text" minlength="2" maxlength="15" placeholder="xxxxx" autocomplete="off" <?php if (isset($DB_USERNAME)){echo "value=$DB_USERNAME";}?>></div>
            <div class="input"><label for="input-password">Password:</label><input id="input-password" class="inputstyle" name="input-password" type="password" minlength="3" placeholder="xxxxxxxxxxxx" autocomplete="off" <?php if (isset($DB_PASSWORD)){echo "value=$DB_PASSWORD";}?>></div>
            <div class="input"><label for="input-dbname">DB Name:</label><input id="input-dbname" class="inputstyle" name="input-dbname" type="text" minlength="3" placeholder="xxxxxxx" autocomplete="off" <?php if (isset($DB_NAME)){echo "value=$DB_NAME";}?>></div>
            <div style="display:flex;justify-content: flex-end;-webkit-align-items: center;align-items: center;;">
                <?php if (isset($sqlvalidatemessage)) {echo $sqlvalidatemessage;}?>
                <input class="button" type="submit" name="validate" value="Test connection">
                <input class="button" type="submit" name="submit" value="Submit" style="display: <?php echo $displaysubmit?>">
            </div>
            <?php
            elseif (isset($register)):
                $username = "";
                $errors = array();
                $_SESSION['success'] = "";
                $db = mysqli_connect($DB_SERVER, $DB_USERNAME, $DB_PASSWORD, $DB_NAME);
                if (isset($_POST['reg_user'])) {
                    $username = mysqli_real_escape_string($db, $_POST['username']);
                    $password_1 = mysqli_real_escape_string($db, $_POST['password_1']);
                    $password_2 = mysqli_real_escape_string($db, $_POST['password_2']);
                    if (empty($username)) {
                        $errors[] = "Username is required";
                    }
                    if (empty($password_1)) {
                        $errors[] = "Password is required";
                    }
                    if ($password_1 != $password_2) {
                        $errors[] = "The two passwords do not match";
                        // Checking if the passwords match
                    }
                    // If the form is error free, then register the user
                    if (count($errors) == 0) {
                        // Password encryption to increase data security
                        $password = hash('sha256', $password_1);
                        // Inserting data into table
                        $query = "INSERT INTO users (username, password, server) VALUES('$username', '$password', '{\"0\":0}')";
                        mysqli_query($db, $query);
                        // Storing username of the logged-in user,
                        // in the session variable
                        $_SESSION['username'] = $username;
                        // Welcome message
                        $_SESSION['success'] = "You have logged in";
                        // Page on which the user will be
                        // redirected after logging in
                        unset($_SESSION['backURI']);
                        //header("location:".$backURL);
                        echo "You created the user ".$username."!";
                        echo "<script>setTimeout(function(){window.location.href = '../users/control/server.php';}, 1000)</script>";
                    }
                }
                ?>
                <div style="margin: auto;">
                    <h2 style="margin: 0 0 10px;font-family: Helvetica,sans-serif;">Create Admin User</h2>
                    <form method="post" action=<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>>
                        <div class="input">
                            <label for="username">Username</label><input type="text" class="inputstyle" readonly="readonly" name="username" id="username" value="admin">
                        </div>
                        <div class="input">
                            <label for="password_1">Password</label><input type="password" id="password_1" class="inputstyle" name="password_1">
                        </div>
                        <div class="input">
                            <label for="password_2">Confirm password</label><input type="password" id="password_2" class="inputstyle" name="password_2">
                        </div>
                        <div class="input">
                            <button type="submit" class="button" name="reg_user">Register</button>
                        </div>
                    </form>
                </div>
            <?php endif;?>
    </div>
</div>