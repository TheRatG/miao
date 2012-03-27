<?php
class Miao_Config
{
	const SECTION_NAME_BUILD = 'build';

	static public function Main()
	{
		$instance = self::_getDefaultInstance();

		$path = self::SECTION_NAME_BUILD;
		$result = $instance->_get( $path );
		return $result;
	}

	static public function Libs( $className )
	{
		$instance = self::_getDefaultInstance();

		$path = str_replace( '_', '/', $className );
		$result = $instance->_get( $path );
		return $result;
	}

	static public function Modules( $className )
	{
		// trigger_error( 'Use function Miao_Config::Libs()', E_DEPRECATED );
		return self::Libs( $className );
	}

	/**
	 *
	 * @var Miao_Config_Base
	 */
	private $_base;

	/**
	 *
	 * @var Miao_Config_File
	 */
	private $_file;

	public function __construct()
	{
		$this->setBase( new Miao_Config_Base( array() ) );
		$this->_file = new Miao_Config_File();
	}

	/**
	 *
	 * @return the $_base
	 */
	public function getBase()
	{
		return $this->_base;
	}

	/**
	 *
	 * @param $base Miao_Config_Base
	 */
	public function setBase( $base )
	{
		$this->_base = $base;
	}

	static private function _getDefaultInstance()
	{
		$index = 'Miao_Config::default';
		if ( !Miao_Registry::isRegistered( $index ) )
		{
			$result = new self();
			Miao_Registry::set( $index, $result );
		}
		else
		{
			$result = Miao_Registry::get( $index );
		}
		return $result;
	}

	private function _get( $path )
	{
		$base = $this->getBase();

		$result = null;
		try
		{
			$result = $base->get( $path );
		}
		catch ( Miao_Config_Exception_PathNotFound $e )
		{
			$ar = explode( '/', $path );
			$className = implode( '_', $ar );

			if ( self::SECTION_NAME_BUILD == $path )
			{
				$pathMain = $path;
				$configFilename = $this->_file->getFilenameMain();
				$configData = include $configFilename;
				$configData = $configData[ 'config' ];
			}
			else
			{
				$pathMain = $ar[ 0 ];
				$configFilename = $this->_file->getFilenameByClassName( $className );
				$configData = include $configFilename;
			}

			$base->add( $pathMain, $configData );
		}
		$configData = $base->get( $path );
		$result = new Miao_Config_Base( $configData );
		return $result;
	}
}