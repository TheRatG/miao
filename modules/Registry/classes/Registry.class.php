<?php
/**
 * Generic storage class helps to manage global data.
 *
 * @category   Miao
 * @package    Miao_Registry
 */
class Miao_Registry extends ArrayObject
{
	/**
	 * Registry object provides storage for shared objects.
	 * @var Miao_Registry
	 */
	private static $_registry = null;

	/**
	 * Retrieves the default registry instance.
	 *
	 * @return Miao_Registry
	 */
	public static function getInstance()
	{
		if ( self::$_registry === null )
		{
			self::$_registry = new self();
		}
		return self::$_registry;
	}

	/**
	 * Unset the default registry instance.
	 * Primarily used in tearDown() in unit tests.
	 * @returns void
	 */
	public static function unsetInstance()
	{
		self::$_registry = null;
	}

	/**
	 * getter method, basically same as offsetGet().
	 *
	 * This method can be called from an object of type Miao_Registry, or it
	 * can be called statically.  In the latter case, it uses the default
	 * static instance stored in the class.
	 *
	 * @param string $index - get the value associated with $index
	 * @return mixed
	 * @throws Miao_Registry_Exception if no entry is registerd for $index.
	 */
	public static function get( $index )
	{
		$instance = self::getInstance();

		if ( !$instance->offsetExists( $index ) )
		{
			throw new Miao_Registry_Exception( "No entry is registered for key '$index'" );
		}

		return $instance->offsetGet( $index );
	}

	/**
	 * setter method, basically same as offsetSet().
	 *
	 * This method can be called from an object of type Miao_Registry, or it
	 * can be called statically.  In the latter case, it uses the default
	 * static instance stored in the class.
	 *
	 * @param string $index The location in the ArrayObject in which to store
	 * the value.
	 * @param mixed $value The object to store in the ArrayObject.
	 * @return void
	 */
	public static function set( $index, $value )
	{
		$instance = self::getInstance();
		$instance->offsetSet( $index, $value );
	}

	/**
	 * Returns TRUE if the $index is a named value in the registry,
	 * or FALSE if $index was not found in the registry.
	 *
	 * @param  string $index
	 * @return boolean
	 */
	public static function isRegistered( $index )
	{
		if ( self::$_registry === null )
		{
			return false;
		}
		return self::$_registry->offsetExists( $index );
	}

	public static function toArray()
	{
		return self::$_registry;
	}

	/**
	 * Constructs a parent ArrayObject with default
	 * ARRAY_AS_PROPS to allow acces as an object
	 *
	 * @param array $array data array
	 * @param integer $flags ArrayObject flags
	 */
	public function __construct( $array = array(), $flags = parent::ARRAY_AS_PROPS )
	{
		parent::__construct( $array, $flags );
	}

	/**
	 * @param string $index
	 * @returns mixed
	 *
	 */
	public function offsetExists( $index )
	{
		return array_key_exists( $index, $this );
	}
}