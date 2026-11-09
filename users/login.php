<?php
include_once '../html/config.php';
require_once '../html/lib.php';
$title = "Login";
gsq_session_start();
if (isset($_SESSION['username'])) {
    redirect('../');
}
if (array_key_exists('login', $_POST)) {
    csrf_verify();
    $username = trim((string)($_POST["username"] ?? ''));
    $password = (string)($_POST["password"] ?? '');
    $error = [];
    // Prepared statements make the query safe; the strict name rules are enforced at registration.
    // Only reject obviously invalid input here so older accounts can still log in.
    if ($username === '' || mb_strlen($username) > 100) {
        $error['specialuser'] = "Please enter a valid user name.";
    }
    if (!$error) {
        ensure_schema();
        $loggedIn = false;
        foreach (db_query("SELECT id, password FROM users WHERE username = ?", [$username]) as $user) {
            if (gsq_password_verify($password, $user['password'], $needsRehash)) {
                if ($needsRehash) {
                    // Upgrade legacy sha256 hashes to bcrypt
                    db_query("UPDATE users SET password = ? WHERE id = ?", [gsq_password_hash($password), (int)$user['id']]);
                }
                $loggedIn = true;
                break;
            }
        }
        if ($loggedIn) {
            session_regenerate_id(true);
            $_SESSION['username'] = $username;
            unset($_SESSION['csrf_token']);
            redirect('../');
        }
        $error['nomatch'] = "Username or password doesn't match.";
    }
    $_SESSION['error'] = $error;
    redirect('../?login=true');
}
redirect('../?login=true');
