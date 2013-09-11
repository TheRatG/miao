<?php
/**
 * User: vpak
 * Date: 11.09.13
 * Time: 17:03
 */

namespace Miao;

class Logger extends \Monolog\Logger
{
    /**
     * @param string $filename
     * @param bool $verbose
     * @param $logLevel
     * @return Logger
     */
    static public function factory( $filename = '', $verbose = false, $logLevel = \Monolog\Logger::DEBUG )
    {
        $handlers  = array();

        if ( $verbose )
        {
            $handlers[] = new \Monolog\Handler\StreamHandler( 'php://output' );
        }
        if ( $filename )
        {
            $handlers[] = new \Monolog\Handler\StreamHandler( $filename, $logLevel );
        }
        if ( !$filename && !$verbose )
        {
            $handlers[] = new \Monolog\Handler\NullHandler( $logLevel );
        }
        $logger = new self( __CLASS__, $handlers );
        return $logger;
    }
}