<?php
/**
 * Console file, run console miao commands.
 * Test, Class generator.
 */
require_once 'bootstrap.php';

$app = Miao\Application::getInstance();
$configMain = $app->getConfig()->toArray();
//$configMiao = $app->getConfig( 'Miao' )->toArray();

// --- dump ---
echo __FILE__ . __METHOD__ . chr( 10 );
var_dump( $configMain ) . chr( 10 );
// --- // ---