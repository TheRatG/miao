<?php
/**
 * @author vpak
 *
 */
class Miao_Router_Rule
{
	const TYPE_VIEW = 'view';
	const TYPE_ACTION = 'action';
	const TYPE_VIEWBLOCK = 'viewblock';
	private $_magicMap = array(
		self::TYPE_VIEW => '_view',
		self::TYPE_ACTION => '_action',
		self::TYPE_VIEWBLOCK => '_viewBlock' );
	private $_type;
	private $_name;
	private $_rule;
	private $_prefix;
	private $_desc;
	private $_method = 'GET';
    private $_norewrite = false;

	/**
	 *
	 * @var array Miao_Router_Rule_Validator
	 */
	private $_validators;
	private $_parts;
	private $_params = array();

	/**
	 *
	 * @param string $type
	 * @param string $name
	 * @param string $rule
	 * @param array $validators
	 */
	public function __construct( $prefix, $type, $name, $rule, $method = '', array $validators = array(), $desc = '', $norewrite = false )
	{
		$this->setPrefix( $prefix );
		$this->setType( $type );
		$this->setName( $name );
		$this->setMethod( $method );
		$this->setRule( $rule );
		$this->setDesc( $desc );
        $this->setNorewrite( $norewrite );
		$this->_init( $validators );
	}

	/**
	 *
	 * @param array $config
	 * @return Miao_Router_Rule
	 */
	static public function factory( array $config )
	{
		$prefix = Miao_Router::checkAndReturnParam( $config, 'prefix', '' );
		$type = Miao_Router::checkAndReturnParam( $config, 'type' );
		$name = Miao_Router::checkAndReturnParam( $config, 'name' );
		$rule = Miao_Router::checkAndReturnParam( $config, 'rule' );
		$method = Miao_Router::checkAndReturnParam( $config, 'method', '' );
		$desc = Miao_Router::checkAndReturnParam( $config, 'desc', '' );
		$validators = Miao_Router::checkAndReturnParam( $config, 'validators',
			array() );
        $norewrite = Miao_Router::checkAndReturnParam( $config, 'norewrite', '' );

		$result = new self( $prefix, $type, $name, $rule, $method, $validators, $desc, $norewrite );
		return $result;
	}

	public function match( $uri, $method = null )
	{
		if ( empty( $method ) )
		{
			$method = Miao_Router::getRequestMethod();
		}

		$result = false;
		if ( $this->_checkMethod( $method ) )
		{
			$parts = explode( '/', trim( $uri, '/' ) );
			$result = array(
				$this->_getOfficeTypeParamName() => $this->getName() );

			$cnt = count( $this->_validators );
			$partsIterator = 0;
			for( $i = 0; $i < $cnt; $i++ )
			{
				$validator = $this->_validators[ $i ];
				if ( $validator instanceof Miao_Router_Rule_Validator_Regexp )
				{
					$slash = $validator->getSlash();
					$part = implode( '/',
						array_slice( $parts, $partsIterator, $slash + 1 ) );
					$partsIterator += $slash + 1;
				}
				else
				{
					$part = isset( $parts[ $partsIterator ] ) ? $parts[ $partsIterator ] : '';
					$partsIterator++;
				}
				$check = $validator->test( $part );
				if ( false == $check )
				{
					$result = $check;
					break;
				}
				$paramIndex = $validator->getId();
				if ( $paramIndex )
				{
					$result[ $paramIndex ] = $part;
				}
			}
			if ( count( $parts ) > $partsIterator )
			{
				$result = false;
			}
		}
		return $result;
	}

	/**
	 * @return the $_prefix
	 */
	public function getPrefix()
	{
		return $this->_prefix;
	}


	/**
	 * @param string $prefix
	 */
	public function setPrefix( $prefix )
	{
		$this->_prefix = $prefix;
	}

    public function setNorewrite( $norewrite )
    {
        $this->_norewrite = (bool)$norewrite;
    }


