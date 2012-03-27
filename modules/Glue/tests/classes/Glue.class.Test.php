<?php
/*
 * $files = array(); $compact = true; $glue = new Glue( $files ); $glue->weld(
 * $compact );
 */
class Miao_GlueTest extends PHPUnit_Framework_TestCase
{
	private $_tmp = 'glue_tmp.php';

	public function tearDown()
	{
		$sourceDir = realpath( __DIR__ . '/../sources/' );
		if ( file_exists( $this->_tmp ) )
		{
			unlink( $this->_tmp );
		}
	}

	public function testConstruct()
	{
		$exceptionName = 'Miao_Glue_Exception';
		$this->setExpectedException( $exceptionName );

		$files = 'asd/Glue.class.Test.php';
		$obj = new Miao_Glue( array( $files ) );
	}

	/**
	 * @dataProvider providerTestWeld
	 *
	 * @param $files array
	 * @param $compact unknown_type
	 * @param $actual unknown_type
	 * @param $exceptionName unknown_type
	 */
	public function testWeld( array $files, $compact, $actual, $exceptionName = '' )
	{
		if ( !empty( $exceptionName ) )
		{
			$this->setExpectedException( $exceptionName );
		}

		$expected = $this->_tmp;

		$obj = new Miao_Glue( $files );
		$result = $obj->weld( $expected, $compact );

		$this->assertTrue( $result );
		$this->assertFileEquals( $expected, $actual );
	}

	public function providerTestWeld()
	{
		$data = array();

		$sourceDir = realpath( __DIR__ . '/../sources/Glue/testWeld' );

		$data[] = array(
			array( $sourceDir . '/1/one.php', $sourceDir . '/1/two.php' ),
			false,
			$sourceDir . '/1/actual.php' );

		$data[] = array(
			array( $sourceDir . '/2/one.php', $sourceDir . '/2/two.php' ),
			false,
			$sourceDir . '/2/actual.php' );

		$data[] = array(
			array( $sourceDir . '/2/one.php', $sourceDir . '/2/two.php' ),
			true,
			$sourceDir . '/2/actual2.php' );

		$data[] = array(
				array( $sourceDir . '/3/one.php', $sourceDir . '/3/two.php' ),
				true,
				$sourceDir . '/3/actual.php' );

		$data[] = array(
				array( $sourceDir . '/4/one.php', $sourceDir . '/4/two.php' ),
				true,
				$sourceDir . '/4/actual.php' );

		return $data;
	}

	/**
	 * @dataProvider providerTestWeldDir
	 *
	 * @param $dirname unknown_type
	 * @param $actual unknown_type
	 * @param $exceptionName unknown_type
	 */
	public function testWeldDir( $dirname, $actual, $exceptionName = '' )
	{
		$expected = $this->_tmp;

		$files = Miao_Glue::getFileList( $dirname );
		$obj = new Miao_Glue( $files );
		$result = $obj->weld( $expected, true );

		$this->assertTrue( $result );
		$this->assertFileEquals( $expected, $actual );
	}

	public function providerTestWeldDir()
	{
		$data = array();

		$sourceDir = realpath( __DIR__ . '/../sources/Glue/testWeldDir' );

		$data[] = array( $sourceDir . '/1', $sourceDir . '/actual_1.php' );

		return $data;
	}
}