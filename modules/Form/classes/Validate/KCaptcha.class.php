<?php
class Miao_Form_Validate_KCaptcha extends Miao_Form_Validate_Base
{
	const INVALID = 'captchaInvalid';

	public function __construct()
	{
		$this->_messageTemplates = array(
			self::INVALID => "Invalid captcha" );
	}

	public function isValid( $value )
	{
		if ( !is_string( $value ) )
		{
			$this->_error( self::INVALID );
			return false;
		}

		$result = false;
		$valueRight = Miao_Session::getInstance()->load( Miao_Form_Control_Captcha::SKEY_NAME, null, true );
		if ( !empty( $valueRight ) )
		{
			if ( $value === $valueRight )
			{
				$result = true;
			}
			else
			{
				$this->_error( self::INVALID );
			}
		}
		return $result;
	}
}