<?php
class Miao_Session_Handler_Memcached extends Miao_Session_Handler
{
	private $_host;
	private $_port;
	private $_persistent;

	private $_savePath;
	private $_handlerName = 'memcached';

	public function __construct( $host, $port = '11211', $persistent = true )
	{
		$this->_host = $host;
		$this->_port = $port;
		$this->_persistent = ( bool ) $persistent;
		$this->_initSavePath();
	}

	public function getSavePath()
	{
		return $this->_savePath;
	}

	public function init()
	{
		$this->_initSavePath();

		ini_set( 'session.save_handler', $this->_handlerName );
		ini_set( 'session.save_path', $this->getSavePath() );
	}

	private function _initSavePath()
	{
		$this->_savePath = sprintf( '%s:%s?persistent=%d', $this->_host, $this->_port, $this->_persistent );
	}
}