<?php
/**
 * @author vpak
 * @date 2013-08-13 15:37:16
 */

namespace Miao;

class Log
{
    const EMERG = 0;

    const ALERT = 1;

    const CRIT = 2;

    const ERR = 3;

    const WARN = 4;

    const NOTICE = 5;

    const INFO = 6;

    const DEBUG = 7;

    /**
     * @var array of priorities where the keys are the
     *      priority numbers and the values are the priority names
     */
    protected $_priorities = array();

    /**
     * @var array of \Miao\Log\Writer_Abstract
     */
    protected $_writers = array();

    /**
     * @var array of \Miao\Log\Filter\FilterInterface
     */
    protected $_filters = array();

    /**
     * @var array of extra log event
     */
    protected $_extras = array();

    /**
     * @var string
     */
    protected $_defaultWriterNamespace = '\Miao\Log\Writer';

    /**
     * @var string
     */
    protected $_defaultFilterNamespace = '\Miao\Log\Filter';

    static public function factory( $filename = '', $verbose = false, $level = 7 )
    {
        $result = new self();
        if ( $filename )
        {
            $result->addWriter( new \Miao\Log\Writer\Stream( $filename ) );
        }
        if ( $verbose )
        {
            $result->addWriter( new \Miao\Log\Writer\Stream( 'php://output' ) );
        }
        if ( !$filename && !$verbose )
        {
            $result->addWriter( new \Miao\Log\Writer\Null() );
        }

        $filter = new \Miao\Log\Filter\Priority( $level );
        $result->addFilter( $filter );

        return $result;
    }

    public function __construct( array $options = array() )
    {
        if ( !is_array( $options ) )
        {
            throw new \Miao\Log\Exception( 'Configuration must be an array' );
        }
        $r = new \ReflectionClass( $this );
        $this->_priorities = array_flip( $r->getConstants() );
        if ( isset( $options[ 'writers' ] ) && is_array( $options[ 'writers' ] ) )
        {
            foreach ( $options[ 'writers' ] as $writer )
            {
                $this->addWriter( $writer );
            }
        }
    }

    /**
     * Class destructor.
     * Shutdown log writers
     * @return void
     */
    public function __destruct()
    {
        foreach ( $this->_writers as $writer )
        {
            $writer->shutdown();
        }
    }

    /**
     * Undefined method handler allows a shortcut:
     * $log->priorityName('message')  instead of $log->log('message', \Miao\Log::PRIORITY_NAME)
     * @param $method string priority name
     * @param $params string message to log
     * @return void
     * @throws \Miao\Log\Exception
     */
    public function __call( $method, $params )
    {
        $priority = strtoupper( $method );
        if ( ( $priority = array_search( $priority, $this->_priorities ) ) !== false )
        {
            switch ( count( $params ) )
            {
                case 0:
                    throw new \Miao\Log\Exception( 'Missing log message' );
                case 1:
                    $message = array_shift( $params );
                    $extras = null;
                    break;
                default:
                    $message = array_shift( $params );
                    $extras = array_shift( $params );
                    break;
            }
            $this->log( $message, $priority, $extras );
        }
        else
        {
            throw new \Miao\Log\Exception( 'Bad log priority' );
        }
    }

    /**
     * Log a message at a priority
     * @param $message string Message to log
     * @param $priority integer Priority of message
     * @param $extras mixed Extra information to log in event
     * @return void
     * @throws \Miao\Log\Exception
     */
    public function log( $message, $priority, $extras = null )
    {
        // sanity checks
        if ( empty( $this->_writers ) )
        {
            throw new \Miao\Log\Exception( 'No writers were added' );
        }

        if ( !isset( $this->_priorities[ $priority ] ) )
        {
            throw new \Miao\Log\Exception( 'Bad log priority' );
        }

        // pack into event required by filters and writers
        $event = array_merge(
            array(
                 'timestamp' => date( 'c' ),
                 'message' => $message,
                 'priority' => $priority,
                 'priorityName' => $this->_priorities[ $priority ]
            ), $this->_extras
        );

        // Check to see if any extra information was passed
        if ( !empty( $extras ) )
        {
            $info = array();
            if ( is_array( $extras ) )
            {
                foreach ( $extras as $key => $value )
                {
                    if ( is_string( $key ) )
                    {
                        $event[ $key ] = $value;
                    }
                    else
                    {
                        $info[ ] = $value;
                    }
                }
            }
            else
            {
                $info = $extras;
            }
            if ( !empty( $info ) )
            {
                $event[ 'info' ] = $info;
            }
        }

        // abort if rejected by the global filters
        foreach ( $this->_filters as $filter )
        {
            if ( !$filter->accept( $event ) )
            {
                return;
            }
        }

        // send to each writer
        foreach ( $this->_writers as $writer )
        {
            $writer->write( $event );
        }
    }

