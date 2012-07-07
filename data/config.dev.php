<?php
$projectRoot = realpath( dirname( __FILE__ ) . '/../' );
return array(
	'project_root' => $projectRoot,
	'main_config_filename' => __FILE__,
	'use_glue' => false,
	'libs' => array(
		0 => array(
			'name' => 'Miao',
			'path' => $projectRoot,
			'plugin' => 'Standart' ),
		1 => array(
			'name' => 'PHPUnit',
			'path' => '/usr/share/php/PHPUnit',
			'plugin' => 'PHPUnit' ) ) );