<?php
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

	/**
	 *
	 * @var array Miao_Router_Rule_Validator
	 */
	private $_validators;
	private $_parts;

	/**
	 *
	 * @param string $type
	 * @param string $name
	 * @param string $rule
	 * @param array $validators
	 */
	public function __construct( $prefix, $type, $name, $rule, array $validators = array() )
	{
		$this->setPrefix( $prefix );
		$this->setType( $type );
		$this->setName( $name );
		$this->setRule( $rule );
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
		$validators = Miao_Router::checkAndReturnParam( $config, 'validators',
			array() );
		$result = new self( $prefix, $type, $name, $rule, $validators );
		return $result;
	}

	public function match( $uri )
	{
		$result = false;
		$parts = explode( '/', trim( $uri, '/' ) );
		$cnt = count( $parts );
		if ( $cnt === count( $this->_parts ) )
		{
			$result = array(
				$this->_getOfficeTypeParamName() => $this->getName() );
			for( $i = 0; $i < $cnt; $i++ )
			{
				$part = $parts[ $i ];
				$validator = $this->_validators[ $i ];
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
	 * @param field_type $prefix
	 */
	public function setPrefix( $prefix )
	{
		$this->_prefix = $prefix;
	}

	/**
	 * @return the $type
	 */
	public function getType()
	{
		return $this->_type;
	}

	/**
	 * @param field_type $type
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
	 * @param field_type $name
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

	public function getValidators()
	{
		return $this->_validators;
	}

	public function makeUrl( array $params = array() )
	{
		$uri = array();
		foreach ( $this->_parts as $paramName )
		{
			if ( ':' == $paramName[ 0 ] )
			{
				$index = substr( $paramName, 1 );
				if ( isset( $params[ $index ] ) )
				{
					$uri[] = $params[ $index ];
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
		$check = $this->match( $uri );
		if ( false === $check )
		{
			$message = sprintf( 'Uri maked (%s) but did not validate', $uri );
			throw new Miao_Router_Rule_Exception( $message );
		}
		return $uri;
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
			if ( ':' == $value[ 0 ] )
			{
				$id = substr( $value, 1 );
				$config = $this->_searchValidatorConfigById( $id, $validators );
				if ( is_null( $config ) )
				{
					$config = array( 'id' => $id, 'type' => 'NotEmpty' );
				}
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