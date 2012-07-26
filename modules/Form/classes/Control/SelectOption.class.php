<?php
class Miao_Form_Control_SelectOption extends Miao_Form_Control_Input
{
	//protected $_type = self::TYPE_OPTION;

	public function render()
	{
		$pieces = array();
		$pieces[] = '<option';
		$pieces[] = sprintf( 'value="%s"', htmlspecialchars( $this->getValue() ) );
		$pieces[] = $this->_renderAttributes();
		$pieces[] = '>';
		$pieces[] = $this->label();
		$result = trim( implode( ' ', $pieces ) ) . '</option>';
		
		return $result;
	}
}