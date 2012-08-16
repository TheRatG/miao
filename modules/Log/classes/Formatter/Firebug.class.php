<?php
class Miao_Log_Formatter_Firebug implements Miao_Log_Formatter_Interface
{
	/**
	 * This method formats the event for the firebug writer.
	 *
	 * The default is to just send the message parameter, but through
	 * extension of this class and calling the
	 * {@see Miao_Log_Writer_Firebug::setFormatter()} method you can
	 * pass as much of the event data as you are interested in.
	 *
	 * @param  array    $event    event data
	 * @return mixed              event message
	 */
	public function format( $event )
	{
		return $event[ 'message' ];
	}
}
