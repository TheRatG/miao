<?php
class Miao_Form_Control_Textarea extends Miao_Form_Control
{
	public function render()
	{
		$pieces = array();
		$pieces[] = '<textarea';
		$pieces[] = sprintf( 'name="%s"', $this->getName() );
		$pieces[] = $this->renderAttributes();

		$result = trim( implode( ' ', $pieces ) ) . '>';
		$result .= $this->getValue() . '</textarea>';

		return $result;
	}
}