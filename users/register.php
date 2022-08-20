<?php
include_once '../html/config.php';
$title = "Registration";
session_start();
if (isset($_SESSION['username'])) {
    header('location: ../');
}
if (array_key_exists('register', $_POST)) {
    $username = trim($_POST["username"]);
    $password1 = trim($_POST["password1"]);
    $password2 = trim($_POST["password2"]);
    if (!empty($username) && !empty($password1) && !empty($password2)) {
        if (strlen($username) < 4) {
            $errors['mincharacters'] = "Please use at least 4 characters for your username.";
        }
        if (!preg_match("/^[a-zA-Z0-9_-äöüß]*$/m", $username)) {
            $errors['specialuser'] = "Special characters are not allowed in user names.";
        }
        if ($password1 !== $password2) {
            $errors['nomatch'] = "Those passwords didn't match. Try again.";
        }
        if (strlen($password1) < 4) {
            $errors['mincharacterspw'] = "Please use at least 8 characters for your password.";
        }
    } else {
        $errors['emptyline'] = "Please fill out every line.";
    }
    if (!isset($errors)) {
        $conn = mysqli_connect($DB_SERVER, $DB_USERNAME, $DB_PASSWORD, $DB_NAME);
        $sql = "SELECT * FROM users WHERE username='$username'";
        $result = $conn->query($sql);
        if (!$result->num_rows == 0) {
            $errors['userexists'] = "A user with this username already exists. Please choos another username or login <a href='login.php'>here</a>.";
        }
        if (!isset($errors)) {
            $password = hash('sha256', $password1);
            $sql = "INSERT INTO users (username, password, server, sprite, seed) VALUES('$username', '$password', '[0]', 'bottts', '$username')";
            mysqli_query($conn, $sql);
            $_SESSION['username'] = $username;
            header('location: ../');
        }
        mysqli_close($conn);
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
<div id="nav">
    <ul>
        <li><a href="../">Gameserver</a></li>
        <div style="width: 100%;"></div>
    </ul>
</div>
<div class="login-popup">
    <div class="centerdiv">
        <div class="padding15">
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" style="margin:0">
                <label for="username">Username:</label><input id="username" class="input" name="username" type="text" minlength="4" maxlength="15" placeholder="xxxxx" autocomplete="off" required="required">
                <div class="error"><?php if(isset($errors['mincharacters'])){echo $errors['mincharacters'];}elseif(isset($errors['specialuser'])){echo $errors['specialuser'];}elseif(isset($errors['userexists'])){echo $errors['userexists'];}?></div>
                <label for="password1">Password:</label><input id="password1" class="input" name="password1" type="password" minlength="8" placeholder="xxxxxxxxxxxx" autocomplete="off" required="required">
                <label for="password2">Password:</label><input id="password2" class="input" name="password2" type="password" minlength="8" placeholder="xxxxxxxxxxxx" autocomplete="off" required="required">
                <span class="error"><?php if(isset($errors['nomatch'])){echo $errors['nomatch'];}elseif(isset($errors['mincharacterspw'])){echo $errors['mincharacterspw'];}?></span>
                <div style="display:flex;justify-content: flex-end;-webkit-align-items: center;align-items: center;">
                    <span class="error"><?php if (isset ($errors['emptyline'])){echo $errors['emptyline'];} ?></span>
                    <input class="button" type="submit" name="register" value="Submit" style="display: <?php echo $displaysubmit?>">
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>

