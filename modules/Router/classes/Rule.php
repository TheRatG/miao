<?php
/**
 * User: vpak
 * Date: 13.09.13
 * Time: 17:33
 */

namespace Miao\Router;

class Rule
{
    private $_rule;

    private $_controller;

    private $_controllerType;

    private $_method;

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
     * @var \Miao\Router\Rule\Validator
     */
    private $_validator;

    /**
     * @var string[]
     */
    private $_parts = array();

    public function __construct( $rule, $controller, $controllerType, $method, $description = null, $prefix = null,
                                 $noRewrite = false )
    {
        $this->setRule( $rule );
        $this->setController( $controller );
        $this->setControllerType( $controllerType );
        $this->setMethod( $method );
        $this->setDescription( $description );
        $this->setPrefix( $prefix );
        $this->setNoRewrite( $noRewrite );

        $this->_parts = explode( '/', $this->getRule() );
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

    public function addValidator( \Miao\Router\Rule\Validator $validator )
    {
        $this->_validator = $validator;
    }

    public function makeUrl( array $params = array(), $method = null )
    {
        if ( empty( $method ) )
        {
            $method = \Miao\Router::getRequestMethod();
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

    public function makeRewrite()
    {
    }
}