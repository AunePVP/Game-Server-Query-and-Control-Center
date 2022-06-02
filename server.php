<?php
// include conifg and get serverid from url
require 'html/config.php';
$ServerID = $_GET['serverid'];
function tailCustom($filepath, $lines = 1, $adaptive = true)
{
    $f = @fopen($filepath, "rb");
    if ($f === false) return false;
    if (!$adaptive) $buffer = 4096;
    else $buffer = ($lines < 2 ? 64 : ($lines < 10 ? 512 : 4096));
    fseek($f, -1, SEEK_END);
    if (fread($f, 1) != "\n") $lines -= 1;
    $output = '';
    $chunk = '';
    while (ftell($f) > 0 && $lines >= 0) {
        $seek = min(ftell($f), $buffer);
        fseek($f, -$seek, SEEK_CUR);
        $output = ($chunk = fread($f, $seek)) . $output;
        fseek($f, -mb_strlen($chunk, '8bit'), SEEK_CUR);
        $lines -= substr_count($chunk, "\n");
    }
    while ($lines++ < 0) {
        $output= substr($output, strpos($output, "\n") + 1);
    }
    fclose($f);
    return trim($output);
}
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
//include('html/type/'.$type.'/index.php');
include 'html/type/query.php';
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
                        <div class="servername_nolink"><?php if (isset($title)) {echo $title;} ?></div></td>
                    <td class="players_cell"><div class="outer_bar"><div class="inner_bar"><span class="players_numeric"><?php echo $countplayers . '/' . $maxplayers;?></span></div></div></td>
                    <td class="img-cell"><img src="<?php echo $img ?>" width="80px" height="80px" style="float:right;margin-right: 8px;" alt="<?php echo $img ?>"></td>
                </tr>
                </tbody></table>
        </summary>
        <?php
        include('html/tabs.php');
        ?>
    </details>
</section>
