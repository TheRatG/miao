<?php
/**
 * User: vpak
 * Date: 13.09.13
 * Time: 18:15
 */

namespace Miao;

class Router
{
    static public function checkAndReturnParam( array $config, $param, $default = null )
    {
        if ( !array_key_exists( $param, $config ) && is_null( $default ) )
        {
            $message = sprintf( 'Invalid config: need "%s" param', $param );
            throw new \Miao\Router\Exception( $message );
        }
        $result = !empty( $config[ $param ] ) ? $config[ $param ] : $default;
        return $result;
    }
}