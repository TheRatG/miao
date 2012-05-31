<?php
class Miao_Form_Validate_Email extends Miao_Form_Validate_Base
{
	const INVALID = 'emailInvalid';
	const INVALID_FORMAT = 'emailInvalidFormat';

	public function __construct()
	{
		$this->_messageTemplates = array(
			self::INVALID => "Invalid type given. String expected",
			self::INVALID_FORMAT => "'%value%' is no valid email address in the basic format local-part@hostname" );
	}

	public function isValid( $value )
	{
		if ( !is_string( $value ) )
		{
			$this->_error( self::INVALID );
			return false;
		}

		$this->_setValue( $value );

		$result = true;
		if ( false === filter_var( $value, FILTER_VALIDATE_EMAIL ) )
		{
			$this->_error( self::INVALID_FORMAT );
			$result = false;
		}
		return $result;
	}
}