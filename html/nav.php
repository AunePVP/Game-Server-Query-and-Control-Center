<div id="nav">
      <ul>
       <li><a href="">Gameserver</a></li>
          <?php
          session_start();
          if (isset($_SESSION['username'])) {
              $username = $_SESSION['username'];
              echo '<div class="dropdown"><button class="dropbtn">'.$username.'</button><div class="dropdown-content"><a href="users/index.php">HI</a></div></div>';
          }
          ?>
      </div>