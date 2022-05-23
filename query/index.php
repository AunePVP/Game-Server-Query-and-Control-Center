<?php
require __DIR__ . '/SourceQuery/bootstrap.php';

use xPaw\SourceQuery\SourceQuery;

// For the sake of this example
header( 'Content-Type: text/plain' );
header( 'X-Content-Type-Options: nosniff' );

// Edit this ->

$ip = $_POST['ip'];
$port = $_POST['port'];
define( 'SQ_TIMEOUT',     1 );
define( 'SQ_ENGINE',      SourceQuery::SOURCE );
// Edit this <-
$Query = new SourceQuery( );

try
{
    $Query->Connect( $ip, $port, SQ_TIMEOUT, SQ_ENGINE );

    $data["info"] = $Query->GetInfo();
    $data["info"]["Game"] = $game;
    $data["info"]["IP"] = $ip;
    $data["info"]["Port"] = $port;
    $data["players"] = $Query->GetPlayers();
    $data["rules"] = $Query->GetRules();

    $data = json_encode($data);
    echo $data;
}
catch( Exception $e )
{
    echo $e->getMessage( );
}
finally
{
    $Query->Disconnect( );
}
