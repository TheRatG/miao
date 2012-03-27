<?php
class Miao_Autoload_Test extends PHPUnit_Framework_TestCase
{
	/**
	 *
	 * @dataProvider providerTestRegister
	 * @param unknown_type $name
	 * @param unknown_type $libPath
	 */
	public function testRegister( $name, $plugin, $libPath )
	{
		$autoload = Miao_Autoload::getInstance();
		$autoload->registerItem( $name, $plugin, $libPath );

		$expected = $autoload->getPlugin( $name );
		$actual = 'Miao_Autoload_Plugin_' . $plugin;

		$condition = $expected instanceof $actual;
		$this->assertTrue( $condition );
	}

	public function providerTestRegister()
	{
		$data = array();

		$name = 'Miao';
		$plugin = 'Standart';
		$libPath = $this->_getLibPath();
		$data[] = array( $name, $plugin, $libPath );

		return $data;
	}

	/**
	 *
	 * @dataProvider providerTestGetFilenameByClassName
	 * @param unknown_type $className
	 * @param unknown_type $actual
	 */
	public function testGetFilenameByClassName( $className, $actual )
	{

		$autoload = Miao_Autoload::getInstance();
		$autoload->registerItem( 'Miao', 'Standart', $this->_getLibPath() );

		$expected = $autoload->getFilenameByClassName( $className );

		$this->assertEquals( $expected, $actual );
	}

	public function providerTestGetFilenameByClassName()
	{
		$data = array();

		$data[] = array(
			'Miao_Autoload_Plugin',
			$this->_getLibPath() . '/modules/Autoload/classes/Plugin.class.php' );

		$data[] = array(
			'Miao_Autoload_Test',
			$this->_getLibPath() . '/modules/Autoload/tests/classes/Autoload.class.Test.php' );

		$data[] = array( 'Miao_Autoload_UnknownClass', '' );

		return $data;
	}

	/**
	 * @dataProvider providerTestAutoload
	 * @param unknown_type $className
	 * @param unknown_type $exceptionName
	 */
	public function testAutoload( $className, $exceptionName = '' )
	{
		if ( $exceptionName )
		{
			$this->setExpectedException( $exceptionName );
		}

		$autoload = Miao_Autoload::getInstance();
		$autoload->registerItem( 'Miao', 'Standart', $this->_getLibPath() );

		$obj = new $className();
	}

	public function providerTestAutoload()
	{

		$data = array();

		$data[] = array( 'Miao_Autoload_Name' );

		$exceptionName = 'Miao_Autoload_Exception_FileNotFound';
		$data[] = array( 'Miao_Autoload_UnknownClass', $exceptionName );

		return $data;
	}

	public function testAutoloadStatic()
	{
		$autoload = Miao_Autoload::getInstance();
		$autoload->registerItem( 'Miao', 'Standart', $this->_getLibPath() );

		$exceptionName = 'Miao_Autoload_Exception_FileNotFound';
		$this->setExpectedException( $exceptionName );
		$obj = Miao_Autoload_UnknownClass::help();
	}

	protected function _getLibPath()
	{
		$result = realpath( __DIR__ . '/../../../../' );
		return $result;
	}
}