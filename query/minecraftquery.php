<?php
require __DIR__ . '/minecraft/src/MinecraftPing.php';
require __DIR__ . '/minecraft/src/MinecraftPingException.php';

use xPaw\MinecraftPing;
use xPaw\MinecraftPingException;

try
{
    $Query = new MinecraftPing( $ip, $gport );

    $queryresult = $Query->Query();
    $queryresult = json_encode($queryresult);
}
catch( MinecraftPingException $e )
{
    echo $e->getMessage();
}
finally
{
    if( $Query )
    {
        $Query->Close();
    }
}
?>