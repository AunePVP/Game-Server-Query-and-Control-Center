<?php
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
            header('location: ../?login=true');
        }
    }
    if (isset($error)) {
        $_SESSION['error'] = $error;
    }
}
?>