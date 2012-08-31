<?php
class Miao_Office_Header
{

	/**
	 *
	 * Header list
	 * @var array
	 */
	private $_list = array();

	private $_redirectUrl;

	private $_contentTypeList = array(
		'html' => 'text/html',
		'xml' => 'application/xml',
		'json' => 'application/json',
		'text' => 'plan/text' );

	public function __construct( $contentType = 'text/html', $encoding = 'UTF-8' )
	{
		$this->setContentType( $contentType );
		$this->setEncoding( $encoding );
	}

	public function send()
	{
		foreach ( $list as $item )
		{
			header( $item, true );
		}
		return headers_list();
	}

	public function setEncoding( $encoding )
	{
		$contentType = $this->getContentType();
		if ( empty( $contentType ) )
		{
			$message = sprintf( 'You can\'t set encoding (%s), until not set content type', $encoding );
			throw new Miao_Office_Header_Exception( $message );
		}
		if ( $encoding !== $this->getEncoding() )
		{
			$contentType .= sprintf( '; charset=%s', strtoupper( $encoding ) );
		}
		$this->set( 'Content-type', $contentType );
	}

	public function getEncoding()
	{
		$contentType = $this->getContentType();
		list( , $encoding ) = $this->_parseContentType( $contentType );
		$result = $encoding;
		return $result;
	}

	public function setContentType( $contentType )
	{
		assert( is_string( $contentType ) );

		$result = $contentType;
		if ( array_key_exists( $contentType, $this->_contentTypeList ) )
		{
			$result = $this->_contentTypeList[ $contentType ];
		}
		else if ( false !== ( $key = array_search( $contentType, $this->_contentTypeList ) ) )
		{
			$result = $this->_contentTypeList[ $key ];
		}
		$name = 'Content-type';
		$result = "Content-type: " . $result;
		$this->set( $name, $result );
	}

	public function setRedirectUrl( $redirectUrl )
	{
		$redirectUrl = trim( $redirectUrl );

		if ( empty( $redirectUrl ) )
		{
			$message = sprintf( 'Ivalid param redirectUrl: must be not empty' );
			throw new Miao_Office_Header_Exception( $message );
		}

		$this->_redirectUrl = trim( $redirectUrl );
		$this->set( 'location', sprintf( 'Location: %s', $this->_redirectUrl ) );
	}

	public function getRedirectUrl()
	{
		return $this->_redirectUrl;
	}

	public function set( $name, $value )
	{
		$this->_list[ $name ] = $value;
	}

	public function get( $name )
	{
		if ( !isset( $this->_list[ $name ] ) )
		{
			$message = sprintf( 'Key (%s) doesn\'t exists ', $name );
			throw new Miao_Office_Header_Exception( $message );
		}
		$result = $this->_list[ $name ];
		return $result;
	}

	public function getContentType()
	{
		$name = 'Content-type';
		$result = $this->get( $name );
		return $result;
	}

	public function getList()
	{
		$result = $this->_list;
		return $result;
	}

	public function reset()
	{
		$this->_list = array();
	}

	public function set404()
	{
		$this->set( '404', 'HTTP/1.0 404 Not Found' );
		$this->set( '404_fcgi', 'Status: 404 Not Found' );
	}

	private function _parseContentType( $contentType )
	{
		$ar = explode( '; ', $contentType );
		$contentType = $ar[ 0 ];
		$encoding = '';
		if ( isset( $ar[ 1 ] ) )
		{
			$encoding = str_replace( 'charset=', '', $ar[ 1 ] );
		}
		$result = array( $contentType, $encoding );
		return $result;
	}
}