<?php
/**
 * @author vpak
 * @date 2013-09-16 16:39:20
 */

namespace Miao\Router\Rule\Validator;

class Regexp extends \Miao\Router\Rule\Validator
{
    /**
     * @var string
     */
    private $_pattern;

    private $_slash = 0;

    static public function create( array $config )
    {
        if ( !isset( $config[ 'pattern' ] ) )
        {
            throw new \Miao\Router\Rule\Validator\Exception( 'Invalid config: param "pattern" was not found' );
        }
        $pattern = $config[ 'pattern' ];
        $slash = 0;
        if ( array_key_exists( 'slash', $config ) )
        {
            $slash = $config[ 'slash' ];
        }
        $result = new self( $config[ 'id' ], $pattern, $slash );
        return $result;
    }

    public function __construct( $id, $pattern, $slash = 0 )
    {
        $this->_setId( $id );
        $this->_pattern = $pattern;
        $this->_slash = $slash;
    }

    /**
     * (non-PHPdoc)
     * @see Miao_Router_Rule_Validator::test()
     */
    public function test( $value )
    {
        // preg_quote($keywords, '/'); не сработало
        $pt = '/^' . str_replace( '/', '\/', $this->_pattern ) . '$/';
        $result = preg_match( $pt, $value );
        return $result;
    }

    /**
     * @return string
     */
    public function getPattern()
    {
        return $this->_pattern;
    }

    public function getSlash()
    {
        return $this->_slash;
    }
}