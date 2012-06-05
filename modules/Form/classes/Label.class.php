<?php
class Miao_Form_Label
{

	/**
	 *
	 * @var string
	 */
	private $_label;

	/**
	 * @return the $_label
	 */
	public function getLabel()
	{
		return $this->_label;
	}

	/**
	 * @param string $_label
	 */
	public function setLabel( $_label )
	{
		$this->_label = $_label;
	}

	public function __construct( $label )
	{
		$this->setLabel( $label );
	}

	public function __toString()
	{
		return $this->getLabel();
	}
}
