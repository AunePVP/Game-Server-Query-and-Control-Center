<?php include('connection.php') ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>
        Login and Registration
        System - LAMP Stack
    </title>

    <link rel="stylesheet" type="text/css"
          href="style.css">
</head>
<body>
<div class="header">
    <h2>Login Here!</h2>
</div>

<form method="post" action="login.php">

    <?php include('error.php'); ?>

    <div class="input-group">
        <label>Enter Username</label>
        <input type="text" name="username" >
    </div>
    <div class="input-group">
        <label>Enter Password</label>
        <input type="password" name="password">
    </div>
    <div class="input-group">
        <button type="submit" class="btn"
                name="login_user">
            Login
        </button>
    </div>



    <p>
        New Here?
        <a href="register.php">
            Click here to register!
        </a>
    </p>



</form>
</body>

</html>