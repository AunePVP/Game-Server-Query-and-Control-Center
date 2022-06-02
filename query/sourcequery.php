<?php
require __DIR__ . '/SourceQuery/bootstrap.php';
use xPaw\SourceQuery\SourceQuery;
// For the sake of this example
header( 'X-Content-Type-Options: nosniff' );

// Edit this ->
define( 'SQ_TIMEOUT',     1 );
define( 'SQ_ENGINE',      SourceQuery::SOURCE );
// Edit this <-
$Query = new SourceQuery( );

try
{
    $Query->Connect( $ip, $qport, SQ_TIMEOUT, SQ_ENGINE );
    $queryresult["info"] = $Query->GetInfo();
    switch ($type) {
        case "arkse":
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