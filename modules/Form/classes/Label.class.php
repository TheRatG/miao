<?php
class Miao_Form_Label
{

	/**
	 *
	 * @var string
	 */
	private $_label;

	/**
	 *
	 * @var string
	 */
	private $_for;

	/**
	 *
	 * @var array
	 */
	protected $_attributes = array();

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
	public function setLabel( $_label, array $attributes = array() )
	{
		$this->_label = $_label;
		$this->_attributes = $attributes;
	}

	public function __construct( $for, $label )
	{
		$this->_for = $for;
		$this->setLabel( $label );
	}

	public function render()
	{
		//<label class="control-label" for="login">Label</label>
		$pieces = array();

		$pieces[] = '<label';
		$pieces[] = sprintf( 'for="%s"', $this->_for );
		foreach ( $this->_attributes as $name => $value )
		{
			$pieces[] = sprintf( '%s="%s"', $name, $value );
		}
		$pieces[] = '>';
		$pieces[] = $this->_label;
		$pieces[] = '</label>';
		$result = implode( ' ', $pieces );
		return $result;
	}

	public function __toString()
	{
		return $this->render();
	}
}
