<?php
namespace Miao\Autoload\Plugin;

use  Miao\Autoload;

class IncludePath extends Autoload\Plugin
{
    public function __construct( $name, $libPath )
    {
        parent::__construct( $name, $libPath );
        self::addIncludePath( $libPath );
    }

    public function getFilenameByClassName( $className )
    {
        $pathRoot = $this->getLibPath();
        $partPath = str_replace( '_', DIRECTORY_SEPARATOR, $className );
        $result = $pathRoot . DIRECTORY_SEPARATOR . $partPath . '.php';

        if ( !file_exists( $result ) )
        {
            $result = '';
        }

        return $result;
    }
}