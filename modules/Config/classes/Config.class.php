<?php
class Miao_Config
{
	const SECTION_NAME_MAIN = 'main';
	const SECTION_NAME_PROJECT = 'project';

	static public function Main()
	{
		$instance = self::_getDefaultInstance();

		$path = self::SECTION_NAME_MAIN;
		$result = $instance->_get( $path );
		return $result;
	}

	static public function Project()
	{
		$instance = self::_getDefaultInstance();

		$path = self::SECTION_NAME_PROJECT;
		$result = $instance->_get( $path, '' );
		return $result;
	}

	static public function Libs( $className )
	{
		$instance = self::_getDefaultInstance();

		$pieces = explode( '_', $className );
		array_shift( $pieces );
		$path = implode( Miao_Config_Base::DELIMETR, $pieces );
		$result = $instance->_get( $path, $className );
		return $result;
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

	private function _get( $path, $className )
	{
		$base = $this->getBase();

		$result = null;
		try
		{
			$result = $base->get( $path );
		}
		catch ( Miao_Config_Exception_PathNotFound $e )
		{
			$ar = explode( Miao_Config_Base::DELIMETR, $path );
			if ( empty( $className ) )
			{
				$className = implode( '_', $ar );
			}

			if ( in_array( $path, array(
				self::SECTION_NAME_MAIN,
				self::SECTION_NAME_PROJECT ) ) )
			{
				$pathMain = $path;
				$funcName = '_getSection' . ucfirst( $path );
				$configData = $this->$funcName( $className );
			}
			else
			{
				$pathMain = $ar[ 0 ];
				$configData = $this->_getSectionDefault( $className );
				$configData = $configData[ $pathMain ];
			}

			$base->add( $pathMain, $configData );
		}
		$configData = $base->get( $path );
		$result = new Miao_Config_Base( $configData );
		return $result;
	}

	private function _getSectionDefault( $className )
	{
		$configFilename = $this->_file->getFilenameByClassName( $className );
		$configData = include $configFilename;
		return $configData;
	}

	private function _getSectionMain()
	{
		$pathMain = $path;
		$configFilename = $this->_file->getFilenameMain();
		$configData = include $configFilename;
		return $configData;
	}

	private function _getSectionProject()
	{
		$configFilename = $this->_file->getFilenameProject();
		$configData = include $configFilename;
		$configData = $configData[ 'config' ];
		if ( isset( $configData[ 'libs' ] ) )
		{
			unset( $configData[ 'libs' ] );
		}
		return $configData;
	}
}