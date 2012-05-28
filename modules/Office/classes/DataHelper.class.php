<?php
abstract class Miao_Office_DataHelper
{
	protected function __construct()
	{

	}

	protected function __clone()
	{

	}

	static protected function _getInstance( $className )
	{
		$index = 'dh:' . $className;

		if ( Miao_Registry::isRegistered( $index ) )
		{
			$result = Miao_Registry::get( $index );
		}
		else
		{
			$result = new $className();
			Miao_Registry::set( $index, $result );
		}
		return $result;
	}
}