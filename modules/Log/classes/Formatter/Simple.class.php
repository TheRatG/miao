<?php
class Miao_Log_Formatter_Simple implements Miao_Log_Formatter_Interface
{
	/**
	 * @var string
	 */
	protected $_format;

	const DEFAULT_FORMAT = '%timestamp% 1 %priorityName% (%priority%): %message%';

	/**
	 * Class constructor
	 *
	 * @param  null|string  $format  Format specifier for log messages
	 * @throws Miao_Log_Exception
	 */
	public function __construct( $format = null )
	{
		if ( $format === null )
		{
			$format = self::DEFAULT_FORMAT . PHP_EOL;
		}

		if ( !is_string( $format ) )
		{
			throw new Miao_Log_Exception( 'Format must be a string' );
		}

		$this->_format = $format;
	}

	/**
	 * Formats data into a single line to be written by the writer.
	 *
	 * @param  array    $event    event data
	 * @return string             formatted line to write to the log
	 */
	public function format( $event )
	{
		$output = $this->_format;
		foreach ( $event as $name => $value )
		{

			if ( ( is_object( $value ) && !method_exists( $value, '__toString' ) ) || is_array( $value ) )
			{

				$value = gettype( $value );
			}

			$output = str_replace( "%$name%", $value, $output );
		}
		return $output;
	}

}
