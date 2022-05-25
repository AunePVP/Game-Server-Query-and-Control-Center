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
    $queryresult["info"]["Game"] = $type;
    $queryresult["info"]["IP"] = $ip;
    $queryresult["info"]["Port"] = $qport;
    switch ($type) {
        case "arkse":
            $queryresult["players"] = $Query->GetPlayers();
            $queryresult["rules"] = $Query->GetRules();
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