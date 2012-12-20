<?php
/**
 * Bootstrap file, include this file in all your php scripts
 */

$boot = new MiaoBootstrap();
$boot->run();

/**
 *
 * @author vpak
 */
class MiaoBootstrap
{
	private $_root;

	public function __construct( $root = '' )
	{
		if ( empty( $root ) )
		{
			$this->_root = realpath( dirname( __FILE__ ) . '/..' );
		}
		else
		{
			$this->_root = $root;
		}
	}

	public function getConfig()
	{
		$configFilename = $this->_root . '/data/config_map.php';
		if ( !file_exists( $configFilename ) )
		{
			$configFilename = $this->_root . '/data/config_map.dev.php';
		}
		$config = include $configFilename;
		return $config;
	}

	public function run()
	{
		$config = $this->getConfig();
		$this->_initApp( $config );
	}

	protected function _initApp( array $config )
	{
		$appFilename = $this->_root . DIRECTORY_SEPARATOR . '/modules/App/classes/App.class.php';
		require_once $appFilename;
		Miao_App::getInstance( $config );
	}
}