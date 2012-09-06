<?php
/**
 *
 * @author Dmitry Kuznetsov <d.kuznetsov@rbc.ru>
 * @copyright RBC 2012
 * @package
 * $Revision$
 */
abstract class Miao_Session_Access  implements Iterator, ArrayAccess, Countable, Serializable
{
	protected $_position = 0;

	/**
	 * (non-PHPdoc)
	 * @see Iterator::rewind()
	 */
	public function rewind()
	{
		$this->_position = 0;
	}

	/**
	 * (non-PHPdoc)
	 * @see Iterator::current()
	 */
	public function current()
	{
		$data = $this->_sessionGet( $this->_name );
		$count = count( $data );
		reset( $data );
		for ( $i = 0; $i < $count; $i++ )
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
	 * @see Iterator::key()
	 */
	public function key()
	{
		return $this->_position;
	}

	/**
	 * (non-PHPdoc)
	 * @see Iterator::next()
	 */
	public function next()
	{
		++$this->_position;
	}

	/**
	 * (non-PHPdoc)
	 * @see Iterator::valid()
	 */
	public function valid()
	{
		// проверка на существование по позиции
		$count = $this->count();
		if ( $this->_position < $count && $this->_position > 0 )
		{
			return true;
		}
		return false;
	}



	/**
	 * (non-PHPdoc)
	 * @see ArrayAccess::offsetExists()
	 */
	public function offsetExists( $index )
	{
		$data = $this->_sessionGet( $this->_name );
		return isset( $data[ $index ] );
	}

	/**
	 * (non-PHPdoc)
	 * @see ArrayAccess::offsetGet()
	 */
	public function offsetGet( $index )
	{
		$data = $this->_sessionGet( $this->_name );
		if ( $this->offsetExists( $index ) )
		{
			return $data[ $index ];
		}
		return null;
	}

	/**
	 * (non-PHPdoc)
	 * @see ArrayAccess::offsetSet()
	 */
	public function offsetSet( $index, $value )
	{
		$data = $this->_sessionGet( $this->_name );
		if ( is_null( $index ) )
		{
			$data[] = $value;
		}
		else
		{
			$data[ $index ] = $value;
        	}
        	$this->_sessionSet( $this->_name, $data );
	}

	/**
	 * (non-PHPdoc)
	 * @see ArrayAccess::offsetUnset()
	 */
	public function offsetUnset( $index )
	{
		$data = $this->_sessionGet( $this->_name );
		if ( $this->offsetExists( $index ) )
		{
			unset( $data[ $index ] );
			$this->_sessionSet( $this->_name, $data );
		}
	}



	/**
	 * (non-PHPdoc)
	 * @see Countable::count()
	 */
	public function count()
	{
		return count( $this->_sessionGet( $this->_name ) );
	}



	/**
	 * (non-PHPdoc)
	 * @see Serializable::serialize()
	 */
	public function serialize()
	{
		$data = $this->_sessionGet( $this->_name );
		return serialize( $data );
	}

	/**
	 * (non-PHPdoc)
	 * @see Serializable::unserialize()
	 */
	public function unserialize( $data )
	{
		$data = unserialize( $data  );
		$this->_sessionSet( $this->_name, $data );
	}



	/**
	 * Magic setter
	 * @param string $name
	 * @param mixed $value
	 */
	public function __set( $name, $value )
	{
		$data = $this->_sessionGet( $this->_name );
		if ( !$data )
		{
			$data = array();
		}
		$data[ $name ] = $value;
		$this->_sessionSet( $this->_name, $data );
	}

	/**
	 * Magic getter
	 * @param string $name
	 */
	public function __get( $name )
	{
		$data = $this->_sessionGet( $this->_name );
		if ( $data && isset( $data[ $name ] ) )
		{
			return $data[ $name ];
		}
		throw new Miao_Session_Exception_UndefinedVar( 'try to get undefined var "' . $name . '"!' );
	}

	/**
	 * Magic unsetter
	 * @param string $name
	 */
	public function __unset( $name )
	{
		$data = $this->_sessionGet( $this->_name );
		if ( $data && isset( $data[ $name ] ) )
		{
			unset( $data[ $name ] );
			$this->_sessionSet( $this->_name, $data );
			return;
		}
		throw new Miao_Session_Exception_UndefinedVar( 'try to unset undefined var!' );
	}

	/**
	 * Magic method for array_pop() function >=php 5.3
	 *
	 * Example:
	 * $tmp = $session();
	 * array_pop( $tmp );
	 *
	 * @param array|null $data
	 */
	public function __invoke( array $data = null )
	{
		if ( is_null( $data ) )
		{
			return $this->_sessionGet( $this->_name );
		}
		else
		{
			$this->_sessionSet( $this->_name, $data );
		}
	}
}