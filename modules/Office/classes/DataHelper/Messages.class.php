<?php
class Miao_Office_DataHelper_Messages extends Miao_Office_DataHelper
{
	const INFO = 'info';
	const ERROR = 'error';
	const SUCCESS = 'success';

	private $_sessionName;

	private $_messages;

	protected function __construct()
	{
		$this->_sessionName = 'Miao_Office_DataHelper_Messages';
	}

	/**
	 * @return Miao_Office_DataHelper_Messages
	 */
	static public function getInstance()
	{
		return parent::_getInstance( __CLASS__ );
	}

	public function add( $message, $type = self::SUCCESS, $key = null )
	{
		$this->_load();
		if ( !is_null( $key ) )
		{
			$this->_messages[ $key ] = $this->_createMessage( $message, $type );
		}
		else
		{
			$this->_messages[] = $this->_createMessage( $message, $type );
		}
		$this->_save();
	}

	public function get( $key = null, $remove = true )
	{
		$this->_load();

		if ( empty( $this->_messages ) )
		{
			$message = sprintf( 'There is no message' );
			throw  new Miao_Office_DataHelper_Exception( $message );
		}

		if ( is_null( $key ) )
		{
			$key = key( $this->_messages );
		}

		if ( !array_key_exists( $key, $this->_messages ) )
		{
			$message = sprintf( 'Invalid key "%s", doesn\'t exists', $key );
			throw  new Miao_Office_DataHelper_Exception( $message );
		}

		$result = $this->_messages[ $key ];
		if ( $remove )
		{
			unset( $this->_messages[ $key ] );
		}

		$this->_save();

		return $result;
	}

	public function getList( $remove = true )
	{
		$this->_load();
		$result = $this->_messages;

		if ( $remove )
		{
			$this->clear();
		}

		return $result;
	}

	public function clear()
	{
		$this->_messages = array();
		$this->_save();
	}

	protected function _save()
	{
		$session = Miao_Session::getInstance();
		$session->saveObject( $this->_sessionName, $this->_messages );
	}

	protected function _load()
	{
		$session = Miao_Session::getInstance();
		$this->_messages = $session->loadObject( $this->_sessionName, array() );
	}

	protected function _createMessage( $message, $type )
	{
		$result = new Miao_Office_DataHelper_Messages_Item( $message, $type );
		return $result;
	}
}