<?php
namespace Miao\Autoload\Plugin;

use  Miao\Autoload;

class Standart extends Autoload\Plugin
{
    public function getFilenameByClassName( $className )
    {
        $items = explode( '\\', $className );
        $result = '';
        if ( count( $items ) >= 2 && $items[ 0 ] == $this->getName() )
        {
            if ( !Autoload\ClassInfo::isTest( $className ) )
            {
                $formatString = '%s/modules/%s/classes/%s.php';
            }
            else
            {
                $formatString = '%s/modules/%s/tests/classes/%s.php';
            }

            if ( count( $items ) == 2 )
            {
                $items[ 2 ] = $items[ 1 ];
            }
            $result = sprintf(
                $formatString, $this->getLibPath(), $items[ 1 ], implode( '/', array_slice( $items, 2 ) )
            );
        }
        return $result;
    }
}