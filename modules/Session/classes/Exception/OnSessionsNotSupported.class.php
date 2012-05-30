<?php
class Miao_Session_Exception_OnSessionsNotSupported extends Miao_Session_Exception
{
	public function __construct()
	{
		parent::__construct( 'Can\'t instantinate new session instance' );
	}
}
