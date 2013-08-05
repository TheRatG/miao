<?php
/**
 * Bootstrap file, include this file in all your php scripts
 */
$projectRoot = realpath( dirname( __FILE__ ) . '/../' );

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
$configModules = array();

// Include Autoload module
require_once $projectRoot . '/modules/Autoload/classes/Autoload.php';
// Register libs
Miao\Autoload\Autoload::init( $configMap[ 'libs' ] );
// Init application
Miao\Application::init( $configMain, $configModules );