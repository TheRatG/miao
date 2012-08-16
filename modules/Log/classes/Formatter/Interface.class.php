<?php
interface Miao_Log_Formatter_Interface
{
	/**
	 * Formats data into a single line to be written by the writer.
	 *
	 * @param  array    $event    event data
	 * @return string             formatted line to write to the log
	 */
	public function format( $event );

}
