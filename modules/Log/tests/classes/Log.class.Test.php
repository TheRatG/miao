<?php
class Miao_Log_Test extends PHPUnit_Framework_TestCase
{

	public function testLog()
	{
		$sourceDir = Miao_PHPUnit::getSourceFolder( __CLASS__ );
		$filename = $sourceDir . '/unittest_log';

		$log = Miao_Log::easyFactory( $filename );
		$msg = 'Hello world';
		$log->err( $msg );

		$content = file_get_contents( $filename );
		$this->assertTrue( !empty( $content ) );
		unlink( $filename );
	}

	public function testFileMode()
	{
		$sourceDir = Miao_PHPUnit::getSourceFolder( __CLASS__ );
		$filename = $sourceDir . '/unittest_log';

		$log = Miao_Log::easyFactory( $filename );
		$msg = 'Hello world';
		$log->err( $msg );


		$expected = '0666';
		$actual = substr( sprintf( '%o', fileperms( $filename ) ), -4 );

		$this->assertEquals( $expected, $actual );

		unlink( $filename );
	}
}