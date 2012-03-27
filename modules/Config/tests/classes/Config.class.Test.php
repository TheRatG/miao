<?php
class Miao_Config_Test extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$path = Miao_Path::getDefaultInstance();
		$this->_path = $path;

		$sourceDir = Miao_PHPUnit::getSourceFolder( 'Miao_Config_TestConfig_Test' );
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
	 * @dataProvider providerTestModules
	 *
	 * @param $className unknown_type
	 * @param $expected unknown_type
	 * @param $exception unknown_type
	 */
	public function testModules( $className, $expected, $exception = '' )
	{
		$config = Miao_Config::Libs( $className );
		$actual = $config->toArray();
		$this->assertEquals( $expected, $actual );
	}

	public function providerTestModules()
	{
		$data = array();

		$data[] = array(
			'Miao_TestConfig',
			array(
				'firstParam' => 1,
				'secondParam' => 2,
				'View' => array(
					'firstViewParam' => 'view_param_1',
					'Article' => array( 'articleParam' => 'articleValue' ) ),
				'thirdParam' => 3,
				'PropertyFirst' => array( '__construct' => array( 'a', 'b' ) ),
				'PropertySecond' => array(
					'__construct' => array( 'test', array( 'a', array( 'b' ) ) ) ) ) );

		$data[] = array(
			'Miao_TestConfig_View',
			array(
				'firstViewParam' => 'view_param_1',
				'Article' => array( 'articleParam' => 'articleValue' ) ) );

		$data[] = array(
			'Miao_TestConfig_View_Article',
			array( 'articleParam' => 'articleValue' ) );

		return $data;
	}
}