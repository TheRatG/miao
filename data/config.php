<?php
define( 'PROJECT_ROOT', realpath( __DIR__ . '/..' ) );
return array(
		'project_root' => PROJECT_ROOT,
		'main_config_filename' => PROJECT_ROOT . '/data/config.php',
		'libs' => array(
				array(
						'name' => 'PHPUnit',
						'path' => '/usr/share/php',
						'plugin' => 'PHPUnit' ),
				array(
						'name' => 'Miao',
						'path' => PROJECT_ROOT,
						'plugin' => 'Standart' )
		),
		'env' => array(
				'error_level' => E_ALL,  // 30719
				'default_timezone' => 'Europe/Moscow',
				'unregister_globals' => true,
				'strip_global_slashes' => true,
				'umask' => 0,
				'upload_tmp_dir' => '/tmp' ) );