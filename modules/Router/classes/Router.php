<?php
/**
 * User: vpak
 * Date: 13.09.13
 * Time: 18:15
 */

namespace Miao;

class Router
{
    /**
     * @var \Miao\Router\Rule[]
     */
    protected $_rules = array();

    /**
     * @var \Miao\Office\Factory
     */
    private $_officeFactory;

    /**
     * @var string
     */
    private $_defaultPrefix;

    static public function checkAndReturnParam( array $config, $param, $default = null )
    {
        if ( !array_key_exists( $param, $config ) && is_null( $default ) )
        {
            $message = sprintf( 'Invalid config: need "%s" param', $param );
            throw new \Miao\Router\Exception( $message );
        }
        $result = !empty( $config[ $param ] ) ? $config[ $param ] : $default;
        return $result;
    }

    public function __construct( $defaultPrefix = null )
    {
        if ( !empty( $defaultPrefix ) )
        {
            $this->_defaultPrefix = $defaultPrefix;
        }
    }

    /**
     * @return string
     */
    public function getDefaultPrefix()
    {
        if ( empty( $this->_defaultPrefix ) )
        {
            $this->_defaultPrefix = $this
                ->getOfficeFactory()
                ->getDefaultPrefix();
        }
        return $this->_defaultPrefix;
    }

    /**
     * @param \Miao\Office\Factory $officeFactory
     */
    public function setOfficeFactory( $officeFactory )
    {
        $this->_officeFactory = $officeFactory;
    }

    /**
     * @return \Miao\Office\Factory
     */
    public function getOfficeFactory()
    {
        $result = $this->_officeFactory;
        if ( is_null( $this->_officeFactory ) )
        {
            $result = \Miao\App::getInstance()
                ->getObject( \Miao\App::INSTANCE_OFFICE_FACTORY_NICK, false );
            if ( !$result )
            {
                $result = new \Miao\Office\Factory();
            }
        }
        return $result;
    }

    public function getCurrentRoute()
    {
        $uri = \Miao\Office\Request::getRequestUri();
        $result = $this->route( $uri );
        return $result;
    }

    public function getCurrentView()
    {
        $params = $this->getCurrentRoute();
        $viewRequestName = $this
            ->getOfficeFactory()
            ->getViewRequestName();
        $result = '';
        if ( isset( $params[ $viewRequestName ] ) )
        {
            $result = $params[ $viewRequestName ];
        }
        return $result;
    }

    public function getCurrentUrl()
    {
        $uri = \Miao\Office\Request::getRequestUri();
        $rule = $this->getRuleByUri( $uri );
        $result = '';
        if ( $rule )
        {
            $method = \Miao\Office\Request::getMethod();
            $params = $GLOBALS[ '_' . $method ];
            $params = array_diff_key( $params, array(
                                                    $this->getOfficeFactory()->getViewRequestName() => 1,
                                                    $this->getOfficeFactory()->getActionRequestName() => 2,
                                                    $this->getOfficeFactory()->getViewBlockRequestName() => 3 ) );

            $result = $rule->makeUrl( $params, $method );
        }
        return $result;
    }

    public function add( \Miao\Router\Rule $rule )
    {
        $rule->setOfficeFactory( $this->getOfficeFactory() );
        $this->_rules[ ] = $rule;
    }

    public function view( $name, array $params = array() )
    {
        $result = $this->makeUrl( $name, \Miao\Autoload\ClassInfo::TYPE_OBJECT_REQUEST_VIEW, $params );
        return $result;
    }

    public function action( $name, array $params, $method = 'POST' )
    {
        $result = $this->makeUrl( $name, \Miao\Autoload\ClassInfo::TYPE_OBJECT_REQUEST_ACTION, $params, $method );
        return $result;
    }

    public function viewBlock( $name, array $params )
    {
        $result = $this->makeUrl( $name, \Miao\Autoload\ClassInfo::TYPE_OBJECT_REQUEST_VIEWBLOCK, $params );
        return $result;
    }

    /**
     * Looking for a suitable rule and return params
     * @param $uri
     * @param null $method
     * @param bool $throwException
     * @throws Router\Exception
     * @return array|bool
     */
    public function route( $uri, $method = null, $throwException = true )
    {
        $uri = trim( $uri, '/' );
        $result = false;

        $prefixRequestName = $this
            ->getOfficeFactory()
            ->getPrefixRequestName();
        $params = array();
        $rule = $this->getRuleByUri( $uri, $method, $params );
        if ( $rule )
        {
            if ( !array_key_exists( $prefixRequestName, $params ) )
            {
                $params[ $prefixRequestName ] = $this->getDefaultPrefix();
            }
            $result = $params;
        }

        if ( $result == false && $throwException )
        {
            $message = sprintf( 'Rule for uri (%s) not found, please check your config', $uri );
            throw new \Miao\Router\Exception( $message );
        }
        return $result;
    }

    public function getRuleByUri( $uri, $method = null, array &$params = array() )
    {
        $uri = trim( $uri, '/' );
        if ( empty( $method ) )
        {
            $method = \Miao\Office\Request::getMethod();
        }

        $result = null;
        foreach ( $this->_rules as $rule )
        {
            $params = $rule->match( $uri, $method );
            if ( is_array( $params ) )
            {
                $result = $rule;
                break;
            }
        }
        return $result;
    }

    public function makeUrl( $name, $type, array $params, $method = \Miao\Office\Request::METHOD_GET )
    {
        $candidates = array();
        foreach ( $this->_rules as $key => $rule )
        {
            $baseCheck = ( $name == $rule->getController()
                && $type == $rule->getControllerType()
                && $method == $rule->getMethod() );
            if ( $baseCheck )
            {
                $candidate = array();
                $candidate[ 'index' ] = $key;

                $keys = array_keys( $params );
                $ruleParams = $rule->getParams();

                $int = array_intersect( $keys, $ruleParams );
                $candidate[ 'cnt' ] = count( $int );

                if ( count( $params ) >= count( $ruleParams ) )
                {
                    $candidates[ ] = $candidate;
                }
            }
        }

        if ( empty( $candidates ) )
        {
            $message = sprintf( 'Rule with name (%s) did not define. Check your config.', $name );
            throw new \Miao\Router\Exception( $message );
        }

        $candidate = array_shift( $candidates );
        foreach ( $candidates as $item )
        {
            if ( $item[ 'cnt' ] > $candidate[ 'cnt' ] )
            {
                $candidate = $item;
            }
            //@todo: need test
            // 			else if ( $item[ 'cnt' ] == $candidate[ 'cnt' ] )
            // 			{
            // 				$message = sprintf( 'Rule duplicate detected, name: %s', $name );
            // 				throw new Miao_Router_Exception( $message );
            // 			}
        }
        $index = $candidate[ 'index' ];
        $rule = $this->_rules[ $index ];
        $result = $rule->makeUrl( $params, $method );
        return $result;
    }

    public function makeRewrite( $mode = \Miao\Router\Rule::REWRITE_MODE_NGINX )
    {
        $s = array();

        foreach ( $this->_rules as $r )
        {
            $s[] = $r->makeRewrite( $mode );
        }
        $s = implode( "\n", $s );

        return $s;
    }
}