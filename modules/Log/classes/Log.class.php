<?php
class Miao_Log
{
	const EMERG = 0; // Emergency: system is unusable
	const ALERT = 1; // Alert: action must be taken immediately
	const CRIT = 2; // Critical: critical conditions
	const ERR = 3; // Error: error conditions
	const WARN = 4; // Warning: warning conditions
	const NOTICE = 5; // Notice: normal but significant condition
	const INFO = 6; // Informational: informational messages
	const DEBUG = 7; // Debug: debug messages

	/**
	 *
	 * @var array of priorities where the keys are the
	 *      priority numbers and the values are the priority names
	 */
	protected $_priorities = array();

	/**
	 *
	 * @var array of Miao_Log_Writer_Abstract
	 */
	protected $_writers = array();

	/**
	 *
	 * @var array of Miao_Log_Filter_Interface
	 */
	protected $_filters = array();

	/**
	 *
	 * @var array of extra log event
	 */
	protected $_extras = array();

	/**
	 *
	 * @var string
	 */
	protected $_defaultWriterNamespace = 'Miao_Log_Writer';

	/**
	 *
	 * @var string
	 */
	protected $_defaultFilterNamespace = 'Miao_Log_Filter';

	/**
	 * Class constructor.
	 * Create a new logger
	 *
	 * @param $writer Miao_Log_Writer_Abstract|null
	 *       	 default writer
	 */
	public function __construct( Miao_Log_Writer_Abstract $writer = null )
	{
		$r = new ReflectionClass( $this );
		$this->_priorities = array_flip( $r->getConstants() );

		if ( $writer !== null )
		{
			$this->addWriter( $writer );
		}
	}

	/**
	 * Factory to construct the logger and one or more writers
	 * based on the configuration array
	 *
	 * @param array|Miao_Config Array or instance of Miao_Config
	 * @return Miao_Log
	 */
	static public function factory( $config = array() )
	{
		if ( !is_array( $config ) || empty( $config ) )
		{
			throw new Miao_Log_Exception( 'Configuration must be an array' );
		}

		$log = new Miao_Log();

		if ( !is_array( current( $config ) ) )
		{
			$log->addWriter( current( $config ) );
		}
		else
		{
			foreach ( $config as $writer )
			{
				$log->addWriter( $writer );
			}
		}

		return $log;
	}

	static public function easyFactory( $filename = '', $verbose = false, $level = 7 )
	{
		$result = new self();
		if ( $filename )
		{
			$result->addWriter( new Miao_Log_Writer_Stream( $filename ) );
		}
		if ( $verbose )
		{
			$result->addWriter( new Miao_Log_Writer_Stream( 'php://output' ) );
		}
		if ( !$filename && !$verbose )
		{
			$result->addWriter( new Miao_Log_Writer_Null() );
		}

		$filter = new Miao_Log_Filter_Priority( $level );
		$result->addFilter( $filter );

		return $result;
	}