	/**
	 * @return the $_prefix
	 */
	public function getDesc()
	{
		return $this->_desc;
	}

	/**
	 * @param string $prefix
	 */
	public function setDesc( $desc )
	{
		$this->_desc = $desc;
	}

	/**
	 * @return the $type
	 */
	public function getType()
	{
		return $this->_type;
	}

	/**
	 * @param string $type
	 */
	public function setType( $type )
	{
		$type = strtolower( $type );
		if ( self::TYPE_ACTION !== $type && self::TYPE_VIEW !== $type && self::TYPE_VIEWBLOCK !== $type )
		{
			$message = sprintf( 'Invalid route type: %s', $type );
			throw new Miao_Router_Rule_Exception( $message );
		}
		$this->_type = $type;
	}

	/**
	 * @return the $name
	 */
	public function getName()
	{
		return $this->_name;
	}

	/**
	 * @param string $name
	 */
	public function setName( $name )
	{
		$this->_name = $name;
	}

	/**
	 * @return the $rule
	 */
	public function getRule()
	{
		return $this->_rule;
	}

	/**
	 * @param string $rule
	 */
	public function setRule( $rule )
	{
		$this->_rule = trim( $rule, '/' );
	}

	/**
	 * @return the $_method
	 */
	public function getMethod()
	{
		if ( empty( $this->_method ) )
		{
			$this->_method = 'GET';
		}
		return $this->_method;
	}

	/**
	 * @param string $method
	 */
	public function setMethod( $method )
	{
		$this->_method = $method;
	}

	/**
	 * @return the $_params
	 */
	public function getParams()
	{
		return $this->_params;
	}

	public function getValidators()
	{
		return $this->_validators;
	}

	public function makeUrl( array $params = array(), $method = null )
	{
		if ( empty( $method ) )
		{
			$metod = Miao_Router::getRequestMethod();
		}

		$uri = array();
		$parts = $this->_parts;
		foreach ( $parts as $key => $paramName )
		{
			if ( $this->_isParam( $paramName ) )
			{
				$index = substr( $paramName, 1 );
				if ( isset( $params[ $index ] ) )
				{
					$uri[] = $params[ $index ];
					unset( $params[ $index ] );
				}
				else
				{
					$message = sprintf(
						'Require param (%s) does not exists in $params', $index );
					throw new Miao_Router_Rule_Exception( $message );
				}
			}
			else
			{
				$uri[] = $paramName;
			}
		}
		$uri = implode( '/', $uri );
		$check = $this->match( $uri, $method );
		if ( false === $check )
		{
			$message = sprintf( 'Uri maked (%s) but did not validate', $uri );
			throw new Miao_Router_Rule_Exception( $message );
		}
		$query = http_build_query( $params );
		if ( !empty( $query ) )
		{
			$uri .= '?' . http_build_query( $params );
		}
		return $uri;
	}

	protected static $_rewriteRuleModeMasks = array(
		'apache' => array(
			'mask' => '^%s%s$',
			'rewrite' => '%s?%s',
			'start' => 'RewriteRule',
			'flags' => '[L,QSA]',
			'index' => 'index.php' ),
		'nginx' => array(
			'mask' => '"^/?%s%s$"',
			'rewrite' => '/%s?%s',
			'start' => 'rewrite',
			'flags' => 'break;',
			'index' => 'index.php' ) );

