<?php
/**
 * User: vpak
 * Date: 13.09.13
 * Time: 17:33
 */

namespace Miao\Router;

class Rule
{
    /**
     * @var string
     */
    private $_rule;

    /**
     * @var string
     */
    private $_controller;

    /**
     * @var string
     */
    private $_controllerType;

    /**
     * @var string
     */
    private $_method;

    /**
     * @var \Miao\Office\Factory
     */
    private $_officeFactory;

    /**
     * @var null
     */
    private $_description;

    /**
     * @var null
     */
    private $_prefix;

    /**
     * @var bool
     */
    private $_noRewrite;

    /**
     * @var \Miao\Router\Rule\Validator[]
     */
    private $_validators;

    /**
     * @var string[]
     */
    private $_parts = array();

    /**
     *
     * @param array $config
     * @return \Miao\Router_Rule
     */
    static public function factory( array $config )
    {
        $prefix = \Miao\Router::checkAndReturnParam( $config, 'prefix', '' );
        $type = \Miao\Router::checkAndReturnParam( $config, 'type' );
        $name = \Miao\Router::checkAndReturnParam( $config, 'name' );
        $rule = \Miao\Router::checkAndReturnParam( $config, 'rule' );
        $method = \Miao\Router::checkAndReturnParam( $config, 'method', '' );
        $desc = \Miao\Router::checkAndReturnParam( $config, 'desc', '' );
        $validators = \Miao\Router::checkAndReturnParam( $config, 'validators',
            array() );
        $noRewrite = \Miao\Router::checkAndReturnParam( $config, 'norewrite', '' );

        $result = new self( $rule, $name, $type, $method, $validators, $desc, $prefix, $noRewrite );
        return $result;
    }
    
    public function __construct( $rule, $controller, $controllerType, $method, array $validators = array(),
                                 $description = null, $prefix = null, $noRewrite = false )
    {
        $this->setRule( $rule );
        $this->setController( $controller );
        $this->setControllerType( $controllerType );
        $this->setMethod( $method );
        $this->setDescription( $description );
        $this->setPrefix( $prefix );
        $this->setNoRewrite( $noRewrite );

        $this->_init( $validators );
    }

    /**
     * @param mixed $controller
     */
    public function setController( $controller )
    {
        $this->_controller = $controller;
    }

    /**
     * @return mixed
     */
    public function getController()
    {
        return $this->_controller;
    }

    /**
     * @param mixed $controllerType
     */
    public function setControllerType( $controllerType )
    {
        $this->_controllerType = $controllerType;
    }

    /**
     * @return mixed
     */
    public function getControllerType()
    {
        return $this->_controllerType;
    }

    /**
     * @param null $description
     */
    public function setDescription( $description )
    {
        $this->_description = $description;
    }

    /**
     * @return null
     */
    public function getDescription()
    {
        return $this->_description;
    }

    /**
     * @param mixed $method
     */
    public function setMethod( $method )
    {
        $this->_method = $method;
    }

    /**
     * @return mixed
     */
    public function getMethod()
    {
        return $this->_method;
    }

    /**
     * @param boolean $noRewrite
     */
    public function setNoRewrite( $noRewrite )
    {
        $this->_noRewrite = $noRewrite;
    }

    /**
     * @return boolean
     */
    public function getNoRewrite()
    {
        return $this->_noRewrite;
    }

    /**
     * @param null $prefix
     */
    public function setPrefix( $prefix )
    {
        $this->_prefix = $prefix;
    }

    /**
     * @return null
     */
    public function getPrefix()
    {
        return $this->_prefix;
    }

    /**
     * @param mixed $rule
     */
    public function setRule( $rule )
    {
        $this->_rule = trim( $rule, '/' );
    }

