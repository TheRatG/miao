<?php
class Miao_Router
{
	private $_main = 'Main';
	private $_error = '404';
	private $_defaultPrefix = '';

	/**
	 *
	 * @var  array Miao_Router_Rule $rules
	 */
	private $_rules = array();

	static public function factory( array $config )
	{
		$main = self::checkAndReturnParam( $config, 'main' );
		$error = self::checkAndReturnParam( $config, 'error' );
		$defaultPrefix = self::checkAndReturnParam( $config, 'defaultPrefix',
			'' );

		$rulesConfig = self::checkAndReturnParam( $config, 'rules', array() );

		$rules = array();
		foreach ( $rulesConfig as $ruleConfig )
		{
			$ruleConfig = self::_convertConfig( $ruleConfig );
			$rule = Miao_Router_Rule::factory( $ruleConfig );
			$rules[] = $rule;
		}
		$result = new Miao_Router( $main, $error, $rules, $defaultPrefix );
		return $result;
	}

	/**
	 *
	 * @param string $main
	 * @param string $error
	 * @param array Miao_Router_Rule $rules
	 * @param string $defaultPrefix
	 */
	public function __construct( $main, $error, array $rules, $defaultPrefix = '' )
	{
		$this->_main = $main;
		$this->_error = $error;
		$this->_defaultPrefix = $defaultPrefix;

		$this->_rules = array();
		foreach ( $rules as $key => $rule )
		{
			$index = $this->_makeRuleIndex( $rule->getType(), $rule->getName() );
			$this->_rules[ $index ] = $rule;
		}
	}

	public function route( $uri, $throwException = true )
	{
		$uri = trim( $uri, '/' );

		$result = false;
		if ( empty( $uri ) )
		{
			$result = array();
			$result[ '_view' ] = $this->_main;
			$result[ '_prefix' ] = $this->_defaultPrefix;
		}
		else
		{
			foreach ( $this->_rules as $rule )
			{
				$params = $rule->match( $uri );
				if ( is_array( $params ) )
				{
					if ( !array_key_exists( 'prefix', $params ) && $this->_defaultPrefix )
					{
						$params[ '_prefix' ] = $this->_defaultPrefix;
					}
					$result = $params;
					break;
				}
			}
		}

		if ( $result == false && $throwException )
		{
			$message = sprintf(
				'Rule for uri (%s) not found, please check your config', $uri );
			throw new Miao_Router_Exception( $message );
		}
		return $result;
	}

	public function view( $name, array $params = array() )
	{
		$index = $this->_makeRuleIndex( Miao_Router_Rule::TYPE_VIEW, $name );
		if ( !array_key_exists( $index, $this->_rules ) )
		{
			$message = sprintf(
				'Rule with name (%s) didn\'t define. Check your config.', $name );
			throw new Miao_Router_Exception( $message );
		}
		$rule = $this->_rules[ $index ];
		$result = $rule->makeUrl( $params );
		return $result;
	}

	public function action( $name, array $params )
	{
	}

	public function viewBlock( $name, array $params )
	{
	}

	/**
	 *
	 * @param array $config
	 * @param string $param
	 */
	static public function checkAndReturnParam( array $config, $param, $default = null )
	{
		if ( !array_key_exists( $param, $config ) && is_null( $default ) )
		{
			$message = sprintf( 'Invalid config: need "%s" param', $param );
			throw new Miao_Router_Exception( $message );
		}
		$result = $config[ $param ];
		return $result;
	}

	static protected function _convertConfig( array $ruleConfig )
	{
		$result = array();
		$validator = $ruleConfig[ 'validator' ];
		if ( array_key_exists( 'type', $validator ) )
		{
			$validator[ 'id' ] = $validator[ 'param' ];
			$result[ 'validators' ] = array( $validator );
		}
		else
		{
			$result[ 'validators' ] = $validator;

			foreach ( $result[ 'validators' ] as &$valItem )
			{
				$valItem[ 'id' ] = $valItem[ 'param' ];
			}
		}

		$type = '';
		$name = '';
		if ( array_key_exists( Miao_Router_Rule::TYPE_VIEWBLOCK, $ruleConfig ) )
		{
			$type = Miao_Router_Rule::TYPE_VIEWBLOCK;
			$name = $ruleConfig[ $type ];
		}
		if ( array_key_exists( Miao_Router_Rule::TYPE_ACTION, $ruleConfig ) )
		{
			$type = Miao_Router_Rule::TYPE_ACTION;
			$name = $ruleConfig[ $type ];
		}
		if ( array_key_exists( Miao_Router_Rule::TYPE_VIEW, $ruleConfig ) )
		{
			$type = Miao_Router_Rule::TYPE_VIEW;
			$name = $ruleConfig[ $type ];
		}
		$result[ 'name' ] = $name;
		$result[ 'type' ] = $type;
		$result[ 'rule' ] = $ruleConfig[ 'rule' ];
		return $result;
	}

	protected function _makeRuleIndex( $type, $name )
	{
		$result = $type . '::' . $name;
		return $result;
	}
}