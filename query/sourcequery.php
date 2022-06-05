<?php
use xPaw\SourceQuery\SourceQuery;
$Query = new SourceQuery( );
try
{
    $Query->Connect( $ip, $qport, SQ_TIMEOUT, SQ_ENGINE );
    $queryresult["info"] = $Query->GetInfo();
    switch ($type) {
        case "arkse":
        case "vrising":
            $queryresult["players"] = $Query->GetPlayers();
            $queryresult["rules"] = $Query->GetRules();
            break;
        case "csgo":
            $lastplayerline = json_decode(tailCustom("query/cron/$ServerID.json", 1));
            $csgorules = $lastplayerline->csgorules ?? "0";
            $queryresult["players"] = $Query->GetPlayers();
            if ($csgorules == "1") {
                $queryresult["rules"] = $Query->GetRules();
            }
            break;
        case "valheim":
            break;
    }
    $queryresult = json_encode($queryresult);
}
catch( Exception $e )
{
    echo $e->getMessage( );
}
finally
{
    $Query->Disconnect( );
}
?>