<div id="nav">
      <ul>
          <li><a href="">Gameserver</a></li>
          <div style="width: 100%;"></div>
          <?php
          session_start();
          session_write_close();
          if (isset($_SESSION['username'])) {
              $username = $_SESSION['username'];
              echo '<div class="dropdown" style="height: 58px;">';
              echo '<a href="users/control/server.php"><img class="user-avater" src="https://avatars.akamai.steamstatic.com/ba20937c5bba10858664e3f32f7d2cc9e96a5275_full.jpg">';
              echo '</a><div class="dropdown-content"><a href="users/control/server.php">More</a><a href="users/logout.php">Logout</a></div></div>';
          } else {
              echo '<div class="login" style="cursor: pointer;"><a class="button white-hover" onclick="popuplogin()">Login</a><div class="dropdown-content"></div></div>';
          }
          ?>
      </ul>
</div>