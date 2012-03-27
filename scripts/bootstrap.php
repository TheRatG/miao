<?php
$config = include __DIR__ . '/../data/config.php' ;
//require_once PROJECT_ROOT . '/modules/Autoload/classes/Autoload.class.php';
require_once PROJECT_ROOT . '/scripts/miao.php';

Miao_Autoload::register( $config['libs'] );
Miao_Path::register( $config );
Miao_Env::register( $config['env'] );