<?php
/**
 * Bootstrap file, include this file in all your php scripts
 * User: vpak
 * Date: 02.09.13
 * Time: 15:05
 */
$projectRoot = realpath( dirname( __FILE__ ) . '/../' );
$loaderFilename = $projectRoot . '/vendor/autoload.php';
if ( !file_exists( $loaderFilename ) )
{
    $loaderFilename = realpath( $projectRoot . '/../..' ) . '/autoload.php';
}

/** @var Composer\Autoload\ClassLoader $loader */
$loader = require_once $loaderFilename;

## Load config file

/**
 * Autoload properties
 */
$configMap = array(
    'project_root' => $projectRoot,
    'config_root' => $projectRoot . '/config',
    'use_glue' => false,
    'libs' => array(
        0 => array(
            'name' => 'Miao',
            'path' => $projectRoot,
            'plugin' => 'Standart'
        )
    )
);

/**
 * Project properties
 */
$configMain = array(
    'config' => array(
        'project_name' => 'miao',
        'build_type' => 'develop',
        'profile' => '0',
        'debug' => '1',
        'paths' => array(
            'root' => $projectRoot,
            'data' => $projectRoot . DIRECTORY_SEPARATOR . 'data',
            'tmp' => $projectRoot . DIRECTORY_SEPARATOR . 'tmp'
        )
    )
);

/**
 * Modules properties
 */
$configModules = array( 'Miao' => array() );

// Register libs
\Miao\Autoload::init( $configMap[ 'libs' ] );

// Init application
\Miao\App::init( $configMap, $configMain, $configModules );

//Register composer loader
\Miao\App::getInstance()->setObject( $loader, \Miao\App::INSTANCE_COMPOSER_LOADER_NICK );