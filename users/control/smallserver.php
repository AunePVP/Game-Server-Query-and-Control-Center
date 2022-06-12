<?php
/*<!-- _------------_Database Connection_------------_ -->*/
$conn = mysqli_connect($DB_SERVER, $DB_USERNAME, $DB_PASSWORD, $DB_NAME);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
/*<!-- _------------_Set SQL Command_------------_ -->*/
if ($username == "admin") {
    // If user is admin, get all servers
    $sql = "SELECT * FROM serverconfig";
} else {
    // If user is not admin, get the premitted servers and set the sql command
    $sql = "SELECT server FROM users WHERE username='$username'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $serverjson = json_decode($row["server"]);
            foreach ($serverjson as $ServerID) {
                if (isset($serverids)) {
                    $serverids .= ", ";
                    $serverids .= $ServerID;
                }
                else $serverids = $ServerID;
            }
        }
    } else {
        echo "Query failed! Talk to your server administrator.";
    }
    $sql = "SELECT * FROM serverconfig WHERE ID IN ($serverids)";
}
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        unset($queryresult);
        $ServerID = $row["ID"];
        $ip = $row["IP"];
        $type = $row["type"];
        $qport = $row["QueryPort"];
        $gport = $row["GamePort"];
        $rport = $row["RconPort"];
        $name = $row["Name"];
        $enabled = $row["enabled"];
        switch ($type) {
            case "csgo":
            case "valheim":
            case "protocol-valve":
            case "arkse":
            case "vrising":
            case "rust":
                include '../../query/sourcequery.php';
                break;
            case "minecraft":
                include '../../query/minecraftquery.php';
                break;
        }
        $serverstatus = json_decode($queryresult ?? false);
        include '../../html/type/query.php';
        // Correct image link if necessary
        $imgcheck = $rest = substr($img, 0 ,5);
        if ($imgcheck == "html/") {
            $img = "../../$img";
        }
        // Set color red, if server is offline and green, if server is online.
        if ($status){
            $status = "online";
        } else {
            $status = "offline";}
        echo "<div class='serversnippet flex' ".'onclick="'."location.href='server.php?id=$ServerID';".'"'."><div class='status $status'></div><div class='content'><div class='name'>$title</div><div class='logo flex'><img src='$img' width='85px' height='85px'></div><div class='player'>$countplayers/$maxplayers</div></div></div>";
    }
} else {
    echo "0 results";
}
mysqli_close($conn);
?>