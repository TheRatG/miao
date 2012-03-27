<?php
/**
 * Search config
 * Get specific section by class name
 * Get instance
 *
 * @author vpak
 *
 */
class Miao_Config_Instance_Test extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$path = Miao_Path::getDefaultInstance();
		$this->_path = $path;

		$sourceDir = Miao_PHPUnit::getSourceFolder(
			'Miao_Config_TestConfig_Test' );
		$moduleRoot = $path->getModuleRoot( 'Miao_TestConfig' );
		Miao_PHPUnit::copyr( $sourceDir, $moduleRoot );

		$this->_moduleRoot = $moduleRoot;
	}

	public function tearDown()
	{
		Miao_PHPUnit::rmdirr( $this->_moduleRoot );
	}

	/**
	 *
	 * @dataProvider providerTestGet
	 */
	public function testGet( $className, $paramSection = '__construct', $expectedList, $exceptionName = '' )
	{
		if ( !empty( $exceptionName ) )
		{
			$this->setExpectedException( $exceptionName );
		}

		$obj = Miao_Config_Instance::get( $className, $paramSection );
		$this->assertInstanceOf( $className, $obj );

		foreach ( $expectedList as $key => $expected )
		{
			$actual = $obj->{$key};
			$this->assertEquals( $expected, $actual );
		}
	}

	public function providerTestGet()
	{
		$data = array();

		$data[] = array(
			'Miao_TestConfig_PropertyFirst',
			'__construct',
			array( 'a' => 'a', 'b' => 'b' ) );

		$data[] = array(
			'Miao_TestConfig_PropertySecond',
			'__construct',
			array( 'a' => 'test', 'b' => array( 'a', array( 'b' ) ) ) );

		$data[] = array(
			'Miao_TestConfig_PropertyThird',
			'__construct',
			array(),
			'Miao_Config_Exception_PathNotFound' );

		return $data;
	}
}