<?php
abstract class Miao_Office_DataHelper_Url
{
	protected $_host;
	protected $_pics;

	abstract protected function _init();

	/**
	 * @return the $_host
	 */
	public function getHost()
	{
		return $this->_host;
	}

	/**
	 * @param field_type $host
	 */
	public function setHost( $host )
	{
		$scheme = parse_url( $host, PHP_URL_SCHEME );
		$result = $scheme ? $host : 'http://' . $host;
		$this->_host = $result;
	}

	/**
	 * @return the $_pics
	 */
	public function getPics()
	{
		return $this->_pics;
	}

	/**
	 * @param field_type $pics
	 */
	public function setPics( $pics )
	{
		$scheme = parse_url( $pics, PHP_URL_SCHEME );
		$result = $scheme ? $pics : 'http://' . $pics;
		$this->_pics = $result;
	}

	final public function __construct()
	{
		$this->_init();

		$this->_checkMandatoryParam( 'host', $this->getHost() );
		$this->_checkMandatoryParam( 'pics', $this->getPics() );
	}

	static protected function _getInstance( $className )
	{
		$index = 'dh:jscsslist:' . $className;
		$result = null;
		if ( !Miao_Registry::isRegistered( $index ) )
		{
			$result = new $className();

			if ( !$result instanceof Miao_Office_DataHelper_Url )
			{
				throw new Miao_Office_DataHelper_Url_Exception( sprintf(
					'Invalid class %s: must be instance of Miao_Office_DataHelper_Url',
				$className ) );
			}
			if ( !$result instanceof Miao_Office_DataHelper_Url_Interface )
			{
				throw new Miao_Office_DataHelper_Url_Exception( sprintf(
					'Invalid class %s: must be implement of Miao_Office_DataHelper_Url_Interface',
				$className ) );
			}

			Miao_Registry::set( $index, $result );
		}
		else
		{
			$result = Miao_Registry::get( $index );
		}
		return $result;
	}

	static public function queryString( $params, $name = null )
	{
		$ret = "";
		foreach ( $params as $key => $val )
		{
			if ( is_array( $val ) )
			{
				if ( $name == null )
				$ret .= self::queryString( $val, $key );
				else
				$ret .= self::queryString( $val, $name . "[$key]" );
			}
			else
			{
				if ( $name != null )
				$ret .= $name . "[$key]" . "=$val&";
				else
				$ret .= "$key=$val&";
			}
		}
		$ret = rtrim( $ret, '&' );
		return $ret;
	}

	public function src( $path, $query= '' )
	{
		$result = $this->buildUrl( $this->getPics(), $path, $query );
		return $result;
	}

	public function href( $path, $query = '', $fragment = '' )
	{
		$result = $this->buildUrl( $this->getHost(), $path, $query, $fragment );
		return $result;
	}

	/**
	 *
	 * Lite version of <b>http_build_url</b>
	 *
	 * @param unknown_type $url
	 * @param unknown_type $path
	 * @param unknown_type $query
	 * @param unknown_type $fragment
	 */
	static public function buildUrl( $url, $path, $query = '', $fragment = '' )
	{
		$resultUrl = array(
			'scheme' => '',
			'host' => '',
			'port' => '',
			'user' => '',
			'pass' => '',
			'path' => '',
			'query' => '',
			'fragment' => '' );

		$orginalUrl = parse_url( $url );
		$resultUrl = array_merge( $resultUrl, $orginalUrl );

		if ( !empty( $path ) )
		{
			if ( $path[ 0 ] == '/' )
			{
				$resultUrl[ 'path' ] = $path;
			}
			else
			{
				$resultUrl[ 'path' ] .= '/' . $path;
			}
		}

		$result = ( $resultUrl[ 'scheme' ] ? $resultUrl[ 'scheme' ] : 'http' ) . '://';
		if ( !empty( $resultUrl[ 'user' ] ) )
		{
			$result .= $resultUrl[ 'user' ];
			if ( !empty( $resultUrl[ 'pass' ] ) )
			{
				$result .= ':' . $resultUrl[ 'pass' ];
			}
			$result .= '@';
		}
		$result .= $resultUrl[ 'host' ];
		if ( !empty( $resultUrl[ 'port' ] ) )
		{
			$result .= ':' . $resultUrl[ 'port' ];
		}
		if ( !empty( $resultUrl[ 'path' ] ) )
		{
			$result .= $resultUrl[ 'path' ];
		}

		// query -------------------
		if ( isset( $orginalUrl[ 'query' ] ) )
		{
			parse_str( $orginalUrl[ 'query' ], $orginalQuery );
		}
		else
		{
			$orginalQuery = array();
		}
		$resultQuery = array();
		if ( is_string( $query ) )
		{
			parse_str( $query, $resultQuery );
		}
		else if ( is_array( $query ) )
		{
			$resultQuery = $query;
		}
		if ( is_array( $orginalQuery ) )
		{
			$resultQuery = array_replace_recursive( $orginalQuery,
			$resultQuery );
		}
		$resultUrl[ 'query' ] = self::queryString( $resultQuery );
		if ( !empty( $resultUrl[ 'query' ] ) )
		{
			$result .= '?' . $resultUrl[ 'query' ];
		}
		// -------------------


		// fragment -------------------
		if ( !empty( $fragment ) )
		{
			$fragment = ltrim( $fragment, '#' );
			$result .= '#' . $fragment;
		}
		else if ( !empty( $resultUrl[ 'fragment' ] ) )
		{
			$result .= '#' . $resultUrl[ 'fragment' ];
		}
		// -------------------


		$check = self::_check( $result );
		if ( !$check )
		{
			$msg = sprintf( 'Invalid url %s', $result );
			throw new Miao_Office_DataHelper_Url_Exception( $msg );
		}

		return $result;
	}

	private function _checkMandatoryParam( $paramName, $url )
	{
		if ( empty( $url ) )
		{
			$exceptionMessage = sprintf(
				'You must be initialize %s before using this class', $paramName );
			throw new Miao_Office_DataHelper_Url_Exception( $exceptionMessage );
		}
		$check = self::_check( $url );
		if ( !$check )
		{
			$exceptionMessage = sprintf( 'Invalid param %s - %s', $paramName,
			$url );
			throw new Miao_Office_DataHelper_Url_Exception( $exceptionMessage );
		}
	}

	static protected function _check( $url )
	{
		$check = filter_var( $url, FILTER_VALIDATE_URL );

		$result = true;
		if ( !$check )
		{
			$result = false;
		}

		return $result;
	}
}