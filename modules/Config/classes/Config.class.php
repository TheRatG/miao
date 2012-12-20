<?php
class Miao_Config
{
	const MAIN = '::main::';

	private $_baseInstanceList = array();

	/**
	 *
	 * @var Miao_Path
	 */
	private $_path;

	/**
	 *
	 * @var Miao_Config_File
	 */
	private $_file;

	public function __construct( Miao_Path $path )
	{
		$this->_path = $path;
	}

	static public function Main( $throwException = true )
	{
		$instance = self::getInstance();
		$result = $instance->_getConfig( self::MAIN, $throwException );
		return $result;
	}

	static public function Libs( $className, $throwException = true )
	{
		$instance = self::getInstance();
		$result = $instance->_getConfig( $className, $throwException );
		return $result;
	}

	static public function checkConfig()
	{
		return false;
	}

	static public function getInstance()
	{
		$result = Miao_App::getInstance()->getConfig();
		return $result;
	}

	private function _getConfig( $className, $throwException )
	{
		$ar = explode( '_', $className );
		if ( empty( $ar ) )
		{
			$message = sprintf( 'Invalid param class name (%s)', $className );
			throw new Miao_Config_Exception();
		}
		$libName = $ar[ 0 ];
		if ( self::MAIN === $libName )
		{
			$ar[ 0 ] = 'config';
		}
		$result = null;
		try
		{
			$base = $this->_getBaseByLibName( $libName );
			$resultData = $base->get( implode( '.', $ar ) );
			if ( !is_array( $resultData ) )
			{
				$resultData = array( $resultData );
			}

			$result = new Miao_Config_Base( $resultData );
		}
		catch ( Miao_Config_Exception_PathNotFound $e )
		{
			if ( $throwException )
			{
				throw $e;
			}
		}
		return $result;
	}

	private function _getBaseByLibName( $libName )
	{
		if ( !isset( $this->_baseInstanceList[ $libName ] ) )
		{
			$path = $this->_path;
			if ( $libName == self::MAIN )
			{
				$filename = $path->getMainConfigFilename();
			}
			else
			{
				$filename = $path->getRootByLibName( $libName ) . '/data/config_modules.php';
				if ( !file_exists( $filename ) )
				{
					$filename = $path->getRootByLibName( $libName ) . '/data/config_modules.dev.php';
				}
			}
			$configData = include $filename;

			$base = new Miao_Config_Base( $configData );
			$this->_baseInstanceList[ $libName ] = $base;
		}
		return $this->_baseInstanceList[ $libName ];
	}
}