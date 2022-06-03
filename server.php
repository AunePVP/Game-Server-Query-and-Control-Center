<?php
unset($Os, $img, $version, $password, $title, $motd, $connectlink, $map);
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
$serverstatus = json_decode($queryresult ?? false);
require 'html/type/query.php';
// Set color if server is offline or online
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