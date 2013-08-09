<?php
namespace Miao\Autoload\Plugin;

use  Miao\Autoload;

class Standart extends Autoload\Plugin implements Autoload\PluginInterface
{
    public function getFilenameByClassName( $className )
    {
        $result = '';
        if ( strpos( $className, '\\' ) === false )
        {
            $items = explode( '_', $className );
        }
        else
        {
            $items = explode( '\\', $className );
        }
        if ( count( $items ) >= 2 && $items[ 0 ] == $this->getName() )
        {
            if ( !Autoload\ClassInfo::parse( $className )->isTest() )
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