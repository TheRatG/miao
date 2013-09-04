<?php
/**
 * @author vpak
 * @date 2013-08-13 16:23:35
 */

namespace Miao\Log\Writer;

class Stream extends \Miao\Log\Writer\AbstractWriter
{
    /**
     * Holds the PHP stream to log to.
     * @var null|stream
     */
    protected $_stream = null;

    /**
     * @var string
     */
    protected $_mode = 'a';

    /**
     * Class Constructor
     * @param $streamOrUrl Stream or URL to open as a stream
     * @param null $mode Mode, only applicable if a URL is given
     * @throws \Miao\Log\Exception
     */
    public function __construct( $streamOrUrl, $mode = null )
    {
        // Setting the default
        if ( $mode === null )
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
                throw new \Miao\Log\Exception( 'Resource is not a stream' );
            }

            if ( $mode != 'a' )
            {
                throw new \Miao\Log\Exception( 'Mode cannot be changed on existing streams' );
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

        $this->_formatter = new \Miao\Log\Formatter\Simple();
    }

    /**
     * Close the stream resource.
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
     * Create a new instance of \Miao\Log\Writer\Stream
     * @param  array|\Miao\Config $config
     * @return \Miao\Log\Writer\Stream
     * @throws \Miao\Log\Exception
     */
    static public function factory( $config )
    {
        $config = self::_parseConfig( $config );
        $config = array_merge(
            array(
                 'stream' => null,
                 'mode' => null
            ), $config
        );

        $streamOrUrl = isset( $config[ 'url' ] ) ? $config[ 'url' ] : $config[ 'stream' ];

        return new self( $streamOrUrl, $config[ 'mode' ] );
    }

    /**
     * Write a message to the log.
     * @param  array $event  event data
     * @return void
     * @throws \Miao\Log\Exception
     */
    protected function _write( $event )
    {
        $this->_checkAndReopen();

        $line = $this->_formatter->format( $event );
        if ( false === @fwrite( $this->_stream, $line ) )
        {
            throw new \Miao\Log\Exception( "Unable to write to stream" );
        }
    }

    /**
     * Because delete log file does not throw exception, and when file,
     * for instance service log rotate delete log file,
     * delete you never know about it, and may lost your log
     * @link http://newsgroups.derkeiler.com/Archive/Comp/comp.lang.c.moderated/2008-04/msg00000.html
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
            throw new \Miao\Log\Exception( $msg );
        }

        if ( file_exists( $uri ) )
        {
            @chmod( $uri, 0777 );
        }
    }
}