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
        case "rust":
            $queryresult["players"] = $Query->GetPlayers();
            $queryresult["rules"] = $Query->GetRules();
            break;
            // Why do I isolate csfo from the other servers? Because CSGO doesn't always respond to a A2S_RULES query.
        case "csgo":
            $lastplayerline = json_decode(tailCustom("query/cron/$ServerID.json", 1));
            // I check with the cronjob if the server accepts A2S_Rules Requests.
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
}
finally
{
    $Query->Disconnect( );
}
?>