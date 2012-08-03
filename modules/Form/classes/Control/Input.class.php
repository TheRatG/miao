<?php
abstract class Miao_Form_Control_Input extends Miao_Form_Control
{
	const TYPE_BUTTON = 'button';
	const TYPE_checkbox = 'checkbox';
	const TYPE_FILE = 'file';
	const TYPE_HIDDEN = 'hidden';
	const TYPE_IMAGE = 'image';
	const TYPE_PASSWORD = 'password';
	const TYPE_RADIO = 'radio';
	const TYPE_RESET = 'reset';
	const TYPE_SUBMIT = 'submit';
	const TYPE_TEXT = 'text';

	protected $_type;

	public function render()
	{
		$pieces = array();
		$pieces[] = '<input';
		$pieces[] = sprintf( 'name="%s"', htmlspecialchars( $this->getName() ) );
		$pieces[] = sprintf( 'value="%s"', htmlspecialchars( $this->getValue() ) );
		$pieces[] = $this->_renderType();
		$pieces[] = $this->_renderAttributes();
		$result = trim( implode( ' ', $pieces ) ) . ' />';
		return $result;
	}

	protected function _renderType()
	{
		$result = sprintf( 'type="%s"', $this->_type );
		return $result;
	}
}