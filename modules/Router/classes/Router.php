<?php
/**
 * User: vpak
 * Date: 13.09.13
 * Time: 18:15
 */

namespace Miao;

class Router
{
    static public function getRequestMethod()
    {
        $result = ( isset( $_SERVER[ 'REQUEST_METHOD' ] ) ) ? $_SERVER[ 'REQUEST_METHOD' ] : 'GET';
        return $result;
    }
}