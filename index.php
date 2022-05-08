<?php include('html/config.php'); ?>

<?php
// Checks if authentication is required. If it's required, it will load the Login window except the visitor of the website is already logged in.
 if ($authentication == TRUE) {
    require 'users/login-snippet.php';
}
?>
<!doctype html>
<html>
<head>
<?php include('html/head.php') ?>
</head>

<body>
<?php include('html/nav.php'); ?>
<div class="container">
      <?php
      include('html/elements.php');
      // banner ist die Servernummer bei Battlemetrics. Falls der Server nciht bie Battlemetrics Registriert ist kan$      readfile("$url.php?type=$typeI&ip=$ipI&qport=$qportI&gport=$gportI&rport=$rportI&banner=$bannerI");
      // If you want to add more than one Server, you can add more lines.
      readfile("$url?query=1&type=$typeI&ip=$ipI&qport=$qportI&gport=$gportI&rport=$rportI&banner=$bannerI");
      readfile("$url?query=2&type=$typeII&ip=$ipII&qport=$qportII&gport=$gportII&rport=$rportII&banner=$bannerII");
      ?>
      </div>
      <?php include 'html/footer.php';?>

</body>
<footer>

</footer>
</html>
