<?php
class Miao_Log_Writer_Mock extends Miao_Log_Writer_Abstract
{
	/**
	 * array of log events
	 */
	public $events = array();

	/**
	 * shutdown called?
	 */
	public $shutdown = false;

	/**
	 * Write a message to the log.
	 *
	 * @param  array  $event  event data
	 * @return void
	 */
	public function _write( $event )
	{
		$this->events[] = $event;
	}

	/**
	 * Record shutdown
	 *
	 * @return void
	 */
	public function shutdown()
	{
		$this->shutdown = true;
	}

	/**
	 * Create a new instance of Miao_Log_Writer_Mock
	 *
	 * @param  array|Config $config
	 * @return Miao_Log_Writer_Mock
	 * @throws Miao_Log_Exception
	 */
	static public function factory( $config )
	{
		return new self();
	}
}
