
<?php
require 'html/config.php';
// Saves the required Information from the URL into variables e.g. 'server/server.php?type=minecraft&ip=ms.vortexnetwork.net&qport=25565'
$type = $_GET['type'];
$ip = $_GET['ip'];
$qport = $_GET['qport'];
$gport = $_GET['gport'];
$banner = $_GET['banner'];

// It created a link out of the variables. This link is used to query the information.
$link = $domain . $path . "/html/server.php?type=" . $type . "&ip=" . $ip . "&qport=" . $qport;
$linkII = "https://api.battlemetrics.com/servers/" . $banner;
// The compound link is queried and made usable with "json_decode".
$serverstatus = json_decode(file_get_contents($link));
$battlemetrics_serverstatus = json_decode(file_get_contents($linkII));
// With this I can retrieve the values from the decoded text and store them in a variable.
// However, this is done in a different file in the next step, because the data of different players are always queried differently.
    # $countplayers = $serverstatus->raw->vanilla->raw->players->online;

// The appropriate file for the query is specified here.
if ($type == "minecraft") {
  include('html/type/minecraft/index.php');
} elseif ($type == "arkse") {
  include('html/type/arkse/index.php');
} elseif ($type == "protocol-valve") {
  include('html/type/protocol-valve/index.php');
} elseif ($type == "valheim") {
  include('html/type/valheim/index.php');
} elseif ($type == "csgo") {
  include('html/type/csgo/index.php');
} else {
  echo "Nichts gefunden";
}
// If the server is offline, it will show a red colour on the website. If its red, it will show a green colour.
if ($status == 1) {
  $statusfarbe ='background-color: #00FF17;';
} else {
  $statusfarbe ='background-color: #E20401;';
}

?>
<section>
          <details>
            <summary>
            <table id="server_list_table">
                <tbody>
                    <tr class="server_onl">
                    <td class="status_cell">
                        <span class="status_icon_onl" style="<?php echo $statusfarbe ?>"></span>
                        <div class="status-letter-online"><?php
                        if ($status == "1") {
                                  echo "ONLINE";
                                  }
                                else {
                                 echo "OFFLINE";
                                }?>
                            </div>
                        <div class="status-letter-offline">OFFLINE</div>
                    </td>
                    <td title="GAME LINK" class="connectlink_cell"><a href="<?php echo $connectlink ?>"><?php echo $ip . ":" . $gport ?></a></td>
                    <td title="<?php echo $title; ?>" class="servername_cell">
                    <div class="servername_nolink"><?php echo $title; ?></div></td>
                    <td class="players_cell"><div class="outer_bar"><div class="inner_bar"><span class="players_numeric"><?php echo $countplayers . '/' . $maxplayers;?></span></div></div></td>
                    <td class="img-cell"><img src="<?php echo $img; ?>" width="80px" height="80px" style="float:right;margin-right: 8px;"></img></td>
                </tr>
              </tbody></table>
            </summary>
            <?php
// The correct menue for every game is specified here.
if ($type == "minecraft") {
  include('html/tabs/tabs-minecraft.php');
} elseif ($type == "arkse") {
  include('html/tabs/tabs-ark.php');
} elseif ($type == "protocol-valve") {
  include('html/tabs/tabs-valve.php');
} elseif ($type == "valheim") {
  include('html/tabs/tabs-valheim.php');
} elseif ($type == "csgo") {
  include('html/tabs/tabs-csgo.php');
} else {
  echo "Nichts gefunden";
}
?>
          </details>
        </section>
