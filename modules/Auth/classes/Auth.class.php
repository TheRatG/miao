<?php
class Miao_Auth
{

	/**
	 * instance of Miao_Auth
	 *
	 * @var Miao_Auth
	 */
	protected static $_instance;

	/**
	 * Persistent storage handler
	 *
	 * @var Miao_Auth_Storage_Interface
	 */
	protected $_storage = null;

	/**
	 *
	 * @var Miao_Auth_Adapter_Interface
	 */
	protected $_adapter = null;

	protected $_check = null;

	/**
	 * @return Miao_Auth
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
	 * @return the $storage
	 */
	public function getStorage()
	{
		return $this->_storage;
	}

	/**
	 * @param Miao_Auth_Storage_Interface $storage
	 */
	public function setStorage( Miao_Auth_Storage_Interface $storage )
	{
		$this->_storage = $storage;
	}

	/**
	 * @return the $_adapter
	 */
	public function getAdapter()
	{
		if ( is_null( $this->_adapter ) )
		{
			$message = 'Adapter was not define. Check your "miao.xml" section Auth';
			throw new Miao_Auth_Adapter_Exception( $message );
		}
		return $this->_adapter;
	}

	/**
	 * @param Miao_Auth_Adapter_Interface $adapter
	 */
	public function setAdapter( Miao_Auth_Adapter_Interface $adapter )
	{
		$this->_adapter = $adapter;
	}

	/**
	 * Returns true if and only if an identity is available from storage
	 *
	 * @return boolean
	 */
	public function hasIdentity()
	{
		$result = false;
		$identity = $this->getIdentity();
		if ( $identity )
		{
			$result = true;
		}
		return $result;
	}

	/**
	 * Returns the identity from storage or null if no identity is available
	 *
	 * @return mixed|null
	 */
	public function getIdentity()
	{
		$result = null;
		$authRes = $this->getResult();
		if ( !$authRes )
		{
			$authRes = $this->getAdapter()->restore();
			if ( $authRes && $authRes instanceof Miao_Auth_Result && $authRes->isValid() )
			{
				$this->getStorage()->write( $authRes );
			}
		}
		if ( $authRes && $authRes instanceof Miao_Auth_Result )
		{
			$check = $this->_check( $authRes );
			if ( $check )
			{
				$result = $authRes->getIdentity();
			}
			else
			{
				$this->clearResult();
			}
		}
		return $result;
	}

	/**
	 *
	 * @return Miao_Auth_Result|NULL
	 */
	public function getResult()
	{
		$storage = $this->getStorage();
		if ( $storage->isEmpty() )
		{
			return null;
		}
		return $storage->read();
	}

	/**
	 * Clears the identity from persistent storage
	 *
	 * @return void
	 */
	public function clearResult()
	{
		$this->getStorage()->clear();
		$this->_check = null;
	}

	/**
	 *
	 * @param scalar $login
	 * @param scalar $password
	 * @param array $options
	 * @return Miao_Auth_Result
	 */
	public function login( $login, $password, array $options = array() )
	{
		$result = $this->_adapter->login( $login, $password, $options );
		if ( $this->hasIdentity() )
		{
			$this->clearResult();
		}
		if ( $result->isValid() )
		{
			$this->getStorage()->write( $result );
		}
		return $result;
	}

	/**
	 *
	 * @return bool
	 */
	public function logout()
	{
		$result = $this->_adapter->logout( $this->getResult() );
		$this->clearResult();
	}

	protected function _check( $authResult )
	{
		if ( is_null( $this->_check ) )
		{
			$this->_check = $this->_adapter->check( $authResult );
		}
		return $this->_check;
	}

	/**
	 * Singleton pattern implementation makes "new" unavailable
	 *
	 * @return void
	 */
	protected function __construct()
	{
		$this->_storage = new Miao_Auth_Storage_Session();

		$config = Miao_Config::Libs( __CLASS__, false );
		if ( $config )
		{
			$adapterClassName = $config->get( 'adapter' );
			$this->setAdapter( new $adapterClassName() );
		}
	}

	/**
	 * Singleton pattern implementation makes "clone" unavailable
	 *
	 * @return void
	 */
	protected function __clone()
	{
	}
}