<?php
/**
 * @author vpak
 * @date 2013-09-06 10:58:19
 */

namespace Miao\Office;

class Header
{
    protected $_list = array();

    protected $_nameList = array();

    public function __construct()
    {
        $this->set( 'Content-type', array( 'text/html', 'charset' => 'utf-8' ) );
    }

    /**
     * Sets a header by name.
     * @param string $key The key
     * @param string|array $values The value or an array of values
     * @param bool $replace Whether to replace the actual value or not (true by default)
     * @return $this
     */
    public function set( $key, $values, $replace = true )
    {
        $uniqueKey = $this->_getUniqueKey( $key );
        $this->_list[ $uniqueKey ] = $values;
        $this->_nameList[ $uniqueKey ] = $key;

        if ( true === $replace || !isset( $this->_list[ $uniqueKey ] ) )
        {
            $this->_list[ $uniqueKey ] = $values;
        }
        else
        {
            $this->_list[ $uniqueKey ] = array_merge( $this->_list[ $uniqueKey ], $values );
        }
        return $this;
    }

    /**
     * Returns a header value by name.
     * @param $key
     * @param bool $asString
     * @return null|string
     * @throws Header\Exception\InvalidArgument
     */
    public function get( $key, $asString = false )
    {
        if ( empty( $key ) || !is_string( $key ) )
        {
            $msg = 'Invalid argument $key, must be string';
            throw new \Miao\Office\Header\Exception\InvalidArgument( $msg );
        }

        $uniqueKey = $this->_getUniqueKey( $key );
        $result = null;
        if ( isset( $this->_list[ $uniqueKey ] ) )
        {
            $result = $this->convert( $this->_list[ $uniqueKey ] );
            if ( $asString )
            {
                $result = sprintf( '%s: %s', $this->_nameList[ $uniqueKey ], $result );
            }
        }
        return $result;
    }

    protected function _getUniqueKey( $key )
    {
        $uniqueKey = strtr( strtolower( $key ), '_', '-' );
        return $uniqueKey;
    }

    public function getList()
    {
        $uniqueKeyList = array_keys( $this->_list );
        $result = array();
        foreach ( $uniqueKeyList as $uniqueKey )
        {
            $result[ ] = $this->get( $uniqueKey, true );
        }
        return $result;
    }

    public function convert( $values )
    {
        $result = '';
        if ( is_array( $values ) )
        {
            foreach ( $values as $key => $value )
            {
                if ( !is_numeric( $key ) )
                {
                    $result[ ] = $key . '=' . $value;
                }
                else
                {
                    $result[ ] = $value;
                }
            }
        }
        else
        {
            $result = array( (string)$values );
        }
        $result = implode( '; ', $result );
        return $result;
    }
}