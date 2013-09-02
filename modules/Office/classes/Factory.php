<?php
/**
 * User: vpak
 * Date: 02.09.13
 * Time: 16:19
 */

namespace Miao\Office;

class Factory
{
    protected $_resourceRequestName = '_resource';

    protected $_viewRequestName = '_view';

    protected $_viewBlockRequestName = '_viewBlock';

    protected $_actionRequestName = '_action';

    protected $_prefixRequestName = '_prefix';

    protected $_defaultPrefix;

    protected $_requestMethod;

    public function __construct( $defaultPrefix = '', $requestMethod = 'get' )
    {
        $this->_defaultPrefix = $defaultPrefix;
        $this->_requestMethod = $requestMethod;
    }

    /**
     * @param string $actionRequestName
     */
    public function setActionRequestName( $actionRequestName )
    {
        $this->_actionRequestName = $actionRequestName;
    }

    /**
     * @return string
     */
    public function getActionRequestName()
    {
        return $this->_actionRequestName;
    }

    /**
     * @param string $prefixRequestName
     */
    public function setPrefixRequestName( $prefixRequestName )
    {
        $this->_prefixRequestName = $prefixRequestName;
    }

    /**
     * @return string
     */
    public function getPrefixRequestName()
    {
        return $this->_prefixRequestName;
    }

    /**
     * @param string $viewBlockRequestName
     */
    public function setViewBlockRequestName( $viewBlockRequestName )
    {
        $this->_viewBlockRequestName = $viewBlockRequestName;
    }

    /**
     * @return string
     */
    public function getViewBlockRequestName()
    {
        return $this->_viewBlockRequestName;
    }

    /**
     * @param string $viewRequestName
     */
    public function setViewRequestName( $viewRequestName )
    {
        $this->_viewRequestName = $viewRequestName;
    }

    /**
     * @return string
     */
    public function getViewRequestName()
    {
        return $this->_viewRequestName;
    }

    /**
     * @return string
     */
    public function getDefaultPrefix()
    {
        return $this->_defaultPrefix;
    }

    /**
     * @param string $defaultPrefix
     */
    public function setDefaultPrefix( $defaultPrefix )
    {
        $this->_defaultPrefix = $defaultPrefix;
    }

    /**
     * @param $requestParams
     * @return mixed
     */
    public function getPrefix( $requestParams )
    {
        $result = $this->getDefaultPrefix();
        if ( isset( $requestParams[ $this->_prefixRequestName ] )
            && !empty( $requestParams[ $this->_prefixRequestName ] )
        )
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

        $result = sprintf( '%s\\%s\\%s', $prefix, ucfirst( $type ), $name );
        return $result;
    }

    /**
     * @return string
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

    public function getClassList( array $requestParams, array $default = array() )
    {
        $resourceName = '\\Miao\\Office\\Resource\\' . ucfirst(
                $this->getRequestMethod()
            );

        $types = \Miao\Office::getTypesObjectRequest();
        $result = $this->_getClassList( $requestParams, $resourceName, $types );
        if ( !empty( $default ) )
        {
            $defaultValues = $this->_getClassList( $default, $resourceName, $types );
            if ( count( array_unique( $result ) ) < count( array_unique( $defaultValues ) ) )
            {
                $result = $defaultValues;
            }
        }
        return $result;
    }

    protected function _getClassList( array $params, $resourceName, array $types )
    {
        $prefix = $this->getPrefix( $params );
        $types = array_map( 'lcfirst', $types );
        $values[ ] = $resourceName;
        for ( $i = 1, $cnt = count( $types ); $i < $cnt; $i++ )
        {
            $values[ ] = $this->_getParamValue(
                $params, lcfirst( $types[ $i ] ), $prefix
            );
        }
        $result = array_combine( $types, $values );
        return $result;
    }

    protected function _getParamValue( array $requestParams, $typeName, $prefix )
    {
        $requestName = sprintf( '_%sRequestName', $typeName );
        $result = null;
        $requestValue = '';

        if ( isset( $requestParams[ $this->$requestName ] )
            && strlen(
                $requestParams[ $this->$requestName ]
            )
        )
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