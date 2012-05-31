<?php
class Miao_Session_Handler_Memcache extends Miao_Session_Handler
{
	private $_host;
	private $_port;
	private $_persistent;

	public function __construct()
	{
		$config = Miao_Config::Libs( __CLASS__ );
		$this->_host = $config->get( 'host' );
		$this->_port = $config->get( 'port' );
		$this->_persistent = $config->get( 'persistent' );

		$this->init();
	}

	public function init()
	{
		$session_save_path = sprintf( 'tcp://%s:%s?persistent=%d', $this->_host, $this->_port, $this->_persistent );
		ini_set( 'session.save_handler', 'memcache' );
		ini_set( 'session.save_path', $session_save_path );
	}
}