<?php
class Miao_Log_Test extends PHPUnit_Framework_TestCase
{
	public function testLog()
	{
		$sourceDir = Miao_PHPUnit::getSourceFolder( __CLASS__ );
		$filename = $sourceDir . '/unittest_log';

		$log = Miao_Log::factory2( $filename );
		$msg = 'Hello world';
		$log->err( $msg );

		$content = file_get_contents( $filename );
		$this->assertTrue( !empty( $content ) );
		unlink( $filename );
	}
}