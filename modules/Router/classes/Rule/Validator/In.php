<?php
/**
 * @author vpak
 * @date 2013-09-16 16:37:05
 */

namespace Miao\Router\Rule\Validator;

class In extends \Miao\Router\Rule\Validator
{
    private $_variants = array();

    static public function create( array $config )
    {
        if ( !isset( $config[ 'variants' ] ) )
        {
            throw new \Miao\Router\Rule\Validator\Exception( 'Invalid config: param "variants" was not found' );
        }
        $delimiter = ',';
        if ( array_key_exists( 'delimiter', $config ) )
        {
            $delimiter = $config[ 'delimiter' ];
        }
        $variants = explode( $delimiter, $config[ 'variants' ] );
        $result = new self( $config[ 'id' ], $variants );
        return $result;
    }

    public function __construct( $id, array $variants )
    {
        $this->_setId( $id );
        $this->setVariants( $variants );
    }

    public function test( $value )
    {
        $result = in_array( $value, $this->_variants );
        return $result;
    }

    public function setVariants( $variants )
    {
        $message = '';
        if ( empty( $variants ) )
        {
            $message = sprintf(
                'Invalid param "variants": %s', $variants
            );
        }

        foreach ( $variants as &$item )
        {
            $item = trim( $item );
            if ( '' === $item )
            {
                $message = sprintf(
                    'Invalid element into "variants": %s', print_r( $variants, true )
                );
                break;
            }
        }

        if ( !empty( $message ) )
        {
            throw new \Miao\Router\Rule\Validator\Exception( $message );
        }

        $this->_variants = $variants;
    }

    public function getPattern()
    {
        return implode( '|', $this->_variants );
    }
}