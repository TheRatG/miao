<?php
/**
 * @author vpak
 * @date 2013-09-16 15:59:12
 */

namespace Miao\Router\Rule\Validator;

class Compare extends \Miao\Router\Rule\Validator
{
    private $_str;

    public function __construct( array $config )
    {
        if ( !isset( $config[ 'str' ] ) )
        {
            throw new \Miao\Router\Rule\Validator\Exception( 'Invalid config: param "str" was not found' );
        }
        $this->_str = $config[ 'str' ];
    }

    public function test( $value )
    {
        $result = false;
        if ( 0 === strcmp( $value, $this->_str ) )
        {
            $result = true;
        }
        return $result;
    }

    public function getPattern()
    {
        return $this->_str;
    }
}