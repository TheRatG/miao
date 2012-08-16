<?php
interface Miao_Log_Filter_Interface
{
	/**
	 * Returns TRUE to accept the message, FALSE to block it.
	 *
	 * @param  array    $event    event data
	 * @return boolean            accepted?
	 */
	public function accept( $event );
}
