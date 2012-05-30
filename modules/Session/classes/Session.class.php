<?php
/**
 * Singleton class provides general functionality for working with PHP Sessions.
 *
 */
class Miao_Session
{
	/**
	 * instance of Miao_Session
	 *
	 * @var Miao_Session
	 */
	protected static $_instance;

	/**
	 *
	 * @var unknown_type
	 */
	private $_handler;

	/**
	 * Constructor of Miao_Session
	 *
	 * @throws Miao_Session_Exception_OnSessionsNotSupported
	 */
	protected function __construct()
	{
		$this->_handler = Miao_Session_Handler::factory();

		if ( !session_id() )
		{
			session_start();
		}
		if ( !isset( $_SESSION ) )
		{
			throw new Miao_Session_Exception_OnSessionsNotSupported();
		}
	}

	/**
	 * Getting a single instance of Miao_Session
	 *
	 * @return Miao_Session
	 */
	static public function getInstance()
	{
		if ( !isset( self::$_instance ) )
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Method-wrapper for session_destroy() method.
	 * Destroys all data registered to a session.
	 */
	public function destroySession()
	{
		session_destroy();
	}

	/**
	 * Method-wrapper for session_id() method.
	 * Get the current session id.
	 *
	 * @return string
	 */
	public function getSessionId()
	{
		return session_id();
	}

	/**
	 * Saving scalar variable in $_SESSION
	 *
	 * @param string $varName
	 * @param mixed $value
	 */
	public function saveScalar( $varName, $value )
	{
		$_SESSION[ $varName ] = $value;
	}

	/**
	 * Saving object in $_SESSION
	 *
	 * @param string $varName
	 * @param mixed $value
	 */
	public function saveObject( $varName, $value )
	{
		if ( !is_scalar( $value ) )
		{
			$_SESSION[ $varName ] = serialize( $value );
		}
		else
		{
			throw new Miao_Session_Exception( 'Invalid param $value, must be object' );
		}
	}

	/**
	 * Get scalar variable from $_SESSION
	 *
	 * @param string $varName
	 * @param mixed $default
	 * @return mixed
	 */
	public function loadScalar( $varName, $defaultValue = null, $useNullAsDefault = false )
	{
		$result = null;
		if ( false === $this->_checkVarExistence( $varName ) )
		{
			if ( ( null === $defaultValue ) && ( false === $useNullAsDefault ) )
			{
				throw new Miao_Session_Exception_OnVariableNotExists( $varName );
			}
			$result = $defaultValue;
		}
		else if ( empty( $_SESSION[ $varName ] ) )
		{
			$result = $defaultValue;
		}
		else
		{
			$result = $_SESSION[ $varName ];
		}
		return $result;
	}

	/**
	 * Get object from $_SESSION
	 *
	 * @param string $varName
	 * @return mixed
	 */
	public function loadObject( $varName, $defaultValue = null, $useNullAsDefault = false )
	{
		$result = $this->loadScalar( $varName, $defaultValue, $useNullAsDefault );
		if ( !is_null( $result ) && is_scalar( $result ) )
		{
			$result = unserialize( $result );
		}
		return $result;
	}

	/**
	 * Проверяет наличие перемнной в массиве $_SESSION
	 *
	 * @param string $varName
	 * @return true, если есть, false - в противном случае
	 */
	protected function _checkVarExistence( $varName )
	{
		if ( !array_key_exists( $varName, $_SESSION ) )
		{
			return false;
		}
		return true;
	}
}
