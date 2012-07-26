<?php
require_once '#phing:libs.Miao.deploy.dst#/scripts/bootstrap.php';

function help()
{
	$mes = array();
	$mes[] = "Welcome to miao console...";
	$mes[] = "-f -- search test file";
	$mes[] = "-d -- resolve name to dir and trying found test file in dir";
	$mes[] = "--no-run -- ";
	$mes[] = "--help/-h -- print help";
	$mes[] = "";
	$mes[] = "Run all miao tests script with params \"test.php -d Miao\"";
	$mes[] = "";

	$message = implode( "\n", $mes );
	echo $message;
	exit();
}

if ( isset( $_SERVER[ 'argc' ] ) )
{
	$opts = array();
	$remainingArgs = array();
	$argv = $_SERVER[ 'argv' ];
	foreach ( $argv as $key => $value )
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
	if ( empty( $opts ) )
	{
		help();
	}
	else
	{
		$console = new Miao_PHPUnit_Console( $opts, $remainingArgs );
		$console->run();
	}
}