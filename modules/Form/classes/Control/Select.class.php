<?php
class Miao_Form_Control_Select extends Miao_Form_Control
{
	/**
	 *
	 * @var array Miao_Form_Control_Radio
	 */
	private $_items = array();
	private $_selected = false;

	public function __construct( $id, array $attributes = array(), $items = array() )
	{
		parent::__construct( $id, $attributes );
		$this->setItems( $items );
	}

	public function setItems( array $items )
	{
		$this->_items = array();
		foreach ( $items as $key => $value )
		{
			$control = new Miao_Form_Control_SelectOption( $this->getName());
			$control->setValue( $key );
			$control->setLabel( $value );

			$this->_items[] = $control;
		}
	}

	public function setValue( $value )
	{
		foreach ( $this->_items as $key => $control )
		{
			if ( $control->getValue() == $value )
			{
				$this->_selected = $key;
				$this->_value = $value;
			}
		}
	}

	public function render()
	{
		$pieces = array();
		$pieces[] = "<select";
		$pieces[] = sprintf( 'name="%s"', $this->getName() );
		$pieces[] = $this->renderAttributes();
		$pieces[] = ">";

		foreach ( $this->_items as $key => $control )
		{
			if ( $key === $this->_selected )
			{
				$control->addAttribute( 'selected', 'selected' );
			}
			$pieces[] = $control->render();
		}

		$pieces[] = '</select>';

		$result = implode( chr( 10 ), $pieces );
		return $result;
	}
}