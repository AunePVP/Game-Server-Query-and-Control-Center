<?php
require_once 'html/config.php' ;
// Checks if authentication is required. If it's required, it will load the Login window except the visitor of the website is already logged in.
 if ($authentication) {
    require 'users/login-snippet.php';
}
?>
<!doctype html>
<html>
<?php include('html/head.php') ?>
<body>
<?php include('html/nav.php'); ?>
<div class="container">
    <table class="server_list_table"><tbody>
        <tr class="server_list_table_top">
            <th class="status_cell"><?php echo $language[$lang][6] ?></th>
            <th class="connectlink_cell"><?php echo $language[$lang][7] ?></th>
            <th class="servername_cell"><?php echo $language[$lang][8] ?></th>
            <th class="players_cell"><?php echo $language[$lang][4] ?></th>
            <th class="img-cell"><div></div></th>
        </tr></tbody>
    </table>
      <?php
      // Get current URL.
      if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
          $url = "https://";
      else
          $url = "http://";
      $url.= dirname($_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'])."/server.php";
      // First DB Connection
      $conn = mysqli_connect($DB_SERVER, $DB_USERNAME, $DB_PASSWORD, $DB_NAME);
      if (!$conn) {
          die("Connection failed: " . mysqli_connect_error());
      }
      if (!isset($username)) {$username = "public";};
      $sql = "SELECT ID FROM serverconfig WHERE owner='$username' AND enabled='1'";
      $result = mysqli_query($conn, $sql);
      if (mysqli_num_rows($result) > 0) {
          $serverhelper = '{"serverowner":[';
          while($row = mysqli_fetch_assoc($result)) {
              $serverid = $row["ID"];
              $serverhelper = $serverhelper.'{"sID":"'.$serverid.'"},';
          }
      } else {
          echo "0 results";
      }
      if (isset($serverhelper)) {
          $serverhelper = $serverhelper.'{"zero":"zero"}]}';
          mysqli_close($conn);
          $serverhelper = json_decode($serverhelper);
          foreach ($serverhelper->serverowner as $value) {
              $ServerID = $value->sID ?? '';
              if (!empty($ServerID)){
                  readfile("$url?serverid=$ServerID");
              }
          }
      }
      if ($username == "public") {
          echo "<div class='white-inftext'>".$language[$lang][9]."</div>";
      }
      ?>
      </div>
</body>
</html>
