<?php
/**
 * Bootstrap file, include this file in all your php scripts
 */

/**
 * Return config data
 * @return unknown
 */
function getConfig()
{
	$root = realpath( dirname( __FILE__ ) . '/..' );
	$configFilename = $root . '/data/config.php';
	if ( !file_exists( $configFilename ) )
	{
		$configFilename = $root . '/data/config.dev.php';
	}
	$config = include $configFilename;

	return $config;
}

/**
 * Register Miao autoload.
 * You can use glue miao in your prod platform
 * @param unknown_type $config
 */
function autoloadInit( $config )
{
	$isMinify = isset( $config[ 'use_glue' ] ) ? $config[ 'use_glue' ] : false;
	if ( !$isMinify )
	{
		foreach ( $config[ 'libs' ] as $value )
		{
			if ( 'Miao' == $value[ 'name' ] )
			{
				require_once $value[ 'path' ] . '/modules/Autoload/classes/Autoload.class.php';
				break;
			}
		}
	}
	else
	{
		$miaoFilename = $config[ 'project_root' ] . '/scripts/miao.php';
		if ( file_exists( $miaoFilename ) )
		{
			require_once $config[ 'project_root' ] . '/scripts/miao.php';
		}
		else
		{
			$msg = sprintf( 'Run command from console: %s', $config[ 'project_root' ] . '/scripts/glue.php' );
			die( $msg );
		}
	}
}

$config = getConfig();
autoloadInit( $config );

Miao_Autoload::register( $config[ 'libs' ] );
Miao_Path::register( $config );
Miao_Env::defaultRegister();