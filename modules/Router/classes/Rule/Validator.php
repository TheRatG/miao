<?php
/**
 * @author vpak
 * @date 2013-09-13 17:45:39
 */

namespace Miao\Router\Rule;

abstract class Validator
{
    protected $_id;

    static public function factory( array $config )
    {
        assert( array_key_exists( 'id', $config ) );
        assert( array_key_exists( 'type', $config ) );

        $type = $config[ 'type' ];
        /** @var \Miao\Router\Rule\Validator $className */
        $className = '\\Miao\\Router\\Rule\\Validator\\' . ucfirst( $type );

        try
        {
            $result = $className::create( $config );
        }
        catch ( \Miao\Autoload\Exception\FileNotFound $e )
        {
            throw new \Miao\Router\Rule\Exception( sprintf( 'Validator %s not found.', $className ) );
        }

        if ( !$result instanceof self )
        {
            $message = sprintf(
                'Validator class (%s) must be extend of Miao_Router_Rule_Validator', $className
            );
            throw new \Miao\Router\Rule\Validator\Exception( $message );
        }

        $result->_setId( $config[ 'id' ] );
        return $result;
    }

    public function getId()
    {
        return $this->_id;
    }

    protected function _setId( $id )
    {
        $this->_id = $id;
    }

    static public function create( array $config )
    {

    }

    abstract public function test( $value );

    abstract public function getPattern();
}