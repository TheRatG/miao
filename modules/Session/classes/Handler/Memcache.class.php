<?php
class Miao_Session_Handler_Memcache extends Miao_Session_Handler
{
	private $_host;
	private $_port;
	private $_persistent;
	private $_savePath;
	private $_handlerName = 'memcache';

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
		$checkProt = false !== strpos( $this->_host, '://' );
		if ( $checkProt )
		{
			$this->_savePath = $this->_host;
			$this->_savePath .= ( $this->_port ) ? ':' . $this->_port : '';
			$this->_savePath .= sprintf( '?persistent=%d', $this->_persistent );
		}
		else
		{
			$this->_savePath = sprintf( 'tcp://%s:%s?persistent=%d', $this->_host, $this->_port, $this->_persistent );
		}
	}
}