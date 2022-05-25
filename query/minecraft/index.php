<?php
	require __DIR__ . '/src/MinecraftPing.php';
	require __DIR__ . '/src/MinecraftPingException.php';
	
	use xPaw\MinecraftPing;
	use xPaw\MinecraftPingException;
	
	try
	{
		$Query = new MinecraftPing( 'mc.hypixel.net', 25565 );
		
		$result = ( $Query->Query() );
		echo $result;
		$result = json_encode($result);
		echo $result;
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
