<div id="myNav" class="overlay" style="width: 0;">    <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">×</a>    <div class="overlay-content">        <button <?php if (isset($Server_selected)){echo $Server_selected;}?>onclick="window.location.href='server.php';">Overview</button>        <?php //if ($username == "admin"): ?>            <!--<button <?php if (isset($User_selected)){echo $User_selected;}?>onclick="window.location.href='user.php';">User</button>-->        <?php //endif; ?>        <button <?php if (isset($Settings_selected)){echo $Settings_selected;}?>onclick="window.location.href='settings.php';">Settings</button>        <button onclick="window.location.href='../../index.php';">Home</button>        <button class="bottom" onclick="window.open('https://github.com/AunePVP/Game-Server-Query-and-Control-Center');">Github</button>    </div></div>