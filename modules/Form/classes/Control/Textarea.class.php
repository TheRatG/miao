<?php
class Miao_Form_Control_Textarea extends Miao_Form_Control
{
	public function render()
	{
		$pieces = array();
		$pieces[] = '<textarea';
		$pieces[] = sprintf( 'id="%s"', $this->getId() );
		$pieces[] = sprintf( 'name="%s"', $this->getId() );
		$pieces[] = $this->_renderAttributes();

		$result = trim( implode( ' ', $pieces ) ) . '>';
		$result .= $this->getValue() . '</textarea>';

		return $result;
	}
}