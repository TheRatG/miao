<?php
/**
 * Console file, run console miao commands.
 * Test, Class generator.
 */
require_once 'bootstrap.php';
require_once 'vendor/autoload.php';

$application = new \Symfony\Component\Console\Application();
$application->add( new \Miao\Console\Command\Info() );
$application->add( new \Miao\Console\Command\Generate\ModuleCommand() );
$application->add( new \Miao\Console\Command\Generate\ClassCommand() );
$application->add( new \Miao\Console\Command\Generate\TestCommand() );
$application->run();