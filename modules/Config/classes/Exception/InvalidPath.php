<?php
/**
 * User: vpak
 * Date: 02.09.13
 * Time: 15:54
 */

namespace Miao\Config\Exception;

class InvalidPath extends \Miao\Config\Exception
{
    public function __construct( $path, $reason )
    {
        $msg = sprintf( 'Invalid path "%s": %s', $path, $reason );
        parent::__construct( $msg );
    }
}