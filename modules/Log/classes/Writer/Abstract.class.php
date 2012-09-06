<?php
abstract class Miao_Log_Writer_Abstract implements Miao_Log_FactoryInterface
{
	/**
	 * @var array of Miao_Log_Filter_Interface
	 */
	protected $_filters = array();

	/**
	 * Formats the log message before writing.
	 * @var Miao_Log_Formatter_Interface
	 */
	protected $_formatter;

	/**
	 * Add a filter specific to this writer.
	 *
	 * @param  Miao_Log_Filter_Interface  $filter
	 * @return void
	 */
	public function addFilter( $filter )
	{
		if ( is_integer( $filter ) )
		{
			$filter = new Miao_Log_Filter_Priority( $filter );
		}

		if ( !$filter instanceof Miao_Log_Filter_Interface )
		{
			throw new Miao_Log_Exception( 'Invalid filter provided' );
		}

		$this->_filters[] = $filter;
	}

	/**
	 * Log a message to this writer.
	 *
	 * @param  array     $event  log data event
	 * @return void
	 */
	public function write( $event )
	{
		foreach ( $this->_filters as $filter )
		{
			if ( !$filter->accept( $event ) )
			{
				return;
			}
		}

		// exception occurs on error
		$this->_write( $event );
	}

	/**
	 * Set a new formatter for this writer
	 *
	 * @param  Miao_Log_Formatter_Interface $formatter
	 * @return void
	 */
	public function setFormatter( Miao_Log_Formatter_Interface $formatter )
	{
		$this->_formatter = $formatter;
	}

	/**
	 * Perform shutdown activites such as closing open resources
	 *
	 * @return void
	 */
	public function shutdown()
	{
	}

	/**
	 * Write a message to the log.
	 *
	 * @param  array  $event  log data event
	 * @return void
	 */
	abstract protected function _write( $event );

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
			throw new Miao_Log_Exception( 'Configuration must be an array' );
		}

		return $config;
	}
}
