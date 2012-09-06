<?php
interface Miao_Log_FactoryInterface
{
	/**
	 * Construct a Miao_Log driver
	 *
	 * @param  array|Miao_Config $config
	 * @return Miao_Log_FactoryInterface
	 */
	static public function factory( $config );
}
