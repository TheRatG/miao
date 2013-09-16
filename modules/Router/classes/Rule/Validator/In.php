<?php
/**
 * @author vpak
 * @date 2013-09-16 16:37:05
 */

namespace Miao\Router\Rule\Validator;

class In extends \Miao\Router\Rule\Validator
{
    private $_delimiter = ',';

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
        $result = new self( $config[ 'variants' ], $delimiter );
        return $result;
    }

    public function __construct( $variants, $delimiter )
    {
        $this->_delimiter = $delimiter;
        $this->_initVariants( $variants );
    }

    public function test( $value )
    {
        $result = in_array( $value, $this->_variants );
        return $result;
    }

    protected function _initVariants( $variantsStr )
    {
        $this->_variants = explode( $this->_delimiter, $variantsStr );

        foreach ( $this->_variants as &$item )
        {
            $item = trim( $item );
            if ( '' === $item )
            {
                $message = sprintf(
                    'Invalid param "variants": %s', $variantsStr
                );
                throw new \Miao\Router\Rule\Validator\Exception( $message );
            }
        }
    }

    public function getPattern()
    {
        return implode( '|', $this->_variants );
    }
}