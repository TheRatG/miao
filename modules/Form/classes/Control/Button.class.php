<?php
class Miao_Form_Control_Button extends Miao_Form_Control_Input
{
	protected $_type = self::TYPE_BUTTON;

	public function render()
	{
		$pieces = array();
		$pieces[] = '<input';
		$pieces[] = sprintf( 'name="%s"', $this->getName() );
		$value = $this->getValue();
		$value = $value ? $value : $this->label()->getLabel();
		$pieces[] = sprintf( 'value="%s"',  $value );
		$pieces[] = $this->_renderType();
		$pieces[] = $this->_renderAttributes();
		$result = trim( implode( ' ', $pieces ) ) . ' />';
		return $result;
	}
}