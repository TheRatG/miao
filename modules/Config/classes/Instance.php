<?php
/**
 * User: vpak
 * Date: 26.09.13
 * Time: 12:33
 */

namespace Miao\Config;

class Instance
{
    static public function get( $className, $paramSection = '__construct' )
    {
        $configObj = \Miao\App::config( $className );
        $params = $configObj->get( $paramSection );
        if ( is_string( $params ) )
        {
            $params = array();
        }
        $rc = new \ReflectionClass( $className );
        $result = $rc->newInstanceArgs( $params );

        return $result;
    }
}