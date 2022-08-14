<?php
use xPaw\MinecraftPing;
use xPaw\MinecraftPingException;
use xPaw\MinecraftQuery;
use xPaw\MinecraftQueryException;
if ($qport == 0) {
    try {
        $Query = new MinecraftPing($ip, $gport);

        $queryresult = $Query->Query();
        $queryresult = json_encode($queryresult);
    } catch (MinecraftPingException $e) {
        echo $e->getMessage();
    } finally {
        if ($Query) {
            $Query->Close();
        }
    }
} else {
    $Query = new MinecraftQuery();
    try {
        $Query->Connect($ip, $qport);

        $queryresult["info"] = ($Query->GetInfo());
        $queryresult["players"] = ($Query->GetPlayers());
        $queryresult = json_encode($queryresult);
    } catch (MinecraftQueryException $e) {
    }
}
?>
