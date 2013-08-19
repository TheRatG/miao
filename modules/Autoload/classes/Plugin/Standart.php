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

            $module = $items[ 1 ];
            if ( count( $items ) == 2 )
            {
                $items[ 2 ] = $items[ 1 ];

                $pos = strpos( strrev( $module ), 'tseT' );
                if ( 0 === $pos )
                {
                    $module = substr( $module, 0, strrpos( $module, 'Test' ) );
                }
            }
            $result = sprintf(
                $formatString, $this->getLibPath(), $module, implode( '/', array_slice( $items, 2 ) )
            );
        }
        return $result;
    }
}