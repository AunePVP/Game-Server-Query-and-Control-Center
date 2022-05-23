<?php
require 'html/config.php';
$ServerID = $_GET['serverid'];
// Query DB for Server Data.
$conn = mysqli_connect($DB_SERVER, $DB_USERNAME, $DB_PASSWORD, $DB_NAME);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
$sql = "SELECT * FROM serverconfig WHERE ID='$ServerID'";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        $ip = $row["IP"];
        $type = $row["type"];
        $qport = $row["QueryPort"];
        $gport = $row["GamePort"];
        $rport = $row["RconPort"];
        $banner = $row["BatPort"];
    }
} else {
    echo "0 results";
}
$conn->close();
// It created a link out of the variables. This link is used to query the information.
$linkII = "https://api.battlemetrics.com/servers/" . $banner;
// The compound link is queried and made usable with "json_decode".
//Get Source Query DATA
if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
    $url = "https://";
else
    $url = "http://";

$url.= $_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']);
$url.= "/query/index.php";
if ($type == "arkse") {
    $postdata = http_build_query(
        array(
            'ip' => $ip,
            'port' => $qport,
        )
    );
    $opts = array('http' =>
        array(
            'method' => 'POST',
            'header' => 'Content-Type: application/x-www-form-urlencoded',
            'content' => $postdata
        )
    );
    $context = stream_context_create($opts);
    $queryresult = file_get_contents($url, false, $context);
}
if ($type == "minecraft") {
    $link = "https://api.mcsrvstat.us/2/".$ip.":".$qport;
    $queryresult = file_get_contents($link);
}
//include 'html/server.php';
$serverstatus = json_decode($queryresult);
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
    echo "<br>Nichts gefunden";
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
            <table class="server_list_table">
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
                        <div class="servername_nolink"><?php echo $titlename; ?></div></td>
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
