<?php
/**
 *
 * @example XML
 *
 * <Router>
 * 	<main>Main</main>
 * 	<error>404</error>
 *	<defaultPrefix>Daily_BackOffice</defaultPrefix>
 *	<route>
 *		<rule>/news/list/:id</rule>
 *		<view>News_List</view>
 *		<validator param="id" type="Numeric" min="0" />
 *	</route>
 *	<route>
 *		<rule>/publisher/list/:id</rule>
 *		<view>Publisher_List</view>
 *		<validator param="id" type="Numeric" min="0" />
 *	</route>
 *	<route>
 *		<rule>/article/edit/main/:id</rule>
 *		<view>Article_EditMain</view>
 *		<validator param="id" type="Numeric" min="5" />
 *	</route>
 *	<route>
 *		<rule>/article/edit/additional/:id</rule>
 *		<view>Article_EditAdditional</view>
 *		<validator param="id" type="Numeric" min="5" />
 *	</route>
 *	<route>
 *		<rule>/photo/edit/:id</rule>
 *		<view>Photo_Edit</view>
 *		<validator param="id" type="Numeric" min="5" />
 *	</route>
 *	<route>
 *		<rule>/video/edit/:id</rule>
 *		<view>Video_Edit</view>
 *		<validator param="id" type="Numeric" min="5" />
 *	</route>
 * </Router>
 *
 * @author vpak
 *
 */
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
	private $_skipedRules = array();

	static public function factory( array $config, $skipBadRules = false )
	{
		$main = self::checkAndReturnParam( $config, 'main' );
		$error = self::checkAndReturnParam( $config, 'error' );
		$defaultPrefix = self::checkAndReturnParam( $config, 'defaultPrefix',
			'' );

		$rulesConfig = self::checkAndReturnParam( $config, 'route', array() );

		$rules = array();
		$skippedRules = array();

		foreach ( $rulesConfig as $ruleConfig )
		{
			$ruleConfig = self::_convertConfig( $ruleConfig );
			try
			{
				$rule = Miao_Router_Rule::factory( $ruleConfig );
				$rules[] = $rule;
			}
			catch ( Miao_Router_Rule_Exception $e )
			{
				if ( !$skipBadRules )
				{
					//throw new Miao_Router_Exception( $e->getMessage() );
					throw $e;
				}
				$skippedRules[] = $ruleConfig;
			}
		}

		$result = new Miao_Router( $main, $error, $rules, $defaultPrefix, $skippedRules );
		return $result;
	}

	/**
	 *
	 * @param string $main
	 * @param string $error
	 * @param array Miao_Router_Rule $rules
	 * @param string $defaultPrefix
	 */
	public function __construct( $main, $error, array $rules, $defaultPrefix = '', array $skippedRules = array() )
	{
		$this->_main = $main;
		$this->_error = $error;
		$this->_defaultPrefix = $defaultPrefix;

		$this->_skipedRules = $skippedRules;

		$this->_rules = array();
		foreach ( $rules as $key => $rule )
		{
			$index = $this->_makeRuleIndex( $rule->getType(), $rule->getName() );
			$this->_rules[ $index ] = $rule;
		}
	}

	public function getCurrentRoute()
	{
		$uri = '/';
		if ( !empty( $_SERVER[ 'REQUEST_URI' ] ) )
		{
			list( $uri ) = explode( '?', $_SERVER[ 'REQUEST_URI' ] );
		}
		else
		{
			throw new Miao_Router_Exception( 'Param $_SERVER[\'REQUEST_URI\'] is undefined' );
		}
		$result = $this->route( $uri );
		return $result;
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
		$result = $this->_makeUrl( $name, Miao_Router_Rule::TYPE_VIEW, $params );
		return $result;
	}

	public function action( $name, array $params )
	{
		$result = $this->_makeUrl( $name, Miao_Router_Rule::TYPE_ACTION,
			$params );
		return $result;
	}

	public function viewBlock( $name, array $params )
	{
		$result = $this->_makeUrl( $name, Miao_Router_Rule::TYPE_VIEWBLOCK,
			$params );
		return $result;
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
		$result = !empty( $config[ $param ] ) ? $config[ $param ] : $default;
		return $result;
	}

	public function makeRewrite( $mode = 'apache' )
	{
		$s = array();

		foreach ( $this->_rules as $r )
		{
			$s[] = $r->makeRewrite( $mode );
		}

		foreach ( $this->_skipedRules as $r )
		{
			$s[] = '# error happened while generating rewrite for ' . $r[ 'rule' ];
		}

		$s = implode( "\n", $s );

		return $s;
	}

	static protected function _convertConfig( array $ruleConfig )
	{
		$result = array();
		$validator = $ruleConfig[ 'validator' ];
		if ( is_array( $validator ) )
		{
			if ( array_key_exists( 'type', $validator ) )
			{
				$result[ 'validators' ] = array( $validator );
			}
			else
			{
				$result[ 'validators' ] = $validator;
			}
		}
		else
		{
			$result[ 'validators' ] = array();
		}

		foreach ( $result[ 'validators' ] as &$valItem )
		{
			if ( !array_key_exists( 'param', $valItem ) )
			{
				$message = sprintf(
					'Invalid validator config, key "param" does not exists. Dump: (%s)',
					print_r( $valItem, true ) );
				throw new Miao_Router_Exception( $message );
			}

			$valItem[ 'id' ] = $valItem[ 'param' ];
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

	protected function _makeUrl( $name, $type, array $params )
	{
		$index = $this->_makeRuleIndex( Miao_Router_Rule::TYPE_VIEW, $name );
		if ( !array_key_exists( $index, $this->_rules ) )
		{
			$message = sprintf(
				'Rule with name (%s) didn\'t define. Check your config.', $name );
			throw new Miao_Router_Exception( $message );
		}
		$rule = $this->_rules[ $index ];
		$result = '/' . $rule->makeUrl( $params );
		return $result;
	}
}
