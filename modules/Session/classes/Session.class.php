<?php
/**
 *
 *
 */
class Miao_Session
{

	protected static $_instance = null;

	protected $_optionsMap = array(
		'save_path',
		'name',
		'save_handler',
		'gc_probability',
		'gc_divisor',
		'gc_maxlifetime',
		'serialize_handler',
		'cookie_lifetime',
		'cookie_path',
		'cookie_domain',
		'cookie_secure',
		'cookie_httponly',
		'use_cookies',
		'use_only_cookies',
		'referer_check',
		'entropy_file',
		'entropy_length',
		'cache_limiter',
		'cache_expire',
		'use_trans_sid',
		'bug_compat_42',
		'bug_compat_warn',
		'hash_function',
		'hash_bits_per_character' );

	protected $_handler = null;

	protected $_namespaceList = array();

	protected function __construct( $options, $handler )
	{
		$this->setOptions( $options );
		$this->setHandler( $handler );
	}

	/**
	 *
	 * @param $options array
	 * @param $handler unknown_type
	 * @return Miao_Session
	 */
	static public function getInstance( array $options = array(), $handler = null )
	{
		if ( is_null( self::$_instance ) )
		{
			if ( empty( $options ) )
			{
				$config = Miao_Config::Libs( 'Miao_Session' );
				$options = $config->get( 'options', false );
				if ( !$options )
				{
					$options = array();
				}
			}

			if ( is_null( $handler ) )
			{
				$config = Miao_Config::Libs( 'Miao_Session' );
				$handlerConfig = $config->get( 'Handler', false );
				if ( empty( $handlerConfig ) )
				{
					$handler = new Miao_Session_Handler_Empty();
				}
				else
				{
					$handlerClassName = 'Miao_Session_Handler_' . key( $handlerConfig );
					$handler = Miao_Config_Instance::get( $handlerClassName );
				}
			}
			self::$_instance = new self( $options, $handler );
		}
		return self::$_instance;
	}

	static public function getNamespace( $namespace )
	{
		$session = self::getInstance();
		$session->start();
		$result = $session->_getNamespace( $namespace );
		return $result;
	}

	protected function _getNamespace( $namespace )
	{
		if ( array_key_exists( $namespace, $this->_namespaceList ) )
		{
			$result = $this->_namespaceList[ $namespace ];
		}
		else
		{
			$this->_namespaceList[ $namespace ] = new Miao_Session_Namespace( $namespace );
			$result = $this->_namespaceList[ $namespace ];
		}
		return $result;
	}

	public function load( $varName, $defaultValue = null, $useNullAsDefault = false )
	{
		$result = null;
		$namespace = self::getNamespace( __CLASS__ );
		if ( false === $namespace->offsetExists( $varName ) )
		{
			if ( ( null === $defaultValue ) && ( false === $useNullAsDefault ) )
			{
				$message = sprintf( 'Variable with name "%s" was not stored in session', $varName );
				throw new Miao_Session_Exception_UndefinedVar( $message );
			}
			$result = $defaultValue;
		}
		else if ( is_null( $namespace[ $varName ] ) )
		{
			$result = $defaultValue;
		}
		else
		{
			$result = $namespace[ $varName ];
		}
		return $result;
	}

	public function save( $varName, $value )
	{
		$namespace = self::getNamespace( __CLASS__ );
		$namespace[ $varName ] = $value;
	}

	public function getOptions()
	{
		$options = $this->_optionsMap;
		foreach ( $options as $name )
		{
			$result[ 'session.' . $name ] = $this->getOption( $name );
		}
		return $result;
	}

	public function getOption( $name )
	{
		$varname = 'session.' . strtolower( $name );
		$result = ini_get( $varname );
		return $result;
	}

	public function setOption( $name, $value )
	{
		$varname = strtolower( $name );
		if ( false !== array_search( $name, $this->_optionsMap ) )
		{
			$varname = 'session.' . $varname;
			$res = ini_set( $varname, $value );

			if ( false === $res )
			{
				$message = sprintf( 'Option %s can\'t be change', $name );
				throw new Miao_Session_Exception( $message );
			}
		}
		else
		{
			$message = sprintf( 'Invalid option name %s', $name );
			throw new Miao_Session_Exception( $message );
		}

		return $this;
	}

	public function setOptions( array $options )
	{
		foreach ( $options as $name => $value )
		{
			$this->setOption( $name, $value );
		}
		return $this;
	}

	public function getHandler()
	{
		return $this->_handler;
	}

	public function setHandler( $handler )
	{
		if ( !is_null( $handler ) )
		{
			if ( !$handler instanceof Miao_Session_Handler )
			{
				$message = sprintf( 'Invalid param $handler, must be extend Miao_Session_Handler' );
				throw new Miao_Session_Exception( $message );
			}

			$this->_handler = $handler;
		}
		else
		{
			$this->_handler = new Miao_Session_Handler_Empty();
		}

		$this->_handler->init();

		return $this;
	}

	public function start()
	{
		if ( !$this->isStarted() )
		{
			session_start();
		}
	}

	public function isStarted()
	{
		$res = session_id();
		$result = true;
		if ( empty( $res ) )
		{
			$result = false;
		}
		return $result;
	}

	public function __call( $name, $arguments )
	{
		$callback = 'session_' . $name;
		return call_user_func_array( $callback, $arguments );
	}
}