	public function makeRewrite( $mode = 'apache', $addDesc = true )
	{
		if ( !in_array( $mode, array_keys( self::$_rewriteRuleModeMasks ) ) )
		{
			throw new Miao_Router_Rule_Exception( sprintf(
				'Bad rewrite mode: %s', $mode ) );
		}

        if ( $this->_norewrite )
        {
            $rule = sprintf( '# rule asks to skip it /%s', $this->_rule  );
            return $rule;
        }

		$validators = $this->getValidators();
		$url = array();
		$params = array();
		$j = 1;
		foreach ( $this->_parts as $k => $part )
		{
			$pattern = $validators[ $k ]->getPattern();
			if ( $this->_isParam( $part ) && !empty( $pattern ) )
			{
				$part = substr( $part, 1 );
				$params[ $part ] = '$' . $j++;

				if ( false !== strpos($pattern, '('))
				{
					$url[] = $pattern;
				}
				else
				{
					$url[] = '(' . $pattern . ')';
				}
			}
			else
			{
				$url[] = $part;
			}
		}

		if ( $mode == 'nginx' && count( $params ) > 9 )
		{
			$rule = sprintf(
				'# error happened while generating rewrite for /%s (too many params)',
				$this->_rule );
		}
		else
		{
			$params[ $this->_magicMap[ $this->_type ] ] = $this->_name;

			/** @fixme */
			$suffix = substr( $this->_rule, -1 ) == '/' ? '/' : '';

			$mask = sprintf( self::$_rewriteRuleModeMasks[ $mode ][ 'mask' ],
				implode( '/', $url ), $suffix );
			$rewrite = sprintf(
				self::$_rewriteRuleModeMasks[ $mode ][ 'rewrite' ],
				self::$_rewriteRuleModeMasks[ $mode ][ 'index' ],
				str_replace( '%24', '$', http_build_query( $params ) ) );
			$start = self::$_rewriteRuleModeMasks[ $mode ][ 'start' ];
			$flags = self::$_rewriteRuleModeMasks[ $mode ][ 'flags' ];

			$desc = $addDesc ? sprintf( "# %s:%s%s\n", $this->_type,
				$this->_name, $this->getDesc() ? ' ' . $this->getDesc() : '' ) : '';
			$rule = sprintf( '%s%s %s %s %s', $desc, $start, $mask, $rewrite,
				$flags );
		}
		return $rule;
	}

	protected function _checkMethod( $method )
	{
		$ruleMethod = $this->getMethod();
		$result = false;
		//because head and get in Office module equals
		if ( $method == $ruleMethod || ( 'HEAD' == $method && 'GET' == $ruleMethod ) )
		{
			$result = true;
		}
		return $result;
	}

	protected function _isParam( $str )
	{
		return ':' == $str[ 0 ];
	}

	protected function _getOfficeTypeParamName()
	{
		$result = '_' . $this->getType();
		return $result;
	}

	protected function _init( $validators )
	{
		$rule = $this->getRule();
		$parts = explode( '/', $rule );
		foreach ( $parts as $value )
		{
			if ( $value && ':' == $value[ 0 ] )
			{
				$id = substr( $value, 1 );
				$config = $this->_searchValidatorConfigById( $id, $validators );
				if ( is_null( $config ) )
				{
					$config = array( 'id' => $id, 'type' => 'NotEmpty' );
				}
				$this->_params[] = $id;
			}
			else
			{
				$config = array(
					'id' => null,
					'type' => 'Compare',
					'str' => $value );
			}
			$validator = Miao_Router_Rule_Validator::factory( $config );
			$this->_validators[] = $validator;
		}
		$this->_parts = $parts;

		if ( count( $validators ) )
		{
			$message = sprintf(
				"Some validators did not find his part of uri (%s). Validators: %s",
				implode( '/', $this->_parts ), print_r( $validators, true ) );
			throw new Miao_Router_Rule_Exception( $message );
		}
	}

	protected function _searchValidatorConfigById( $id, &$validators )
	{
		$result = null;
		foreach ( $validators as $key => $config )
		{
			if ( !array_key_exists( 'id', $config ) )
			{
				throw new Miao_Router_Rule_Exception( 'Invalid validator config item: must content attribute "id"' );
			}
			if ( $config[ 'id' ] == $id )
			{
				$result = $config;
				unset( $validators[ $key ] );
				break;
			}
		}
		return $result;
	}
}