	/**
	 * Construct a writer object based on a configuration array
	 *
	 * @param $spec array
	 *       	 config array with writer spec
	 * @return Miao_Log_Writer_Abstract
	 */
	protected function _constructWriterFromConfig( $config )
	{
		$writer = $this->_constructFromConfig( 'writer', $config, $this->_defaultWriterNamespace );

		if ( !$writer instanceof Miao_Log_Writer_Abstract )
		{
			$writerName = is_object( $writer ) ? get_class( $writer ) : 'The specified writer';
			throw new Miao_Log_Exception( "{$writerName} does not extend Miao_Log_Writer_Abstract!" );
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
	 *
	 * @param $config array|Miao_Config
	 *       	 Miao_Config or Array
	 * @return Miao_Log_Filter_Interface
	 */
	protected function _constructFilterFromConfig( $config )
	{
		$filter = $this->_constructFromConfig( 'filter', $config, $this->_defaultFilterNamespace );

		if ( !$filter instanceof Miao_Log_Filter_Interface )
		{
			$filterName = is_object( $filter ) ? get_class( $filter ) : 'The specified filter';
			throw new Miao_Log_Exception( "{$filterName} does not implement Miao_Log_Filter_Interface" );
		}

		return $filter;
	}

	/**
	 * Construct a filter or writer from config
	 *
	 * @param $type string
	 *       	 'writer' of 'filter'
	 * @param $config mixed
	 *       	 Miao_Config or Array
	 * @param $namespace string
	 * @return object
	 */
	protected function _constructFromConfig( $type, $config, $namespace )
	{
		if ( !is_array( $config ) || empty( $config ) )
		{
			throw new Miao_Log_Exception( 'Configuration must be an array' );
		}

		$params = isset( $config[ $type . 'Params' ] ) ? $config[ $type . 'Params' ] : array();
		$className = $this->getClassName( $config, $type, $namespace );

		$reflection = new ReflectionClass( $className );
		if ( !$reflection->implementsInterface( 'Miao_Log_FactoryInterface' ) )
		{
			throw new Miao_Log_Exception( 'Driver does not implement Miao_Log_FactoryInterface and can not be constructed from config.' );
		}

		return call_user_func( array( $className, 'factory' ), $params );
	}

	/**
	 * Get the writer or filter full classname
	 *
	 * @param $config array
	 * @param $type string
	 *       	 filter|writer
	 * @param $defaultNamespace string
	 * @return string full classname
	 */
	protected function getClassName( $config, $type, $defaultNamespace )
	{
		if ( !isset( $config[ $type . 'Name' ] ) )
		{
			throw new Miao_Log_Exception( "Specify {$type}Name in the configuration array" );
		}
		$className = $config[ $type . 'Name' ];

		$namespace = $defaultNamespace;
		if ( isset( $config[ $type . 'Namespace' ] ) )
		{
			$namespace = $config[ $type . 'Namespace' ];
		}

		$fullClassName = $namespace . '_' . $className;
		return $fullClassName;
	}

	/**
	 * Class destructor.
	 * Shutdown log writers
	 *
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
	 * $log->priorityName('message')
	 * instead of
	 * $log->log('message', Miao_Log::PRIORITY_NAME)
	 *
	 * @param $method string
	 *       	 priority name
	 * @param $params string
	 *       	 message to log
	 * @return void
	 * @throws Miao_Log_Exception
	 */
	public function __call( $method, $params )
	{
		$priority = strtoupper( $method );
		if ( ( $priority = array_search( $priority, $this->_priorities ) ) !== false )
		{
			switch ( count( $params ) )
			{
				case 0:
					throw new Miao_Log_Exception( 'Missing log message' );
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
			throw new Miao_Log_Exception( 'Bad log priority' );
		}
	}

	/**
	 * Log a message at a priority
	 *
	 * @param $message string
	 *       	 Message to log
	 * @param $priority integer
	 *       	 Priority of message
	 * @param $extras mixed
	 *       	 Extra information to log in event
	 * @return void
	 * @throws Miao_Log_Exception
	 */
	public function log( $message, $priority, $extras = null )
	{
		// sanity checks
		if ( empty( $this->_writers ) )
		{
			throw new Miao_Log_Exception( 'No writers were added' );
		}

		if ( !isset( $this->_priorities[ $priority ] ) )
		{
			throw new Miao_Log_Exception( 'Bad log priority' );
		}

		// pack into event required by filters and writers
		$event = array_merge( array(
			'timestamp' => date( 'c' ),
			'message' => $message,
			'priority' => $priority,
			'priorityName' => $this->_priorities[ $priority ] ), $this->_extras );

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
						$info[] = $value;
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
	 * Add a custom priority
	 *
	 * @param $name string
	 *       	 Name of priority
	 * @param $priority integer
	 *       	 Numeric priority
	 * @throws Miao_Log_InvalidArgumentException
	 */
	public function addPriority( $name, $priority )
	{
		// Priority names must be uppercase for predictability.
		$name = strtoupper( $name );

		if ( isset( $this->_priorities[ $priority ] ) || false !== array_search( $name, $this->_priorities ) )
		{
			throw new Miao_Log_Exception( 'Existing priorities cannot be overwritten' );
		}

		$this->_priorities[ $priority ] = $name;
	}

	/**
	 * Add a filter that will be applied before all log writers.
	 * Before a message will be received by any of the writers, it
	 * must be accepted by all filters added with this method.
	 *
	 * @param $filter int|Miao_Log_Filter_Interface
	 * @return void
	 */
	public function addFilter( $filter )
	{
		if ( is_integer( $filter ) )
		{
			$filter = new Miao_Log_Filter_Priority( $filter );

		}
		elseif ( is_array( $filter ) )
		{
			$filter = $this->_constructFilterFromConfig( $filter );

		}
		elseif ( !$filter instanceof Miao_Log_Filter_Interface )
		{
			throw new Miao_Log_Exception( 'Invalid filter provided' );
		}

		$this->_filters[] = $filter;
	}

	/**
	 * Add a writer.
	 * A writer is responsible for taking a log
	 * message and writing it out to storage.
	 *
	 * @param $writer mixed
	 *       	 Miao_Log_Writer_Abstract or Config array
	 * @return void
	 */
	public function addWriter( $writer )
	{
		if ( is_array( $writer ) )
		{
			$writer = $this->_constructWriterFromConfig( $writer );
		}

		if ( !$writer instanceof Miao_Log_Writer_Abstract )
		{
			throw new Miao_Log_Exception( 'Writer must be an instance of Miao_Log_Writer_Abstract' . ' or you should pass a configuration array' );
		}

		$this->_writers[] = $writer;
	}

	/**
	 * Set an extra item to pass to the log writers.
	 *
	 * @param $name Name
	 *       	 of the field
	 * @param $value Value
	 *       	 of the field
	 * @return void
	 */
	public function setEventItem( $name, $value )
	{
		$this->_extras = array_merge( $this->_extras, array( $name => $value ) );
	}
}
