<?php
require_once 'classes/Glue.class.php';

$maioDir = '/www/v2.teleprog.rbc.ru/libs/miao/trunk';
$resultFilename = 'miao.php';

$modules = array();
$modules[] = 'Autoload';
$modules[] = 'Config';
$modules[] = 'Path';
$modules[] = 'Environment';
$modules[] = 'TemplatesEngine';
$modules[] = 'FrontOffice';

$files = array();
foreach ( $modules as $moduleName )
{
	$dirname = $maioDir . '/modules/' . $moduleName . '/classes';
	$files = array_merge( $files, Miao_Glue::getFileList( $dirname ) );
}

// --- dump ---
echo '<pre>';
echo __FILE__ . chr( 10 );
echo __METHOD__ . chr( 10 );
var_dump( $files );
echo '</pre>';
// --- // ---

$compact = true;
$glue = new Miao_Glue( $files );
$res = $glue->weld( $resultFilename, $compact );

 // --- dump ---
echo '<pre>';
echo __FILE__ . chr( 10 );
echo __METHOD__ . chr( 10 );
var_dump( $res );
echo '</pre>';
// --- // ---