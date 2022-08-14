<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once '../html/config.php';
$title = "Login";
session_start();
if (isset($_SESSION['username'])) {
    header('location: ../');
}
if (array_key_exists('login', $_POST)) {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    if (!preg_match("/^[a-zA-Z0-9_-äöüß]*$/m", $username)) {
        $error['specialuser'] = "Special characters are not allowed in user names.";
    }
    if (!isset($errors)) {
        $password = hash('sha256', $password);
        $conn = mysqli_connect($DB_SERVER, $DB_USERNAME, $DB_PASSWORD, $DB_NAME);
        $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
        $result = $conn->query($sql);
        if (mysqli_num_rows($result) == 1) {
            $_SESSION['username'] = $username;
            header('location: ../');
        } else {
            $error['nomatch'] = "Username or password doesn't match.";
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8" >
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="style.css">
    <title><?php echo $title?></title>
</head>
<body>
<div class="login-popup">
    <div class="centerdiv">
        <div class="padding15">
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" style="margin:0">
                <label for="username">Username:</label><input id="username" class="input" name="username" type="text" minlength="4" maxlength="15" placeholder="xxxxx" autocomplete="off" required="required">
                <label for="password">Password:</label><input id="password" class="input" name="password" type="password" minlength="8" placeholder="xxxxxxxxxxxx" autocomplete="off" required="required">
                <div style="display:flex;justify-content: flex-end;-webkit-align-items: center;align-items: center;">
                    <?php if (isset($error['specialuser'])){echo $error['specialuser'];}elseif (isset($error['nomatch'])){echo $error['nomatch'];}?>
                    <input class="button" type="submit" name="login" value="Submit">
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
