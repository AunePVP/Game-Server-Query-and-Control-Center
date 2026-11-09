<?php
/*<!-- _------------_Get the servers of this account_------------_ -->*/
if (is_admin($username)) {
    // If user is admin, get all servers
    $serverrows = db_query("SELECT * FROM serverconfig");
} else {
    // If user is not admin, get only the permitted servers
    $serverids = user_server_ids($username);
    $serverrows = [];
    if ($serverids) {
        $placeholders = implode(',', array_fill(0, count($serverids), '?'));
        $serverrows = db_query("SELECT * FROM serverconfig WHERE ID IN ($placeholders)", $serverids);
    }
}
if ($serverrows) {
    foreach ($serverrows as $row) {
        $queryresult = null;
        $ServerID = $row["ID"];
        $ip = $row["IP"];
        $type = $row["type"];
        $qport = $row["QueryPort"];
        $gport = $row["GamePort"];
        $rport = $row["RconPort"];
        $name = $row["Name"];
        switch ($type) {
            case "csgo":
            case "valheim":
            case "protocol-valve":
            case "arkse":
            case "dayz":
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
        echo "<div class='serversnippet flex' ".'onclick="'."location.href='server.php?id=".(int)$ServerID."';".'"'."><div class='status $status'></div><div class='content'><div class='name'>".h($title)."</div><div class='logo flex'><img src='".h($img)."' width='85px' height='85px'></div><div class='player'>".h($countplayers)."/".h($maxplayers)."</div></div></div>";
    }
}
?>