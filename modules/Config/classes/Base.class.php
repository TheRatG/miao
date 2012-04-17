<?php
class Miao_Config_Base
{
	const DELIMETR = '.';

	private $_configData;

	public function __construct( array $configData )
	{
		$this->_configData = $configData;
	}

	public function get( $path, $default = null )
	{
		if ( empty( $path ) )
		{
			throw new Miao_Config_Exception_InvalidPath( $path, 'path is empty' );
		}
		if ( substr( $path, 0, 1 ) !== self::DELIMETR )
		{
			$path = self::DELIMETR . $path;
		}

		$result = $this->_configData;
		if ( $path !== self::DELIMETR )
		{
			$keys = explode( self::DELIMETR, $path );
			for( $i = 1, $c = count( $keys ); $i < $c; $i++ )
			{
				if ( empty( $keys[ $i ] ) )
				{
					throw new Miao_Config_Exception_InvalidPath( $path, 'path contains empty key' );
				}
				if ( !is_array( $result ) || !isset( $result[ $keys[ $i ] ] ) )
				{
					if ( is_null( $default ) )
					{
						throw new Miao_Config_Exception_PathNotFound( $path );
					}
					$result = $default;
					break;
				}
				$result = $result[ $keys[ $i ] ];
			}
		}
		return $result;
	}

	public function add( $pathMain, array $configData )
	{
		$this->_configData[ $pathMain ] = $configData;
	}

	public function toArray()
	{
		return $this->_configData;
	}
}