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
		return !$this->getStorage()->isEmpty();
	}

	/**
	 * Returns the identity from storage or null if no identity is available
	 *
	 * @return mixed|null
	 */
	public function getIdentity()
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
	public function clearIdentity()
	{
		$this->getStorage()->clear();
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
		return $this->_adapter->login( $login, $password, $options );
	}

	/**
	 *
	 * @param scalar $login
	 * @return bool
	 */
	public function logout( $login )
	{
		return $this->_adapter->logout( $login );
	}

	/**
	 * Singleton pattern implementation makes "new" unavailable
	 *
	 * @return void
	 */
	protected function __construct()
	{
		$this->_storage = new Miao_Auth_Storage_Session();
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