<?php
/**
 * Core and facade
 *
 * @author vpak
 */
class Miao_App
{
	private static $_instance;
	private $_configData;
	private $_autoload;
	private $_path;
	private $_config;

	/**
	 * @return the $_autoload
	 */
	public function getAutoload()
	{
		return $this->_autoload;
	}

	/**
	 * @return the $_path
	 */
	public function getPath()
	{
		return $this->_path;
	}

	/**
	 * @return the $_config
	 */
	public function getConfig()
	{
		return $this->_config;
	}

	static public function getInstance( array $config = array() )
	{
		if ( !empty( $config ) )
		{
			self::$_instance = new self( $config );

			self::$_instance->_initAutoload();
			self::$_instance->_initPath();

			Miao_Env::defaultRegister();

			self::$_instance->_config = new Miao_Config( self::$_instance->_path );
		}
		return self::$_instance;
	}

	private function __construct( array $config = array() )
	{
		$this->_configData = $config;
	}

	private function __clone()
	{
	}

	/**
	 * @TODO: add glue
	 */
	private function _initAutoload()
	{
		$config = $this->_configData;
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
		$autoloadConfig = $this->_configData[ 'libs' ];

		$this->_autoload = new Miao_Autoload();
		$this->_autoload->register( $autoloadConfig );
	}

	/**
	 * @TODO: del register from Path
	 */
	private function _initPath()
	{
		$config = $this->_configData;
		$map = array();
		foreach ( $config[ 'libs' ] as $item )
		{
			$map[ $item[ 'name' ] ] = $item[ 'path' ];
		}
		$this->_path = new Miao_Path( $config[ 'project_root' ], $config[ 'main_config_filename' ], $map );
	}
}