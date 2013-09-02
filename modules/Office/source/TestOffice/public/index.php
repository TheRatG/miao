<?php
/**
 * User: vpak
 * Date: 02.09.13
 * Time: 16:14
 */
$projectRoot = realpath( __DIR__ . '/../..' );
require_once $projectRoot . '/scripts/bootstrap.php';

$factory = new \Miao\Office\Factory( '\\Miao\\TestOffice' );
$office = $factory->getOffice( $_GET, array( '_view' => 'Main' ) );
$office->sendResponse();