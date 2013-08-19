<?php

// Libs properties
$configMap = include '#phing:libs.Project.deploy.dst#/data/config_map.php';
// Common properties
$configMain = include '#phing:libs.Project.deploy.dst#/data/config_main.php';
// Modules properties
$configModules = include '#phing:libs.Project.deploy.dst#/data/config.php';

// Include Autoload module
require_once '#phing:libs.Miao.deploy.dst#/modules/Autoload/classes/Autoload.class.php';

// Register libs
\Miao\Autoload::init( $configMap[ 'libs' ] );

// Init application
\Miao\App::init( $configMap, $configMain, $configModules );
