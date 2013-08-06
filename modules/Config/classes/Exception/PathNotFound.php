<?php
namespace Miao\Config\Exception;

use Miao\Config;

class PathNotFound extends Config\Exception
{
    public function __construct( $path )
    {
        $msg = sprintf( 'Path "%s" not found', $path );
        parent::__construct( $msg );
    }
}