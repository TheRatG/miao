<?php

namespace Miao\Log;

class LogTest extends \PHPUnit_Framework_TestCase
{
    public function testLog()
    {
        $sourceDir = \Miao\App::getInstance()->getPath()->getTestSourcesDir( __CLASS__ );
        $filename = $sourceDir . '/test_log';

        $log = \Miao\Log::factory( $filename );
        $msg = 'Hello world';
        $log->err( $msg );

        $content = file_get_contents( $filename );
        $this->assertTrue( !empty( $content ) );
        unlink( $filename );
    }

    public function testFileMode()
    {
        $sourceDir = \Miao\App::getInstance()->getPath()->getTestSourcesDir( __CLASS__ );
        $filename = $sourceDir . '/test_log';

        $log = \Miao\Log::factory( $filename );
        $msg = 'Hello world';
        $log->err( $msg );


        $expected = '0777';
        $actual = substr( sprintf( '%o', fileperms( $filename ) ), -4 );

        $this->assertEquals( $expected, $actual );

        unlink( $filename );
    }
}