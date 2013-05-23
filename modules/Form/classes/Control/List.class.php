<?php
/**
 * @author vpak
 * @date 2013-05-20 16:31:31
 */
class Miao_Form_Control_List extends Miao_Form_Control implements ArrayAccess, Countable
{
	/**
     * @var string
     */
	protected $_name;

	/**
     * @var Miao_Form_Control
     */
	protected $_prototype;

	/**
     * @var Miao_Form_Control[]
     */
	protected $_items = array();

	/**
     * @var bool
     */
	protected $_forceKeyEnable = false;

	/**
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws Miao_Form_Control_List_Exception
     */
	public function __call( $name, $arguments )
	{
		$result = null;
		if ( method_exists( $this->_prototype, $name ) )
		{
			$result = call_user_func_array( array( $this->_prototype, $name ),
				$arguments );
		}
		else
		{
			$message = sprintf( 'Call to undefined method %s::%s()',
				get_class( $this->_prototype ), $name );
			throw new Miao_Form_Control_List_Exception( $message );
		}
		return $result;
	}

	public function __construct( Miao_Form_Control $prototype, array $values = array() )
	{
		$this->setPrototype( clone $prototype );
		$this->setValue( $values );
		parent::__construct( $this->_prototype->getName() );

		$this->_label = $this->_prototype->_label;
		$this->_attributes = $this->_prototype->_attributes;
	}

	/**
     * @return string
     */
	public function getName()
	{
		return $this->_name;
	}

	/**
     * @return bool $forceKeyEnable
     */
	public function getForceKeyEnable()
	{
		return $this->_forceKeyEnable;
	}

	/**
     * @param boolean $forceKeyEnable
     */
	public function setForceKeyEnable( $forceKeyEnable )
	{
		$this->_forceKeyEnable = $forceKeyEnable;
	}

	/**
     * @param Miao_Form_Control $prototype
     */
	public function setPrototype( Miao_Form_Control $prototype )
	{
		$this->_prototype = $prototype;
	}

	/**
     * @return Miao_Form_Control[]
     */
	public function getItems()
	{
		if ( empty( $this->_items ) )
		{
			$this->offsetSet( null, clone $this->_prototype );
		}
		return $this->_items;
	}

	/**
     * @param array $values
     * @return Miao_Form_Control_List
     */
	public function setValue( $values )
	{
		if ( !empty( $values ) )
		{
			/** @var Miao_Form_Control $control */
			foreach ( $values as $key => $value )
			{
				if ( !$this->offsetExists( $key ) )
				{
					$this->offsetSet( $key, clone $this->_prototype );
				}

				$control = $this->offsetGet( $key );
				$control->setValue( $value );
			}
		}
		return $this;
	}

	/**
     * @return the $_value
     */
	public function getValue()
	{
		$result = array();
		$items = $this->getItems();
		foreach ( $items as $key => $item )
		{
			$result[ $key ] = $item->getValue();
		}
		return $result;
	}

	/**
     * @return string
     */
	public function render()
	{
		$items = $this->getItems();
		$result = array();
		foreach ( $items as $item )
		{
			$result[] = $item->render();
		}
		$result = implode( "", $result );
		return $result;
	}

	/**
     * @return bool
     */
	public function validate( Miao_Form $form = null )
	{
		$result = true;
		$items = $this->getItems();
		foreach ( $items as $control )
		{
			$result = $control->validate( $form ) && $result;
		}
		return $result;
	}

	/**
     * ArrayAccess: BEGIN
     */

	/**
     * @param mixed $offset
     * @return boolean
     */
	public function offsetExists( $offset )
	{
		$result = array_key_exists( $offset, $this->_items );
		return $result;
	}

	/**
     * @param mixed $offset
     * @return Miao_Form_Control
     */
	public function offsetGet( $offset )
	{
		$result = null;
		if ( $this->offsetExists( $offset ) )
		{
			$result = $this->_items[ $offset ];
		}
		return $result;
	}

	/**
     * @param mixed $offset
     * @param Miao_Form_Control $value
     * @return void
     */
	public function offsetSet( $offset, $value )
	{
		if ( is_null( $offset ) )
		{
			$this->_items[] = null;
			$offset = array_pop( array_keys( $this->_items ) );
		}
		$this->_items[ $offset ] = $value;

		$controlKey = $offset;
		if ( !$this->getForceKeyEnable() )
		{
			$controlKey = '';
		}
		$name = $this->_makeName( $controlKey );
		$value->setName( $name );
	}

	/**
     * @param mixed $offset
     * @return void
     */
	public function offsetUnset( $offset )
	{
		if ( $this->offsetExists( $offset ) )
		{
			unset( $this->_items[ $offset ] );
		}
	}

	/**
     * ArrayAccess: END
     */

	/**
     * @return number
     */
	public function count()
	{
		return count( $this->_items );
	}

	protected function _makeName( $key )
	{
		$controlName = $this->_prototype->getName();
		$name = sprintf( '%s[%s]', $controlName, $key );
		return $name;
	}
}