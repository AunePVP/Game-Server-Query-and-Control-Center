<?php include('connection.php') ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
        body{ font: 14px sans-serif; height: 100vh; display: flex;}
        .wrapper{ width: 360px; padding: 20px; margin: auto; }
    </style>
</head>
<body>
<div class="wrapper">
    <h2>Login</h2>

    <form method="post" action="login.php">

        <?php include('error.php'); ?>

        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" class="form-control">
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control">
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-primary"
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
</div>
</body>

</html>