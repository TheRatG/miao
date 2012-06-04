<?php
class Miao_Form_Control_RadioList extends Miao_Form_Control
{
	/**
	 *
	 * @var array Miao_Form_Control_Radio
	 */
	private $_items = array();

	private $_checked = false;

	public function __construct( $id, array $attributes = array(), $items )
	{
		parent::__construct( $id, $attributes );
		$this->setItems( $items );
	}

	public function setItems( array $items )
	{
		foreach ( $items as $key => $value )
		{
			$control = new Miao_Form_Control_Radio( $this->getId(), $this->getAttributes() );
			$control->setValue( $key );
			$control->setLabel( $value );

			$this->_items[] = $control;
		}
	}

	public function setValue( $value )
	{
		foreach ( $this->_items as $key => $control )
		{
			if ( $control->getValue() === $value )
			{
				$this->_checked = $key;
			}
		}
	}

	public function render()
	{
		$pieces = array();
		foreach ( $this->_items as $key => $control )
		{
			if ( $key === $this->_checked )
			{
				$control->addAttribute( 'checked', 'checked' );
			}
			$pieces[] = $control->render();
		}
		$result = implode( chr( 10 ), $pieces );
		return $result;
	}
}