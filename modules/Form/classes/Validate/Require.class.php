<?php
class Miao_Form_Validate_Require extends Miao_Form_Validate_Base
{
	const IS_EMPTY = 'isEmpty';

	public function __construct()
	{
		$this->_messageTemplates = array(
			self::IS_EMPTY => "Value is required and can't be empty" );
	}

	public function isValid( $value )
	{
		$this->_setValue($value);
		$result = true;
		if ( '' === $value || null === $value || !is_scalar( $value ) )
		{
			$this->_error( self::IS_EMPTY );
			$result = false;
		}
		return $result;
	}
}