<?php
class Miao_Autoload_Name_Test extends PHPUnit_Framework_TestCase
{
	/**
	 *
	 * @var Miao_Path
	 */
	private $_path;

	public function setUp()
	{
		$this->_path = Miao_Path::getInstance();
	}

	/**
	 *
	 * @dataProvider providerTestParseName
	 * @param string $name
	 */
	public function testParseName( $name, $actual, $exceptionName = '' )
	{
		$obj = new Miao_Autoload_Name( $this->_path );
		$obj->parse( $name );
		$expected = $obj->toArray();
		$this->assertEquals( $expected, $actual );
	}

	public function providerTestParseName()
	{
		$this->setUp();

		$data = array();

		$name = 'Miao';
		$actual = array(
			'type' => Miao_Autoload_Name::T_LIB,
			'name' => 'Miao',
			'lib' => 'Miao',
			'module' => '',
			'class' => '' );
		$exceptionName = '';
		$data[] = array( $name, $actual, $exceptionName );

		$name = 'Miao_Autoload';
		$actual = array(
			'type' => Miao_Autoload_Name::T_MODULE,
			'name' => 'Miao_Autoload',
			'lib' => 'Miao',
			'module' => 'Autoload',
			'class' => '' );
		$exceptionName = '';
		$data[] = array( $name, $actual, $exceptionName );

		$name = 'Miao_Autoload_Plugin';
		$actual = array(
			'type' => Miao_Autoload_Name::T_CLASS,
			'name' => 'Miao_Autoload_Plugin',
			'lib' => 'Miao',
			'module' => 'Autoload',
			'class' => 'Plugin' );
		$exceptionName = '';
		$data[] = array( $name, $actual, $exceptionName );

		return $data;
	}
}