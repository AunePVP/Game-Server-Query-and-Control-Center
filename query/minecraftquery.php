<?php
use xPaw\MinecraftPing;
use xPaw\MinecraftPingException;
use xPaw\MinecraftQuery;
use xPaw\MinecraftQueryException;
if ($qport == 0) {
    require __DIR__ . '/minecraft/src/MinecraftPing.php';
    require __DIR__ . '/minecraft/src/MinecraftPingException.php';
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
}

else {
    require __DIR__ . '/minecraft/src/MinecraftQuery.php';
    require __DIR__ . '/minecraft/src/MinecraftQueryException.php';
    $Query = new MinecraftQuery();
    try {
        $Query->Connect($ip, $qport);

        $queryresult["info"] = ($Query->GetInfo());
        $queryresult["players"] = ($Query->GetPlayers());
        $queryresult = $queryresult;
        $queryresult = json_encode($queryresult);
    } catch (MinecraftQueryException $e) {
        echo $e->getMessage();
    }
}
?>