<?php
require_once __DIR__ . '/bootstrap.php';

function help()
{
	$mes = array();
	$mes[] = "-f -- search test file";
	$mes[] = "-d -- resolve name to dir and trying found test file in dir";
	$mes[] = "--no-run -- ";
	$mes[] = "--help/-h -- print help";

	$message = implode( "\n", $mes );
	echo $message;
	exit();
}

if ( isset( $_SERVER[ 'argc' ] ) )
{
	$opts = array();
	$remainingArgs = array();
	foreach ( $_SERVER[ 'argv' ] as $key => $value )
	{
		if ( 0 == $key )
		{
			continue;
		}

		switch ( $value )
		{
			case '-f':
				$opts[ 'file' ] = true;
				break;
			case '-d':
				$opts[ 'dir' ] = true;
				break;
			case '--no-run':
				$opts[ 'no-run' ] = true;
				break;
			case '--help':
			case '-h':
				help();
				break;
			default:
				$remainingArgs[] = $value;
		}
	}

	$console = new Miao_PHPUnit_Console( $opts, $remainingArgs );
	$console->run();
}