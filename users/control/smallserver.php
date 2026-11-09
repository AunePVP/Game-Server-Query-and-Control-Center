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
        $ServerID = (int)$row["ID"];
        $ip = $row["IP"];
        $type = $row["type"];
        $qport = $row["QueryPort"];
        $gport = $row["GamePort"];
        $name = $row["Name"];
        include '../../query/runquery.php';
        // Set color red, if server is offline and green, if server is online.
        if ($status){
            $status = "online";
        } else {
            $status = "offline";}
        echo "<div class='serversnippet flex' ".'onclick="'."location.href='server.php?id=".(int)$ServerID."';".'"'."><div class='status $status'></div><div class='content'><div class='name'>".h($title)."</div><div class='logo flex'><img src='".h($img)."' width='85px' height='85px'></div><div class='player'>".h($countplayers)."/".h($maxplayers)."</div></div></div>";
    }
}
?>