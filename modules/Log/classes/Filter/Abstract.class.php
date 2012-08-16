<?php
abstract class Miao_Log_Filter_Abstract implements Miao_Log_Filter_Interface, Miao_Log_FactoryInterface
{
	/**
	 * Validate and optionally convert the config to array
	 *
	 * @param  array|Miao_Config $config Miao_Config or Array
	 * @return array
	 * @throws Miao_Log_Exception
	 */
	static protected function _parseConfig( $config )
	{
		if ( !is_array( $config ) )
		{
			throw new Miao_Log_Exception( 'Configuration must be an array or instance of Miao_Config' );
		}

		return $config;
	}
}
