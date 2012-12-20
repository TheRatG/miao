<?php
/**
 *
 * Need refactoring
 * @author vpak
 *
 */
class Miao_Office_Factory
{
	protected $_defaultPrefix = '';
	protected $_requestMethod;

	protected $_resourceRequestName = '_resource';
	protected $_viewRequestName = '_view';
	protected $_viewBlockRequestName = '_viewBlock';
	protected $_actionRequestName = '_action';
	protected $_prefixRequestName = '_prefix';

	/**
	 * @return the $_viewRequestName
	 */
	public function getViewRequestName()
	{
		return $this->_viewRequestName;
	}

	/**
	 * @return the $_viewBlockRequestName
	 */
	public function getViewBlockRequestName()
	{
		return $this->_viewBlockRequestName;
	}

	/**
	 * @return the $_actionRequestName
	 */
	public function getActionRequestName()
	{
		return $this->_actionRequestName;
	}

	/**
	 * @return the $_prefixRequestName
	 */
	public function getPrefixRequestName()
	{
		return $this->_prefixRequestName;
	}

	public function __construct( array $config )
	{
		$this->setDefaultPrefix( $config[ 'defaultPrefix' ] );
		if ( !empty( $config[ 'requestMethod' ] ) )
		{
			$this->setRequestMethod( $config[ 'requestMethod' ] );
		}
	}

	/**
	 * @return the $_defaultPrefix
	 */
	public function getDefaultPrefix()
	{
		return $this->_defaultPrefix;
	}

	/**
	 * @param field_type $_defaultPrefix
	 */
	public function setDefaultPrefix( $defaultPrefix )
	{
		$this->_defaultPrefix = $defaultPrefix;
	}

	/**
	 * @return the $_requestMethod
	 */
	public function getRequestMethod()
	{
		if ( empty( $this->_requestMethod ) )
		{
			$this->_requestMethod = 'Get';
			if ( isset( $_SERVER[ 'REQUEST_METHOD' ] ) )
			{
				$this->_requestMethod = $_SERVER[ 'REQUEST_METHOD' ];
			}
			$this->_requestMethod = strtolower( $this->_requestMethod );
			$this->_requestMethod = ucfirst( $this->_requestMethod );
		}
		return $this->_requestMethod;
	}

	/**
	 * @param field_type $_requestMethod
	 */
	public function setRequestMethod( $requestMethod )
	{
		$this->_requestMethod = $requestMethod;
	}

	/**
	 * @return the $_prefix
	 */
	public function getPrefix( $requestParams )
	{
		$result = $this->getDefaultPrefix();
		if ( isset( $requestParams[ $this->_prefixRequestName ] ) && !empty(
			$requestParams[ $this->_prefixRequestName ] ) )
		{
			$result = $requestParams[ $this->_prefixRequestName ];
		}
		return $result;
	}

	public function getClassName( $type, $name, $prefix = '' )
	{
		if ( empty( $prefix ) )
		{
			$prefix = $this->_defaultPrefix;
		}

		$result = sprintf( '%s_%s_%s', $prefix, ucfirst( $type ), $name );
		return $result;
	}

	public function getClassList( array $requestParams, array $default = array() )
	{
		$prefix = $this->getPrefix( $requestParams );

		$resourceName = $this->_getParamValue( $requestParams, 'resource',
			$prefix );
		if ( empty( $resourceName ) )
		{
			$resourceName = 'Miao_Office_Resource_' . ucfirst(
				$this->getRequestMethod() );
		}

		$types = Miao_Office::getTypesObjectRequest();
		$values = $this->_getClassList( $requestParams, $resourceName, $types );
		$defaultValues = $this->_getClassList( $default, $resourceName, $types );

		$result = $defaultValues;

		$checkValues = false;
		foreach ( $values as $key => $val )
		{
			if ( 0 === strcasecmp( $key, Miao_Office::TYPE_RESOURCE ) )
			{
				continue;
			}
			if ( !empty( $val ) )
			{
				$checkValues = true;
				break;
			}
		}

		if ( $checkValues )
		{
			$result = $values;
		}

		return $result;
	}

	public function getOffice( array $params, array $default = array( '_view' => 'Main' ) )
	{
		$list = $this->getClassList( $params, $default );

		$frontOffice = new Miao_Office();
		$frontOffice->setFactory( $this );

		/**
		 * TODO: refactoring
		 */
		$resource = new $list[ lcfirst( Miao_Office::TYPE_RESOURCE ) ]( $frontOffice );
		if ( $list[ lcfirst( Miao_Office::TYPE_VIEW ) ] )
		{
			$className = $list[ lcfirst( Miao_Office::TYPE_VIEW ) ];
			$path = Miao_Path::getInstance();
			$templatesDir = $path->getModuleRoot( $className ) . '/templates';
			$templatesObj = new Miao_Office_TemplatesEngine_PhpNative( $templatesDir );
			$view = new $list[ lcfirst( Miao_Office::TYPE_VIEW ) ]( $templatesObj );
			$frontOffice->setView( $view );
		}
		if ( $list[ lcfirst( Miao_Office::TYPE_VIEWBLOCK ) ] )
		{
			$viewBlock = new $list[ lcfirst( Miao_Office::TYPE_VIEWBLOCK ) ]();
			$frontOffice->setViewBlock( $viewBlock );
		}
		if ( $list[ lcfirst( Miao_Office::TYPE_ACTION ) ] )
		{
			$action = new $list[ lcfirst( Miao_Office::TYPE_ACTION ) ]();
			$frontOffice->setAction( $action );
		}

		$frontOffice->setResource( $resource );
		$header = new Miao_Office_Header();
		$frontOffice->setHeader( $header );

		return $frontOffice;
	}

	protected function _getClassList( array $params, $resourceName, array $types )
	{
		$result = array();

		if ( !empty( $params ) )
		{
			$prefix = $this->getPrefix( $params );
			$types = array_map( 'lcfirst', $types );
			$values[] = $resourceName;
			for( $i = 1, $cnt = count( $types ); $i < $cnt; $i++ )
			{
				$values[] = $this->_getParamValue( $params,
					lcfirst( $types[ $i ] ), $prefix );
			}
			$result = array_combine( $types, $values );
		}
		return $result;
	}

	protected function _getParamValue( array $requestParams, $typeName, $prefix )
	{
		$requestName = sprintf( '_%sRequestName', $typeName );
		$result = '';
		$requestValue = '';

		if ( isset( $requestParams[ $this->$requestName ] ) && strlen(
			$requestParams[ $this->$requestName ] ) )
		{
			$requestValue = $requestParams[ $this->$requestName ];
		}
		if ( $requestValue )
		{
			$requestValue = trim( $requestValue );
			$result = $this->getClassName( $typeName, $requestValue, $prefix );
		}
		return $result;
	}
}