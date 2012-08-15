<?php
/**
 * @author vpak
 * @date 2012-08-15 11:06:38
 */
class Miao_Auth_Storage_Session
{
	/**
	 * Default session object member name
	 */
	const MEMBER_DEFAULT = 'storage';

	/**
	 * Object to proxy $_SESSION storage
	 *
	 * @var Zend_Session_Namespace
	 */
	protected $_session;

	/**
	 * Session namespace
	 *
	 * @var mixed
	 */
	protected $_namespace;

	/**
	 * Session object member
	 *
	 * @var mixed
	 */
	protected $_member;

	/**
	 * Sets session storage options and initializes session namespace object
	 *
	 * @param  mixed $namespace
	 * @param  mixed $member
	 * @return void
	 */
	public function __construct( $namespace = self::NAMESPACE_DEFAULT, $member = self::MEMBER_DEFAULT )
	{
		$this->_member = $member;
	}

	/**
	 * Returns the session namespace
	 *
	 * @return string
	 */
	public function getNamespace()
	{
		return $this->_namespace;
	}

	/**
	 * Returns the name of the session object member
	 *
	 * @return string
	 */
	public function getMember()
	{
		return $this->_member;
	}

	/**
	 * Defined by Zend_Auth_Storage_Interface
	 *
	 * @return boolean
	 */
	public function isEmpty()
	{
		return !isset( Miao_Session::getInstance()->checkVarExistence( $this->_member ) );
	}

	/**
	 * Defined by Zend_Auth_Storage_Interface
	 *
	 * @return mixed
	 */
	public function read()
	{
		return Miao_Session::getInstance()->loadObject( $this->_member );
	}

	/**
	 * Defined by Zend_Auth_Storage_Interface
	 *
	 * @param  mixed $contents
	 * @return void
	 */
	public function write( $contents )
	{
		Miao_Session::getInstance()->saveObject( $this->_member, $contents );
	}

	/**
	 * Defined by Zend_Auth_Storage_Interface
	 *
	 * @return void
	 */
	public function clear()
	{
		unset( $this->_session->{$this->_member} );
	}
}