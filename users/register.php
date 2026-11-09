<?php
include_once '../html/config.php';
require_once '../html/lib.php';
$title = "Registration";
gsq_session_start();
if (isset($_SESSION['username'])) {
    redirect('../');
}
// Registration can be disabled in the settings
if (empty($register)) {
    http_response_code(403);
    exit('Registration is disabled.');
}
$displaysubmit = "block";
if (array_key_exists('register', $_POST)) {
    csrf_verify();
    $username = trim((string)($_POST["username"] ?? ''));
    $password1 = (string)($_POST["password1"] ?? '');
    $password2 = (string)($_POST["password2"] ?? '');
    if ($username !== '' && $password1 !== '' && $password2 !== '') {
        if (mb_strlen($username) < 4) {
            $errors['mincharacters'] = "Please use at least 4 characters for your username.";
        } elseif (!preg_match(GSQ_USERNAME_REGEX, $username)) {
            $errors['specialuser'] = "Special characters are not allowed in user names.";
        } elseif (in_array(mb_strtolower($username), GSQ_RESERVED_USERS, true)) {
            $errors['userexists'] = "This username is reserved. Please choose another username.";
        }
        if ($password1 !== $password2) {
            $errors['nomatch'] = "Those passwords didn't match. Try again.";
        }
        if (strlen($password1) < 8) {
            $errors['mincharacterspw'] = "Please use at least 8 characters for your password.";
        }
    } else {
        $errors['emptyline'] = "Please fill out every line.";
    }
    if (!isset($errors)) {
        ensure_schema();
        if (db_row("SELECT id FROM users WHERE username = ?", [$username])) {
            $errors['userexists'] = "A user with this username already exists. Please choose another username or login <a href='../?login=true'>here</a>.";
        }
        if (!isset($errors)) {
            try {
                db_query("INSERT INTO users (username, password, server, sprite, seed) VALUES (?, ?, '[0]', 'bottts', ?)", [$username, gsq_password_hash($password1), $username]);
            } catch (mysqli_sql_exception $e) {
                // Unique index hit by a concurrent registration
                $errors['userexists'] = "A user with this username already exists. Please choose another username or login <a href='../?login=true'>here</a>.";
            }
            if (!isset($errors)) {
                session_regenerate_id(true);
                $_SESSION['username'] = $username;
                unset($_SESSION['csrf_token']);
                redirect('../');
            }
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
                <?php echo csrf_field() ?>
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

