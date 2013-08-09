<?php
namespace Miao\Autoload\Plugin;

use  Miao\Autoload;

class PHPUnit extends Autoload\Plugin implements Autoload\PluginInterface
{
    public function __construct( $name, $libPath )
    {
        parent::__construct( $name, $libPath );
        self::addIncludePath( $libPath );

        require_once 'PHPUnit' . DIRECTORY_SEPARATOR . 'Autoload.php';
    }

    public function getFilenameByClassName( $className )
    {
        $items = explode( '_', $className );
        if ( count( $items ) < 2 || $items[ 0 ] != 'PHPUnit' )
        {
            return '';
        }
        $result = sprintf( '%s/%s.php', $this->getLibPath(), implode( '/', $items ) );
        return $result;
    }
}