<?php
class Miao_Config
{
	const MAIN = '::main::';

	private $_baseInstanceList = array();

	/**
	 *
	 * @var Miao_Config_File
	 */
	private $_file;

	static public function Main( $throwException = true )
	{
		$instance = self::_getDefaultInstance();
		$result = $instance->_getConfig( self::MAIN, $throwException );
		return $result;
	}

	static public function Libs( $className, $throwException = true )
	{
		$instance = self::_getDefaultInstance();
		$result = $instance->_getConfig( $className, $throwException );
		return $result;
	}

	static public function checkConfig()
	{
		return false;
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
		$base = $this->_getBaseByLibName( $libName );
		$resultData = $base->get( implode( '.', $ar ) );
		$result = new Miao_Config_Base( $resultData );
		return $result;
	}

	private function _getBaseByLibName( $libName )
	{
		if ( !isset( $this->_baseInstanceList[ $libName ] ) )
		{
			$path = Miao_Path::getDefaultInstance();
			if ( $libName == self::MAIN )
			{
				$filename = $path->getMainConfigFilename();
			}
			else
			{
				$filename = $path->getRootByLibName( $libName ) . '/data/config.php';
			}
			$configData = include $filename;

			$base = new Miao_Config_Base( $configData );
			$this->_baseInstanceList[ $libName ] = $base;
		}
		return $this->_baseInstanceList[ $libName ];
	}
}