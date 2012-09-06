<?php
class Miao_Office_Action_KCaptcha extends Miao_Office_Action
{

	public function execute()
	{
		$captcha = new Miao_Form_KCaptcha();
		$captcha->generate();
		$value = $captcha->getKeyString();
		Miao_Session::getInstance()->save( Miao_Form_Control_Captcha::SKEY_NAME, $value );
	}
}