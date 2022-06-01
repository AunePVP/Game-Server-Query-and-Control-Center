<?php
$Timer = MicroTime( true );
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
        $name = $row["Name"];
    }
} else {
    echo "0 results";
}
$conn->close();
// Query data -> decode data -> include correct file for reading data from array
switch ($type) {
    case "csgo":
    case "valheim":
    case "protocol-valve":
    case "arkse":
        include 'query/sourcequery.php';
        break;
    case "minecraft":
        include 'query/minecraftquery.php';
        break;
}
$queryresult = $queryresult ?? false;
$serverstatus = json_decode($queryresult);
include('html/type/'.$type.'/index.php');
// If the server is offline, it will show a red colour on the website. If its red, it will show a green colour.
if ($status == 1) {
    $statusfarbe ='background-color: #00FF17;';
} else {
    $statusfarbe ='background-color: #E20401;';
}
$Timer = Number_Format( MicroTime( true ) - $Timer, 4, '.', '' )."sec";
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
                        <div class="servername_nolink"><?php if (isset($title)) {echo $title;} ?></div></td>
                    <td class="players_cell"><div class="outer_bar"><div class="inner_bar"><span class="players_numeric"><?php echo $countplayers . '/' . $maxplayers;?></span></div></div></td>
                    <td class="img-cell"><img src="<?php echo $img; ?>" width="80px" height="80px" style="float:right;margin-right: 8px;"></td>
                </tr>
                </tbody></table>
        </summary>
        <?php
        // Include the tab for the game
        //include('html/type/'.$type.'/tabs.php');
        include('html/tabs.php');
        ?>
    </details>
</section>
