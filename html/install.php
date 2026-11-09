<?php
require_once __DIR__ . '/lib.php';
if (is_file(GSQ_CONFIG_FILE)) {
    require_once GSQ_CONFIG_FILE;
}
gsq_session_start();

/*
 * Installation steps:
 *  1. "db"    – config.php has not been written by the installer yet ($install is not set)
 *  2. "admin" – the database is configured, but no admin user exists yet
 *  done       – both steps are finished: the installer is locked
 */
$step = isset($install) ? 'admin' : 'db';
if ($step === 'admin') {
    try {
        $adminExists = (bool)db_row("SELECT id FROM users WHERE username = ?", [GSQ_ADMIN_USER]);
    } catch (mysqli_sql_exception $e) {
        http_response_code(500);
        exit('Database error. Please check html/config.php.');
    }
    if ($adminExists) {
        redirect('../');
    }
}
csrf_verify();

$displaysubmit = "none";
if ($step === 'db') {
    if (array_key_exists('validate', $_POST) || array_key_exists('submit', $_POST)) {
        $DB_SERVER = test_input($_POST["input-ip"] ?? '');
        $DB_USERNAME = test_input($_POST["input-username"] ?? '');
        $DB_PASSWORD = (string)($_POST["input-password"] ?? '');
        $DB_NAME = test_input($_POST["input-dbname"] ?? '');
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        try {
            $conn = new mysqli($DB_SERVER, $DB_USERNAME, $DB_PASSWORD, $DB_NAME);
            $sqlvalidatemessage = "Connection successful!";
            $displaysubmit = "block";
        } catch (mysqli_sql_exception $e) {
            $conn = null;
            $sqlvalidatemessage = "Connection failed!";
        }
        // Write the data into the config file and create the tables
        if ($conn && array_key_exists('submit', $_POST)) {
            $conn->query("CREATE TABLE IF NOT EXISTS `serverconfig` (
            `ID` int NOT NULL,
            `IP` varchar(60) NOT NULL,
            `type` text NOT NULL,
            `QueryPort` int NOT NULL,
            `GamePort` int NOT NULL,
            `RconPort` int NOT NULL,
            `Name` text NOT NULL,
            `controlserver` JSON NOT NULL
            )");
            $conn->query("CREATE TABLE IF NOT EXISTS `users` (
            `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `username` varchar(100) NOT NULL UNIQUE,
            `password` varchar(100) NOT NULL,
            `server` JSON NOT NULL,
            `sprite` varchar(25) NOT NULL,
            `seed` varchar(100) NOT NULL
            )");
            $conn->close();
            if (!write_config([
                'install' => 0,
                'DB_SERVER' => $DB_SERVER,
                'DB_USERNAME' => $DB_USERNAME,
                'DB_PASSWORD' => $DB_PASSWORD,
                'DB_NAME' => $DB_NAME,
                'register' => true,
            ])) {
                exit("Unable to write html/config.php!");
            }
            $step = 'admin';
        } elseif ($conn) {
            $conn->close();
        }
    }
}
$errors = array();
$created = false;
if ($step === 'admin' && isset($_POST['reg_user'])) {
    // The admin user name is fixed. Never trust the (read-only) form field.
    $password_1 = (string)($_POST['password_1'] ?? '');
    $password_2 = (string)($_POST['password_2'] ?? '');
    if (strlen($password_1) < 8) {
        $errors[] = "Please use at least 8 characters for the password";
    }
    if ($password_1 !== $password_2) {
        $errors[] = "The two passwords do not match";
    }
    if (count($errors) == 0) {
        ensure_schema();
        db_query("INSERT INTO users (username, password, server, sprite, seed) VALUES (?, ?, '[0]', 'bottts', ?)", [GSQ_ADMIN_USER, gsq_password_hash($password_1), GSQ_ADMIN_USER]);
        session_regenerate_id(true);
        $_SESSION['username'] = GSQ_ADMIN_USER;
        unset($_SESSION['csrf_token']);
        $created = true;
    }
}
?>
<style>
    .adddbdiv {
        margin: auto;
        width: 500px;
        /*height: 500px;*/
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
        margin: 0 0 15px;
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
</style>
<div class="adddbdiv">
    <div class="padding25">
        <?php if ($step === 'db'):?>
        <form method="post" action="<?php echo h($_SERVER["PHP_SELF"]);?>" style="margin:0">
            <?php echo csrf_field() ?>
            <p class="cAx">Database</p>
            <div class="input"><label for="input-ip">IP:</label><input id="input-ip" class="inputstyle" name="input-ip" type="text" minlength="3" maxlength="40" required="required" placeholder="xxx.xxx.xxx.xx" autocomplete="off" value="<?php echo h($DB_SERVER ?? '')?>"></div>
            <div class="input"><label for="input-username">Username:</label><input id="input-username" class="inputstyle" name="input-username" type="text" minlength="2" maxlength="15" placeholder="xxxxx" autocomplete="off" value="<?php echo h($DB_USERNAME ?? '')?>"></div>
            <div class="input"><label for="input-password">Password:</label><input id="input-password" class="inputstyle" name="input-password" type="password" minlength="3" placeholder="xxxxxxxxxxxx" autocomplete="off" value="<?php echo h($DB_PASSWORD ?? '')?>"></div>
            <div class="input"><label for="input-dbname">DB Name:</label><input id="input-dbname" class="inputstyle" name="input-dbname" type="text" minlength="3" placeholder="xxxxxxx" autocomplete="off" value="<?php echo h($DB_NAME ?? '')?>"></div>
            <div style="display:flex;justify-content: flex-end;-webkit-align-items: center;align-items: center;">
                <?php if (isset($sqlvalidatemessage)) {echo h($sqlvalidatemessage);}?>
                <input class="button" type="submit" name="validate" value="Test connection">
                <input class="button" type="submit" name="submit" value="Submit" style="display: <?php echo $displaysubmit?>">
            </div>
        </form>
        <?php elseif ($created):?>
            <p>You created the user <?php echo h(GSQ_ADMIN_USER)?>!</p>
            <script>setTimeout(function(){window.location.href = '../users/control/server.php';}, 1000)</script>
        <?php else:?>
            <div style="margin: auto;">
                <p class="cAx">Create Admin User</p>
                <?php foreach ($errors as $err) {echo '<p>' . h($err) . '</p>';}?>
                <form method="post" action="<?php echo h($_SERVER["PHP_SELF"]);?>" style="margin:0">
                    <?php echo csrf_field() ?>
                    <div class="input">
                        <label for="username">Username:</label><input type="text" class="inputstyle" readonly="readonly" name="username" id="username" value="<?php echo h(GSQ_ADMIN_USER)?>">
                    </div>
                    <div class="input">
                        <label for="password_1">Password:</label><input type="password" id="password_1" class="inputstyle" name="password_1" minlength="8" required="required">
                    </div>
                    <div class="input">
                        <label for="password_2">Confirm password:</label><input type="password" id="password_2" class="inputstyle" name="password_2" minlength="8" required="required">
                    </div>
                    <div style="display: flex;justify-content: flex-end;">
                        <button type="submit" class="button" name="reg_user">Register</button>
                    </div>
                </form>
            </div>
        <?php endif;?>
    </div>
</div>
