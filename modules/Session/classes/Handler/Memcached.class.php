<?php
class Miao_Session_Handler_Memcached extends Miao_Session_Handler
{
	private $_host;
	private $_port;
	public function __construct()
	{
		$config = Miao_Config::Libs( __CLASS__ );
		$this->_host = $config->get( 'host' );
		$this->_port = $config->get( 'port' );

		$this->init();
	}
	public function init()
	{
		$session_save_path = sprintf( '%s:%s', $this->_host, $this->_port );
		ini_set( 'session.save_handler', 'memcached' );
		ini_set( 'session.save_path', $session_save_path );
	}
}