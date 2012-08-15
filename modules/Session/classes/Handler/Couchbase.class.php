<?php
require ( "Couchbase.php" );

/**
 *
 * @author vpak
 *
 */
class Miao_Session_Handler_Couchbase extends Miao_Session_Handler
{
	/**
	 *
	 * @var Couchbase
	 */
	private $_cb;

	private $_host;
	private $_port;
	private $_couchport;
	private $_secret = 'default';

	private $_lifetime = 0;

	/**
	 *
	 * @return the $_lifetime
	 */
	public function getLifetime()
	{
		return $this->_lifetime;
	}

	/**
	 *
	 * @param $lifetime number
	 */
	public function setLifetime( $lifetime )
	{
		$this->_lifetime = $lifetime;
	}

	/**
	 *
	 * @return the $_host
	 */
	public function getHost()
	{
		return $this->_host;
	}

	/**
	 *
	 * @param $host field_type
	 */
	public function setHost( $host )
	{
		$this->_host = $host;
	}

	/**
	 *
	 * @return the $_port
	 */
	public function getPort()
	{
		return $this->_port;
	}

	/**
	 *
	 * @param $port field_type
	 */
	public function setPort( $port )
	{
		$this->_port = $port;
	}

	/**
	 *
	 * @return the $_couchport
	 */
	public function getCouchport()
	{
		return $this->_couchport;
	}

	/**
	 *
	 * @param $couchport field_type
	 */
	public function setCouchport( $couchport )
	{
		$this->_couchport = $couchport;
	}

	public function __construct( $secret, $host, $port = 11211, $couchport = 8091 )
	{
		$this->_secret = $secret;
		$this->setHost( $host );
		$this->setPort( $port );
		$this->setCouchport( $couchport );
	}

	public function getSid( $id )
	{
		$result = sprintf( 'session/%s/%s', $this->_secret, $id );
		return $result;
	}

	public function init()
	{
		$result = session_set_save_handler( array( $this, "open" ), array(
			$this,
			"close" ), array( $this, "read" ), array( $this, "write" ), array(
			$this,
			"destroy" ), array( $this, "gc" ) );
		$this->_lifetime = ini_get( 'session.cache_expire' );
	}

	public function open()
	{
		$lifetime = ini_get( 'session.gc_maxlifetime' );
		$this->_initCb();

		return true;
	}

	public function read( $id )
	{
		$sId = $this->getSid( $id );
		$result = $this->_cb->get( $sId );
		return $result;
	}

	public function write( $id, $data )
	{
		$sId = $this->getSid( $id );
		$result = $this->_cb->set( $sId, $data, $this->_lifetime );
		return $result;
	}

	public function destroy( $id )
	{
		$sId = $this->getSid( $id );
		$result = $this->_cb->delete( $sId );
		return $result;
	}

	public function gc()
	{
		return true;
	}

	public function close()
	{
		return true;
	}

	public function __destruct()
	{
		session_write_close();
		unset( $this->_cb );
	}

	private function _initCb()
	{
		$this->_cb = new Couchbase();
		$this->_cb->addCouchbaseServer( $this->_host, $this->_port, $this->_couchport );
	}
}