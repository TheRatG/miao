<?php
class Miao_Log_Writer_Stream extends Miao_Log_Writer_Abstract
{
	/**
	 * Holds the PHP stream to log to.
	 * @var null|stream
	 */
	protected $_stream = null;
	protected $_mode = 'a';

	/**
	 * Class Constructor
	 *
	 * @param  streamOrUrl     Stream or URL to open as a stream
	 * @param  mode            Mode, only applicable if a URL is given
	 */
	public function __construct( $streamOrUrl, $mode = NULL )
	{
		// Setting the default
		if ( $mode === NULL )
		{
			$this->_mode = 'a';
		}
		else
		{
			$this->_mode = $mode;
		}

		if ( is_resource( $streamOrUrl ) )
		{
			if ( get_resource_type( $streamOrUrl ) != 'stream' )
			{
				throw new Miao_Log_Exception( 'Resource is not a stream' );
			}

			if ( $mode != 'a' )
			{
				throw new Miao_Log_Exception( 'Mode cannot be changed on existing streams' );
			}

			$this->_stream = $streamOrUrl;
		}
		else
		{
			if ( is_array( $streamOrUrl ) && isset( $streamOrUrl[ 'stream' ] ) )
			{
				$streamOrUrl = $streamOrUrl[ 'stream' ];
			}

			$this->_open( $streamOrUrl );
		}

		$this->_formatter = new Miao_Log_Formatter_Simple();
	}

	/**
	 * Create a new instance of Miao_Log_Writer_Mock
	 *
	 * @param  array|Miao_Config $config
	 * @return Miao_Log_Writer_Mock
	 * @throws Miao_Log_Exception
	 */
	static public function factory( $config )
	{
		$config = self::_parseConfig( $config );
		$config = array_merge( array(
			'stream' => null,
			'mode' => null ), $config );

		$streamOrUrl = isset( $config[ 'url' ] ) ? $config[ 'url' ] : $config[ 'stream' ];

		return new self( $streamOrUrl, $config[ 'mode' ] );
	}

	/**
	 * Close the stream resource.
	 *
	 * @return void
	 */
	public function shutdown()
	{
		if ( is_resource( $this->_stream ) )
		{
			fclose( $this->_stream );
		}
	}

	/**
	 * Write a message to the log.
	 *
	 * @param  array  $event  event data
	 * @return void
	 */
	protected function _write( $event )
	{
		$this->_checkAndReopen();

		$line = $this->_formatter->format( $event );
		if ( false === @fwrite( $this->_stream, $line ) )
		{
			throw new Miao_Log_Exception( "Unable to write to stream" );
		}
	}

	/**
	 * Because delete log file does not throw exception, and when file,
	 * for instance service log rotator delete log file,
	 * delete you never know about it, and may lost your log
	 *
	 * @link http://newsgroups.derkeiler.com/Archive/Comp/comp.lang.c.moderated/2008-04/msg00000.html
	 *
	 */
	protected function _checkAndReopen()
	{
		$streamMeta = stream_get_meta_data( $this->_stream );
		$uri = $streamMeta[ 'uri' ];
		if ( $uri[ 0 ] == '/' )
		{
			clearstatcache( false, $streamMeta[ 'uri' ] );
			if ( !file_exists( $streamMeta[ 'uri' ] ) )
			{
				$this->_open( $uri );
			}
		}
	}

	protected function _open( $uri )
	{
		if ( !$this->_stream = @fopen( $uri, $this->_mode, false ) )
		{
			$msg = "\"$uri\" cannot be opened with mode \"$this->_mode\"";
			throw new Miao_Log_Exception( $msg );
		}

		if ( file_exists( $uri ) )
		{
			@chmod( $uri, 0777 );
		}
	}
}
