<?php
session_start();
if (!isset($_SESSION['username'])) {
    $_SESSION['msg'] = "You have to log in first";
    header('location: login.php');
}
if ($_SESSION['username'] != "admin") {
    header("location: ../login.php");
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="style.css">
    <script src="script.js"></script>
    <title>Admin panel</title>
</head>
<body>
<nav>
    <div id="sidebar">
        <button class="selected" onclick="window.location.href='index.php';">Overview</button>
        <button onclick="window.location.href='server.php';">Server</button>
        <button onclick="window.location.href='user.php';">User</button>
        <button onclick="window.location.href='settings.php';">Settings</button>
        <button class="bottom" onclick="window.open('https://github.com/AunePVP/Game-Server-Query-and-Control-Center');">Github</button>
    </div>
    <div id="topbar">
        <span id="open-span" onclick="openNav()">☰</span>
    </div>
</nav>
<main>
    <div id="myNav" class="overlay" style="width: 0;">
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">×</a>
        <div class="overlay-content">
            <button class="selected" onclick="window.location.href='index.php';">Overview</button>
            <button onclick="window.location.href='server.php';">Server</button>
            <button onclick="window.location.href='user.php';">User</button>
            <button onclick="window.location.href='settings.php';">Settings</button>
            <button class="bottom" onclick="window.open('https://github.com/AunePVP/Game-Server-Query-and-Control-Center');">Github</button>
        </div>
    </div>
    <div class="server inlineflex flex-wrap">
        <div class="serversnippet"></div>
        <div class="serversnippet"></div>
        <div class="serversnippet"></div>
        <div class="serversnippet"></div>
    </div>
</main>
</body>
</html>