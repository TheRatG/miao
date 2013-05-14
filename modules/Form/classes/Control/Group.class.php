<?php
/**
 * @author vpak
 * @date 2013-05-13 17:28:34
 */
class Miao_Form_Control_Group extends Miao_Form_Control
{
	/**
	 *
	 * @var bool
	 */
	protected $_forceKeyEnable;

	/**
	 *
	 * @var array
	 */
	protected $_controlPrototypeList = array();

	/**
	 *
	 * @var array Miao_Form_Control_Group_Item
	 */
	protected $_itemList = array();

	/**
	 * @return the $_forceKeyEnable
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

	public function addControl( Miao_Form_Control $control )
	{
		$this->_controlPrototypeList[ $control->getName() ] = $control;
		return $this;
	}

	public function getItemList()
	{
		if ( empty( $this->_itemList ) )
		{
			$this->addItem();
		}
		return $this->_itemList;
	}

	/**
	 * @return the $_value
	 */
	public function getValue()
	{
		$itemList = $this->getItemList();
		$result = array();

		$nameList = array_keys( $this->_controlPrototypeList );
		foreach ( $nameList as $name )
		{
			$result[ $name ] = $this->getValuesByName( $name );
		}
		return $result;
	}

	public function getValuesByName( $name )
	{
		$itemList = $this->getItemList();
		$result = array();
		foreach ( $itemList as $key => $item )
		{
			$result[ $key ] = $item->$name->getValue();
		}
		return $result;
	}

	public function error()
	{
		return $this->_validator;
	}

	public function validate()
	{
		$this->_isValid = $this->_validator->isValid( $this->getValue() );
		return $this->_isValid;
	}

	/**
	 * @param array $value
	 */
	public function setValue( array $value )
	{
		if ( !empty( $value ) )
		{
			$this->_value = $value;
			$itemList = $this->getItemList();
			foreach ( $this->_value as $key => $value )
			{
				if ( !array_key_exists( $key, $this->_controlPrototypeList ) || !is_array(
					$value ) )
				{
					continue;
				}

				foreach ( $value as $itemKey => $itemValue )
				{
					if ( !array_key_exists( $itemKey, $this->_itemList ) )
					{
						$this->addItem( $itemKey );
					}
					$item = $this->_itemList[ $key ];
					$item->setValue( $itemValue );
				}
			}
		}
		return $this;
	}

	public function clearValue()
	{
		$this->_value = array();
		unset( $this->_itemList );
	}

	public function render()
	{
		$result = array();
		$itemList = $this->getItemList();

		foreach ( $itemList as $item )
		{
			$result[] = $item->render();
		}

		$result = implode( "", $result );
		return $result;
	}

	public function addItem( $key = null )
	{
		if ( is_null( $key ) )
		{
			$this->_itemList[] = null;
			$key = array_pop( array_keys( $this->_itemList ) );
		}

		$item = new Miao_Form_Control_Group_Item( $this );
		foreach ( $this->_controlPrototypeList as $control )
		{
			$controlKey = $key;
			if ( !$this->getForceKeyEnable() )
			{
				$controlKey = '';
			}
			$item->addControl( $controlKey, $control );
		}

		$this->_itemList[ $key ] = $item;
		return $this;
	}

	public function __destruct()
	{
		unset( $this->_itemList );
		unset( $this->_controlPrototypeList );
	}
}