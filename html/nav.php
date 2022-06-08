<div id="nav">
      <ul>
          <li><a href="">Gameserver</a></li>
          <div style="width: 100%;"></div>
          <?php
          session_start();
          if (isset($_SESSION['username'])) {
              $username = $_SESSION['username'];
              echo '<div style="height: 58px;"><a href="users/control/index.php"><img class="user-avater" src="https://avatars.akamai.steamstatic.com/ba20937c5bba10858664e3f32f7d2cc9e96a5275_full.jpg"></a></div>';
          }else {
              $_SESSION['backURI'] = $_SERVER['REQUEST_URI'];
              echo '<div class="login"><a class="button white-hover" href='."'users/control/index.php'".'">Login</a><div class="dropdown-content"></div></div>';
          }
          ?>
      </div>