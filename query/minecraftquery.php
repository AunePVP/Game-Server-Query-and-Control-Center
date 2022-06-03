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
        $queryresult = json_encode($queryresult);
        echo $queryresult;
        #$queryresult =  ('{"info":{"HostName":"?4Linux?aGSM?3 von ?eLeo","GameType":"SMP","GameName":"MINECRAFT","Version":"1.18.2","Plugins":"","Map":"world","Players":0,"MaxPlayers":20,"HostPort":25565,"HostIp":"193.23.126.118","Software":"Vanilla"},"players":false}');
    } catch (MinecraftQueryException $e) {
        echo $e->getMessage();
    }
}
?>