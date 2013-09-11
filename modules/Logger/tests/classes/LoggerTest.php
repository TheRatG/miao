<?php
/**
 * @author vpak
 * @date 2013-09-11 17:09:15
 */

namespace Miao\Logger;

class LoggerTest extends \PHPUnit_Framework_TestCase
{
    public function testLog()
    {
        $sourceDir = \Miao\App::getInstance()
            ->getPath()
            ->getTestSourcesDir( __CLASS__ );
        $filename = $sourceDir . '/test_log';

        if ( !file_exists( $sourceDir ) )
        {
            mkdir( $sourceDir, 0777, true );
        }

        $logger = \Miao\Logger::factory( $filename );
        $logger->debug( 'Hello world' );

        $content = file_get_contents( $filename );
        $this->assertRegExp( '/Hello world/', $content );
        unlink( $filename );
    }
}