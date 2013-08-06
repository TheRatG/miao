<?php
namespace Miao\Config\Exception;

use Miao\Config;

class InvalidPath extends Config\Exception
{
    public function __construct( $path, $reason )
    {
        $msg = sprintf( 'Invalid path "%s": %s', $path, $reason );
        parent::__construct( $msg );
    }
}