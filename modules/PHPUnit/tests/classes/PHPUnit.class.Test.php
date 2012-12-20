<?php
class Miao_PHPUnit_Test extends PHPUnit_Framework_TestCase
{
	/**
	 *
	 * @dataProvider providerTestGetSourceFolder
	 * @param unknown_type $method
	 * @param unknown_type $expected
	 */
	public function testGetSourceFolder( $method, $expected )
	{
		$actual = Miao_PHPUnit::getSourceFolder( $method );

		$this->assertEquals( $expected, $actual );
	}

	public function providerTestGetSourceFolder()
	{
		$data = array();

		$path = Miao_Path::getInstance();

		$expected = $path->getModuleRoot( 'Miao_PHPUnit_Test' );
		$data[] = array(
			__METHOD__,
			$expected . '/tests/sources/PHPUnit/testGetSourceFolder' );

		$expected = $path->getModuleRoot( 'Miao_FrontOffice_ViewBlock_Test' );
		$data[] = array(
			'Miao_FrontOffice_ViewBlock_Test::providerTestFetch',
			$expected . '/tests/sources/ViewBlock/testFetch' );

		$expected = $path->getModuleRoot( 'Miao_TestOffice' );
		$data[] = array(
			'Miao_TestOffice_Test',
			$expected . '/tests/sources/TestOffice' );

		$expected = $path->getModuleRoot( 'Miao_TestOffice' );
		$data[] = array(
			'Miao_TestOffice',
			$expected . '/tests/sources/TestOffice' );

		return $data;
	}

}