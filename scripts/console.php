<?php
/** 
 * User: vpak
 * Date: 02.09.13
 * Time: 16:21 
 */
require_once 'bootstrap.php';

$application = new \Symfony\Component\Console\Application();
$application->add( new \Miao\Console\Command\Info() );
$application->add( new \Miao\Console\Command\Generate\ModuleCommand() );
$application->add( new \Miao\Console\Command\Generate\ClassCommand() );
$application->add( new \Miao\Console\Command\Generate\TestCommand() );
$application->run();