<?php
class Miao_Office_DataHelper_Messages_Item
{

	private $_type;

	private $_value;

	/**
	 * @return the $_type
	 */
	public function getType()
	{
		return $this->_type;
	}

	/**
	 * @param field_type $type
	 */
	public function setType( $type )
	{
		$this->_type = $type;
	}

	/**
	 * @return the $_value
	 */
	public function getValue()
	{
		return $this->_value;
	}

	/**
	 * @param field_type $value
	 */
	public function setValue( $value )
	{
		$this->_value = $value;
	}

	public function __construct( $value, $type )
	{
		$this->setValue( $value );
		$this->setType( $type );
	}

	public function __toString()
	{
		return $this->getValue();
	}
}