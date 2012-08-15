<?php
class Miao_Session_Namespace implements Iterator, ArrayAccess
{
	private $_data = array();
	private $_namespace = false;
	protected $_position = 0;
	protected $_keys = array();

	static public function getNick( $namespace )
	{
		$result = '__Miao-Session::' . $namespace;
		return $result;
	}

	public function __construct( $namespace )
	{
		$this->_namespace = $namespace;
		$this->_load();
	}

	public function __set( $name, $value )
	{
		$this->_load();

		$this->_data[ $name ] = $value;

		$this->_save();
	}

	public function __get( $name )
	{
		$this->_load();
		$value = false;
		if ( !isset( $this->_data[ $name ] ) )
		{
			$message = sprintf( 'Undefined var %s', $name );
			throw new Miao_Session_Exception_UndefinedVar( $message );
		}

		$value = $this->_data[ $name ];

		return $value;
	}

	public function __isset( $name )
	{
		$this->_load();
		return isset( $this->_data[ $name ] );
	}
	public function __unset( $name )
	{
		$this->_load();
		if ( $this->__isset( $name ) )
		{
			unset( $this->_data[ $name ] );
			$this->_save();
		}
	}

	public function clear()
	{
		$this->_data = array();
		$this->_save();
	}

	protected function _save()
	{
		$nick = self::getNick( $this->_namespace );
		$_SESSION[ $nick ] = serialize( $this->_data );
	}

	protected function _load()
	{
		$nick = self::getNick( $this->_namespace );
		$this->_data = isset( $_SESSION[ $nick ] ) ? unserialize( $_SESSION[ $nick ] ) : false;
		if ( !is_array( $this->_data ) )
		{
			$this->_data = array();
			$this->_save();
		}
	}

	/**
	 * (non-PHPdoc)
	 *
	 * @see Iterator::rewind()
	 */
	public function rewind()
	{
		$this->_position = 0;
	}

	/**
	 * (non-PHPdoc)
	 *
	 * @see Iterator::current()
	 */
	public function current()
	{
		$this->_load();
		$data = $this->_data;
		$count = count( $data );
		reset( $data );
		for( $i = 0; $i < $count; $i++ )
		{
			if ( $i == $this->_position )
			{
				break;
			}
			next( $data );
		}
		return current( $data );
	}

	/**
	 * (non-PHPdoc)
	 *
	 * @see Iterator::key()
	 */
	public function key()
	{
		$keys = array_keys( $this->_data );
		return $keys[ $this->_position ];
	}

	/**
	 * (non-PHPdoc)
	 *
	 * @see Iterator::next()
	 */
	public function next()
	{
		++$this->_position;
	}

	/**
	 * (non-PHPdoc)
	 *
	 * @see Iterator::valid()
	 */
	public function valid()
	{
		// проверка на существование по позиции
		$count = $this->count();
		if ( $this->_position < $count && $this->_position >= 0 )
		{
			return true;
		}
		return false;
	}

	/**
	 * (non-PHPdoc)
	 *
	 * @see Countable::count()
	 */
	public function count()
	{
		$this->_load();
		$result = count( $this->_data );
		return $result;
	}

	public function offsetExists( $offset )
	{
		return $this->__isset( $offset );
	}
	public function offsetGet( $offset )
	{
		return $this->__get( $offset );
	}
	public function offsetSet( $offset, $value )
	{
		$this->__set( $offset, $value );
	}
	public function offsetUnset( $offset )
	{
		$this->__unset( $offset );
	}

}