    /**
     * @return mixed
     */
    public function getRule()
    {
        return $this->_rule;
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

    public function match( $uri, $method = null )
    {
        if ( empty( $method ) )
        {
            $method = \Miao\Office\Request::getMethod();
        }

        $result = false;
        if ( $method == $this->getMethod() )
        {
            $parts = explode( '/', trim( $uri, '/' ) );
            $result = array(
                $this->_getControllerRequestName() => $this->getController()
            );

            $cnt = count( $this->_validators );
            $partsIterator = 0;
            for ( $i = 0; $i < $cnt; $i++ )
            {
                $validator = $this->_validators[ $i ];
                if ( $validator instanceof \Miao\Router\Rule\Validator\Regexp )
                {
                    $slash = $validator->getSlash();
                    $part = implode(
                        '/', array_slice( $parts, $partsIterator, $slash + 1 )
                    );
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

    public function makeUrl( array $params = array(), $method = null )
    {
        if ( empty( $method ) )
        {
            $method = \Miao\Office\Request::getMethod();
        }

        $uri = array();
        $parts = $this->_parts;
        foreach ( $parts as $paramName )
        {
            if ( $this->_isParam( $paramName ) )
            {
                $index = substr( $paramName, 1 );
                if ( isset( $params[ $index ] ) )
                {
                    $uri[ ] = $params[ $index ];
                    unset( $params[ $index ] );
                }
                else
                {
                    $message = sprintf(
                        'Require param (%s) does not exists in $params', $index
                    );
                    throw new \Miao\Router\Rule\Exception( $message );
                }
            }
            else
            {
                $uri[ ] = $paramName;
            }
        }
        $uri = implode( '/', $uri );
        $check = $this->match( $uri, $method );
        if ( false === $check )
        {
            $message = sprintf( 'Uri made (%s) but did not validate', $uri );
            throw new \Miao\Router\Rule\Exception( $message );
        }
        $query = http_build_query( $params );
        if ( !empty( $query ) )
        {
            $uri .= '?' . http_build_query( $params );
        }
        $uri = '/' . $uri;
        return $uri;
    }

    public function makeRewrite()
    {
    }

    protected function _isParam( $str )
    {
        return ':' == $str[ 0 ];
    }

    protected function _getControllerRequestName()
    {
        switch ( strtolower( $this->getControllerType() ) )
        {
            case strtolower( \Miao\Autoload\ClassInfo::TYPE_OBJECT_REQUEST_ACTION ):
                $result = $this
                    ->getOfficeFactory()
                    ->getActionRequestName();
                break;

            case strtolower( \Miao\Autoload\ClassInfo::TYPE_OBJECT_REQUEST_VIEW ):
                $result = $this
                    ->getOfficeFactory()
                    ->getViewRequestName();
                break;

            case strtolower( \Miao\Autoload\ClassInfo::TYPE_OBJECT_REQUEST_VIEWBLOCK ):
                $result = $this
                    ->getOfficeFactory()
                    ->getViewBlockRequestName();
                break;

            default:
                $msg = sprintf( 'Invalid controller type "%s"', $this->getControllerType() );
                throw new \Miao\Router\Rule\Exception( $msg );
        }
        return $result;
    }

    protected function _init( array $validators )
    {
        $rule = $this->getRule();
        $parts = explode( '/', $rule );
        foreach ( $parts as $key => $value )
        {
            if ( ':' == $value[ 0 ] )
            {
                $id = substr( $value, 1 );
                $config = $this->_searchValidatorConfigById( $id, $validators );
                if ( is_null( $config ) )
                {
                    $config = array( 'id' => $id, 'type' => 'NotEmpty' );
                }
                $this->_params[ ] = $id;
            }
            else
            {
                $config = array(
                    'id' => $value,
                    'type' => 'Compare',
                    'str' => $value
                );
            }
            $validator = \Miao\Router\Rule\Validator::factory( $config );
            $this->_validators[ $key ] = $validator;
        }
        $this->_parts = $parts;

        if ( count( $validators ) )
        {
            $message = sprintf(
                "Some validators did not find his part of uri (%s). Validators: %s", implode( '/', $this->_parts ),
                print_r( $validators, true )
            );
            throw new \Miao\Router\Rule\Exception( $message );
        }
    }

    protected function _searchValidatorConfigById( $id, &$validators )
    {
        $result = null;
        foreach ( $validators as $key => $config )
        {
            if ( !array_key_exists( 'id', $config ) )
            {
                throw new \Miao\Router\Rule\Exception( 'Invalid validator config item: must content attribute "id"' );
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