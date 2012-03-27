<?php
$maioDir = realpath( __DIR__ .'/..' );
require_once $maioDir . '/modules/Glue/classes/Glue.class.php';

$resultFilename = $maioDir . '/scripts/miao.php';

$modules = array();
$modules[] = 'Autoload';
$modules[] = 'Config';
$modules[] = 'Env';
$modules[] = 'PHPUnit';
$modules[] = 'Registry';

$files = array();
foreach ( $modules as $moduleName )
{
	$dirname = $maioDir . '/modules/' . $moduleName . '/classes';
	$files = array_merge( $files, Miao_Glue::getFileList( $dirname ) );
}

$message = sprintf( "Found (%d) files: %s\n", count( $files ), print_r( $files ) );
echo $message;

$compact = true;
$glue = new Miao_Glue( $files );
$res = $glue->weld( $resultFilename, $compact );

$message = sprintf( "Result: %s\n", $res ? 'Ok' : 'Fail' );
echo $message;