<?php
$Timer = MicroTime(true);
require 'query/SourceQuery/bootstrap.php';
require 'query/minecraft/src/MinecraftPing.php';
require 'query/minecraft/src/MinecraftPingException.php';
require 'query/minecraft/src/MinecraftQuery.php';
require 'query/minecraft/src/MinecraftQueryException.php';
require 'html/type/minecraft/jsonconversion.php';
require 'html/type/minecraft/minecraftcolor.php';
require_once 'html/config.php';
if (file_exists("query/cron/cache/minecraft.php")) {
    include 'query/cron/cache/minecraft.php';
}

use xPaw\SourceQuery\SourceQuery;

const SQ_TIMEOUT = 1;
const SQ_ENGINE = SourceQuery::SOURCE;
function minecraftcache($username) {
    global $mcuuid;

    if (!isset($mcuuid[$username])) {
        $data = json_decode(file_get_contents("https://api.mojang.com/users/profiles/minecraft/$username"));
        $uuid = $data->id;
        $data = '$mcuuid[\''.$username.'\'] = "'. $uuid .'";';
        file_put_contents("query/cron/cache/minecraft.php", $data . "\n", FILE_APPEND);
        return $uuid;
    } else {
        return $mcuuid[$username];
    }

}
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
        $output = substr($output, strpos($output, "\n") + 1);
    }
    fclose($f);
    return trim($output);
}

function convertos($Os)
{
    $Opers = array(
        'l' => 'Linux',
        'w' => 'Windows',
        'm' => 'Mac'
    );
    return $Opers[$Os];
}

?>
    <!doctype html>
    <html lang="<?php
    echo $lang ?>">
    <?php
    include('html/head.php') ?>
    <body>
    <?php
    include('html/nav.php'); ?>
    <div class="container">
        <table class="server_list_table">
            <tbody>
            <tr class="server_list_table_top">
                <th class="status_cell"><?php
                    echo $language[$lang][6] ?></th>
                <th class="connectlink_cell"><?php
                    echo $language[$lang][7] ?></th>
                <th class="servername_cell"><?php
                    echo $language[$lang][8] ?></th>
                <th class="players_cell"><?php
                    echo $language[$lang][4] ?></th>
                <th class="img-cell">
                    <div></div>
                </th>
            </tr>
            </tbody>
        </table>
        <?php
        //$url.= dirname($_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'])."/server.php";
        // First DB Connection
        $conn = mysqli_connect($DB_SERVER, $DB_USERNAME, $DB_PASSWORD, $DB_NAME);
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }
        if (!isset($username)) {
            $username = "public";
        }
        $sql = "SELECT server FROM users WHERE username='$username'";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $serverjson = json_decode($row["server"]);
            }
        } else {
            echo "Query failed! Talk to your server administrator.";
        }
        //mysqli_close($conn);
        if (isset($serverjson)) {
            foreach ($serverjson as $ServerID) :
                unset($queryresult);
                if (!empty($ServerID)) {
                    //$conn = mysqli_connect($DB_SERVER, $DB_USERNAME, $DB_PASSWORD, $DB_NAME);
                    if (!$conn) {
                        die("Connection failed: " . mysqli_connect_error());
                    }
                    $sql = "SELECT * FROM serverconfig WHERE ID='$ServerID'";
                    $result = mysqli_query($conn, $sql);
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $ip = $row["IP"];
                            $type = $row["type"];
                            $qport = $row["QueryPort"];
                            $gport = $row["GamePort"];
                            $rport = $row["RconPort"];
                            $name = $row["Name"];
                            $enabled = $row["enabled"];
                        }
                    } else {
                        echo "0 results";
                    }
                }
                if ($enabled == 1) {
                    require 'server.php';
                }
            endforeach;
            $conn->close();
        }
        if ($username == "public") {
            echo "<div class='white-inftext'>" . $language[$lang][9] . "</div>";
        }
        ?>
    </div>
    </body>
    </html>
<?php
echo "<div class='querytime'>Queried in " . Number_Format(MicroTime(true) - $Timer, 4, '.', '') . " seconds.</div>";
?>