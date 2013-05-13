<?php
class Miao_Form_Control_Captcha extends Miao_Form_Control
{
	const SKEY_NAME = 'session_captcha_value';

	private $_url;

	public function getUrl()
	{
		return $this->_url;
	}

	public function __construct( $name, array $attributes = array(), $url = null )
	{
		if ( empty( $url ) )
		{
			$url = '/?_prefix=Miao_Office&_action=KCaptcha';
		}
		$this->_url = $url;
		parent::__construct( $name, $attributes );
		$this->addValidator( 'KCaptcha' );
	}

	public function image()
	{
		$pieces = array();
		$pieces[] = '<img';
		$pieces[] = sprintf( 'src="%s"', $this->getUrl() );
		$pieces[] = $this->renderAttributes();
		$pieces[] = '/>';
		$result = trim( implode( ' ', $pieces ) );
		return $result;
	}

	public function input()
	{
		$pieces = array();
		$pieces[] = '<input type="text"';
		$pieces[] = sprintf( 'name="%s"', $this->getName() );
		$pieces[] = $this->renderAttributes();
		$pieces[] = '/>';
		$result = trim( implode( ' ', $pieces ) );
		return $result;
	}

	public function render()
	{
		$result = $this->input() . $this->image();
		return $result;
	}
}