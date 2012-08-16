<?php
class Miao_Log_Writer_Null extends Miao_Log_Writer_Abstract
{
	/**
	 * Write a message to the log.
	 *
	 * @param  array  $event  event data
	 * @return void
	 */
	protected function _write( $event )
	{
	}

	/**
	 * Create a new instance of Miao_Log_Writer_Null
	 *
	 * @param  array|Config $config
	 * @return Miao_Log_Writer_Null
	 * @throws Miao_Log_Exception
	 */
	static public function factory( $config )
	{
		return new self();
	}
}
