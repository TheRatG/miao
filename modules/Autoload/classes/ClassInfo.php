<?php
namespace Miao\Autoload;

class ClassInfo
{
    static public function isTest( $className )
    {
        $result = false;

        $pos = strpos( strrev( $className ), 'tseT' );
        if ( 0 === $pos )
        {
            $result = true;
        }
        return $result;
    }
}