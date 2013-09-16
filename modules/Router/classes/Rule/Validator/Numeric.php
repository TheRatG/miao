<?php
/**
 *
 * @author vpak
 * @date 2013-09-16 16:39:07
 */

namespace Miao\Router\Rule\Validator;

class Numeric extends \Miao\Router\Rule\Validator
{
    private $_min = 1;
    private $_max = null;

    public function __construct( array $config )
    {
        if ( array_key_exists( 'min', $config ) )
        {
            $this->_min = $config[ 'min' ];
        }
        if ( array_key_exists( 'max', $config ) )
        {
            $this->_max = $config[ 'max' ];
        }
    }

    public function test( $value )
    {
        $value = ( string ) trim( $value );
        $result = is_numeric( $value );

        $len = strlen( $value );

        if ( $this->_min )
        {
            if ( $len < $this->_min )
            {
                $result = false;
            }
        }

        if ( $result && $this->_max )
        {
            if ( $len > $this->_max )
            {
                $result = false;
            }
        }

        return $result;
    }

    public function getPattern()
    {
        $len = '+';
        $min = intval( $this->_min );
        $max = intval( $this->_max );

        if ( $min && $max )
        {
            $len = $min == $max ? sprintf( '{%s}', $min ) : sprintf( '{%s,%s}', $min, $max );
        }
        else if ( $min > 1 )
        {
            $len = sprintf( '{%s,}', $min );
        }

        return '[0-9]' . $len;
    }
}