    /**
     * Add a writer.
     * A writer is responsible for taking a log
     * message and writing it out to storage.
     * @param $writer \Miao\Log\Writer\AbstractWriter or Config array
     * @return void
     * @throws \Miao\Log\Exception
     */
    public function addWriter( $writer )
    {
        if ( is_array( $writer ) )
        {
            $writer = $this->_constructWriterFromConfig( $writer );
        }

        if ( !$writer instanceof \Miao\Log\Writer\AbstractWriter )
        {
            throw new \Miao\Log\Exception( 'Writer must be an instance of \Miao\Log\Writer\AbstractWriter' . ' or you should pass a configuration array' );
        }

        $this->_writers[ ] = $writer;
    }

    /**
     * Add a filter that will be applied before all log writers.
     * Before a message will be received by any of the writers, it
     * must be accepted by all filters added with this method.
     * @param $filter int|\Miao\Log\Filter\FilterInterface
     * @return void
     * @throws \Miao\Log\Exception
     */
    public function addFilter( $filter )
    {
        if ( is_integer( $filter ) )
        {
            $filter = new \Miao\Log\Filter\Priority( $filter );
        }
        elseif ( is_array( $filter ) )
        {
            $filter = $this->_constructFilterFromConfig( $filter );
        }
        elseif ( !$filter instanceof \Miao\Log\Filter\FilterInterface )
        {
            throw new \Miao\Log\Exception( 'Invalid filter provided' );
        }

        $this->_filters[ ] = $filter;
    }

    /**
     * Add a custom priority
     * @param $name string Name of priority
     * @param $priority integer Numeric priority
     * @throws \Miao\Log\Exception
     */
    public function addPriority( $name, $priority )
    {
        // Priority names must be uppercase for predictability.
        $name = strtoupper( $name );

        if ( isset( $this->_priorities[ $priority ] ) || false !== array_search( $name, $this->_priorities ) )
        {
            throw new \Miao\Log\Exception( 'Existing priorities cannot be overwritten' );
        }

        $this->_priorities[ $priority ] = $name;
    }

    /**
     * Construct a writer object based on a configuration array
     * @param array $config
     * @return \Miao\Log\Writer\Abstract
     * @throws Log\Exception
     */
    protected function _constructWriterFromConfig( array $config )
    {
        $writer = $this->_constructFromConfig( 'writer', $config, $this->_defaultWriterNamespace );

        if ( !$writer instanceof \Miao\Log\Writer\AbstractWriter )
        {
            $writerName = is_object( $writer ) ? get_class( $writer ) : 'The specified writer';
            throw new \Miao\Log\Exception( "{$writerName} does not extend \Miao\Log\Writer\AbstractWriter!" );
        }

        if ( isset( $config[ 'filterName' ] ) )
        {
            $filter = $this->_constructFilterFromConfig( $config );
            $writer->addFilter( $filter );
        }

        return $writer;
    }

    /**
     * Construct filter object from configuration array or Miao_Config object
     * @param array $config
     * @return \Miao\Log\Filter\Interface
     * @throws \Miao\Log\Exception
     */
    protected function _constructFilterFromConfig( array $config )
    {
        $filter = $this->_constructFromConfig( 'filter', $config, $this->_defaultFilterNamespace );

        if ( !$filter instanceof \Miao\Log\Filter\FilterInterface )
        {
            $filterName = is_object( $filter ) ? get_class( $filter ) : 'The specified filter';
            throw new \Miao\Log\Exception( "{$filterName} does not implement \Miao\Log\Filter\FilterInterface" );
        }

        return $filter;
    }

    /**
     * @param string $type 'writer' of 'filter'
     * @param $config \Miao\Config or Array
     * @param string $namespace
     * @return object
     * @throws \Miao\Log\Exception
     */
    protected function _constructFromConfig( $type, $config, $namespace )
    {
        if ( !is_array( $config ) || empty( $config ) )
        {
            throw new \Miao\Log\Exception( 'Configuration must be an array' );
        }

        $params = isset( $config[ $type . 'Params' ] ) ? $config[ $type . 'Params' ] : array();
        $className = $this->getClassName( $config, $type, $namespace );

        $reflection = new ReflectionClass( $className );
        if ( !$reflection->implementsInterface( '\Miao\Log\FactoryInterface' ) )
        {
            throw new \Miao\Log\Exception( 'Driver does not implement \Miao\Log\FactoryInterface and can not be constructed from config.' );
        }

        return call_user_func( array( $className, 'factory' ), $params );
    }
}