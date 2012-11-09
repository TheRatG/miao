<?php
class Miao_Form_Validate_RequireFile extends Miao_Form_Validate_Base
{
	const IS_EMPTY = 'isEmpty';

	public function __construct()
	{
		$this->_messageTemplates = array(
			self::IS_EMPTY => "Value is required and can't be empty" );
	}

	public function isValid( $value )
	{
		$this->_setValue( $value );
		$result = true;
		if ( empty( $value ) )
		{
			$this->_error( self::IS_EMPTY );
			$result = false;
		}
		else if ( 0 !== $value[ 'error' ] )
		{
			$this->_error( $value[ 'error' ] );
			$result = false;
		}
		return $result;
	}
}