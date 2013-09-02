<?php
/**
 * @author vpak
 * @date 2013-08-13 11:47:42
 */

namespace Miao\Office;

class Request
{
    /**
     * @var array
     */
    protected $_vars;

    /**
     * Имя метода REQUEST_METHOD
     * @var string
     */
    protected $_method;

    /**
     * Получить значение свойства _vars
     * @return array
     */
    public function getVars()
    {
        return $this->_vars;
    }

    /**
     * @param array $data
     */
    public function __construct( array $data = null )
    {
        $this->_method = isset( $_SERVER[ 'REQUEST_METHOD' ] ) ? strtoupper( $_SERVER[ 'REQUEST_METHOD' ] ) : 'GET';
        $this->resetVars( $data );
    }

    public function getMethod()
    {
        return $this->_method;
    }

    public function getValueOf( $name, $defaultValue = null, $throwException = true )
    {
        $result = $defaultValue;
        if ( array_key_exists( $name, $this->_vars ) )
        {
            $result = $this->_vars[ $name ];
        }
        else if ( $throwException )
        {
            $msg = 'Request variable with name "' . $name . '" was not received in "' . $this->_method . '"';
            throw new \Miao\Office\Request\Exception\OnVarNotExists( $msg );
        }
        return $result;
    }

    /**
     * Restore vars
     * @param array $data
     */
    public function resetVars( array $data = null )
    {
        $method = $this->_method;
        if ( $method == 'HEAD' )
        {
            $method = 'GET';
        }
        if ( is_null( $data ) )
        {
            $this->_vars = $GLOBALS[ '_' . $method ];
            $this->_vars = array_merge_recursive( $this->_vars, $_FILES );
        }
        else
        {
            $this->_vars = $data;
        }
    }

    /**
     * Преобразует специальные символы в HTML сущности и удаляет теги.
     * @param string $data
     * @param string $allowable_tags указания тэгов, которые не должны удаляться
     * @return string
     */
    public function stripRequestedString( $data, $allowable_tags = '' )
    {
        return htmlspecialchars( strip_tags( trim( $data ), $allowable_tags ) );
    }

    public function getServerHttpHost()
    {
        $result = $this->getServerVar( 'HTTP_HOST' );
        return $result;
    }

    public function setValuesOf( array $data )
    {
        foreach ( $data as $varName => $value )
        {
            $this->setValueOf( $varName, $value );
        }
    }

    public function setValueOf( $varName, $value )
    {
        $this->_vars[ $varName ] = $value;
    }

    /*
     * Получение значений серверных переменных.
     */
    public function getServerVar( $key )
    {
        $result = false;
        if ( in_array( $key, array( 'HTTP_HOST', 'SERVER_NAME' ) ) )
        {
            $result = isset( $_SERVER[ 'SERVER_NAME' ] ) ? $_SERVER[ 'SERVER_NAME' ] : ( isset( $_SERVER[ 'HTTP_HOST' ] ) ? $_SERVER[ 'HTTP_HOST' ] : false );
        }
        else
        {
            $result = isset( $_SERVER[ $key ] ) ? $_SERVER[ $key ] : false;
        }

        return $result;
    }
}