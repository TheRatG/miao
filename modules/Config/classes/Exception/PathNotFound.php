<?php
/**
 * User: vpak
 * Date: 02.09.13
 * Time: 15:53
 */

namespace Miao\Config\Exception;

class PathNotFound extends \Miao\Config\Exception
{
    public function __construct( $path )
    {
        $msg = sprintf( 'Path "%s" not found', $path );
        parent::__construct( $msg );
    }
}