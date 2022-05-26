<?php
// include conifg and get serverid from url
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
// Create Link for Battle Metrics API
$linkII = "https://api.battlemetrics.com/servers/" . $banner;

// Query data -> decode data -> include correct file for reading data from array
switch ($type) {
    case "arkse":
        include 'query/sourcequery.php';
        $serverstatus = json_decode($queryresult);
        $arr = json_decode($queryresult, true);
        include('html/type/arkse/index.php');
        break;
    case "minecraft":
        include 'query/minecraftquery.php';
        $serverstatus = json_decode($queryresult);
        include('html/type/minecraft/index.php');
        break;
    case "valheim":
        include 'query/sourcequery.php';
        $serverstatus = json_decode($queryresult);
        include('html/type/valheim/index.php');
        break;
    case "protocol-valve":
        include 'query/sourcequery.php';
        $serverstatus = json_decode($queryresult);
        include('html/type/protocol-valve/index.php');
        break;
    case "csgo":
        include 'query/sourcequery.php';
        $serverstatus = json_decode($queryresult);
        include('html/type/csgo/index.php');
        break;
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
                    <td title="<?php if (isset($title)) {echo $title;} ?>" class="servername_cell">
                        <div class="servername_nolink"><?php if (isset($titlename)) {echo $titlename;} ?></div></td>
                    <td class="players_cell"><div class="outer_bar"><div class="inner_bar"><span class="players_numeric"><?php echo $countplayers . '/' . $maxplayers;?></span></div></div></td>
                    <td class="img-cell"><img src="<?php echo $img; ?>" width="80px" height="80px" style="float:right;margin-right: 8px;"></img></td>
                </tr>
                </tbody></table>
        </summary>
        <?php
        // Include the tab for the game
        switch ($type) {
            case "arkse":
                include('html/tabs/tabs-ark.php');
                break;
            case "minecraft":
                include('html/tabs/tabs-minecraft.php');
                break;
            case "valheim":
                include('html/tabs/tabs-valheim.php');
                break;
            case "protocol-valve":
                include('html/tabs/tabs-valve.php');
                break;
            case "csgo":
                include('html/tabs/tabs-csgo.php');
                break;
        }
        ?>
    </details>
</section>
