<?php
/**
 * User: vpak
 * Date: 02.09.13
 * Time: 16:19
 */

namespace Miao\Office;

class Factory
{
    protected $_viewRequestName = '_miaoView';

    protected $_viewBlockRequestName = '_miaoViewBlock';

    protected $_actionRequestName = '_miaoAction';

    protected $_prefixRequestName = '_miaoPrefix';

    protected $_defaultPrefix;

    public function __construct( $defaultPrefix = '' )
    {
        $this->_defaultPrefix = $defaultPrefix;
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

    public function getControllerClassName( array $params, array $defaultParams = array() )
    {
        $params = array_merge_recursive( $params, $defaultParams );
        $prefix = $this->getPrefix( $params );

        $types = array( 'view', 'action', 'viewBlock' );
        $result = null;
        foreach ( $types as $type )
        {
            $tmp = $this->_getParamValue( $params, $type, $prefix );
            if ( $tmp  )
            {
                if ( !empty( $result ) )
                {
                    $msg = 'Only one type controller in the same time';
                    throw new \Miao\Office\Exception( $msg );
                }
                $result = $tmp;
            }
        }
        return $result;
    }

    protected function _getParamValue( array $params, $typeName, $prefix )
    {
        $requestName = sprintf( '_%sRequestName', $typeName );
        $result = null;
        $requestValue = '';

        if ( isset( $params[ $this->$requestName ] )
            && strlen(
                $params[ $this->$requestName ]
            )
        )
        {
            $requestValue = $params[ $this->$requestName ];
        }
        if ( $requestValue )
        {
            $requestValue = trim( $requestValue );
            $result = $this->getClassName( $typeName, $requestValue, $prefix );
        }
        return $result;
    }
}