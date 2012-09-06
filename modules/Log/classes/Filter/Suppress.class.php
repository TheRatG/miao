<?php
class Miao_Log_Filter_Suppress extends Miao_Log_Filter_Abstract
{
	/**
	 * @var boolean
	 */
	protected $_accept = true;

	/**
	 * This is a simple boolean filter.
	 *
	 * Call suppress(true) to suppress all log events.
	 * Call suppress(false) to accept all log events.
	 *
	 * @param  boolean  $suppress  Should all log events be suppressed?
	 * @return  void
	 */
	public function suppress( $suppress )
	{
		$this->_accept = ( !$suppress );
	}

	/**
	 * Returns TRUE to accept the message, FALSE to block it.
	 *
	 * @param  array    $event    event data
	 * @return boolean            accepted?
	 */
	public function accept( $event )
	{
		return $this->_accept;
	}

	/**
	 * Create a new instance of Miao_Log_Filter_Suppress
	 *
	 * @param  array|Miao_Config $config
	 * @return Miao_Log_Filter_Suppress
	 * @throws Miao_Log_Exception
	 */
	static public function factory( $config )
	{
		return new self();
	}
}
