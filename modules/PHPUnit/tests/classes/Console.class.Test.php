<?php
class Miao_PHPUnit_Console_Test extends PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider providerTestGetFilenameByClassName
	 *
	 * @param $className unknown_type
	 * @param $actual unknown_type
	 */
	public function testGetFilenameByClassName( $className, $actual )
	{
		$opts = array( 'no-run' => true );
		$remainingArgs = array( 'Miao_Autoload_Test' );
		$consoleObj = new Miao_PHPUnit_Console( $opts, $remainingArgs );
		$expected = $consoleObj->getFilenameByClassName( $className );

		$this->assertEquals( $expected, $actual );
	}

	public function providerTestGetFilenameByClassName()
	{
		$data = array();

		$data[] = array(
			'Miao_Autoload_Test',
			Miao_Autoload::getFilenameByClassName( 'Miao_Autoload_Test' ) );
		$data[] = array(
			'Miao_Autoload',
			Miao_Autoload::getFilenameByClassName( 'Miao_Autoload_Test' ) );

		return $data;
	}

	/**
	 * @dataProvider providerTestGetListFileListByModuleName
	 *
	 * @param $moduleName string
	 */
	public function testGetListFileListByModuleName( $moduleName, array $actual )
	{
		$opts = array( 'no-run' => true );
		$remainingArgs = array( 'Miao_Autoload_Test' );
		$consoleObj = new Miao_PHPUnit_Console( $opts, $remainingArgs );
		$expected = $consoleObj->getListFileListByModuleName( $moduleName );

		$this->assertTrue( array_diff( $expected, $actual ) === array() );
	}

	public function providerTestGetListFileListByModuleName()
	{
		$data = array();

		$items = array();
		$items[] = Miao_Autoload::getFilenameByClassName( 'Miao_FrontOffice_TemplatesEngine_PhpNative_Test' );
		$data[] = array( 'Miao_FrontOffice_TemplatesEngine', $items );

		$items = array();
		$items[] = Miao_Autoload::getFilenameByClassName( 'Miao_FrontOffice_TemplatesEngine_PhpNative_Test' );
		$data[] = array( 'Miao_FrontOffice_TemplatesEngine_Test', $items );

		$items = array();
		$items[] = Miao_Autoload::getFilenameByClassName( 'Miao_Autoload_Plugin_Test' );
		$items[] = Miao_Autoload::getFilenameByClassName( 'Miao_Autoload_Plugin_Modules_Test' );
		$items[] = Miao_Autoload::getFilenameByClassName( 'Miao_Autoload_Plugin_PHPExcel_Test' );
		$items[] = Miao_Autoload::getFilenameByClassName( 'Miao_Autoload_Plugin_Project_Test' );
		$items[] = Miao_Autoload::getFilenameByClassName( 'Miao_Autoload_Plugin_Zend_Test' );
		$items[] = Miao_Autoload::getFilenameByClassName( 'Miao_Autoload_Test' );
		$items[] = Miao_Autoload::getFilenameByClassName( 'Miao_Autoload_Name_Test' );
		$data[] = array( 'Miao_Autoload', $items );

		$items = array();
		$items[] = Miao_Autoload::getFilenameByClassName( 'Miao_Autoload_Plugin_Test' );
		$items[] = Miao_Autoload::getFilenameByClassName( 'Miao_Autoload_Plugin_Modules_Test' );
		$items[] = Miao_Autoload::getFilenameByClassName( 'Miao_Autoload_Plugin_PHPExcel_Test' );
		$items[] = Miao_Autoload::getFilenameByClassName( 'Miao_Autoload_Plugin_Project_Test' );
		$items[] = Miao_Autoload::getFilenameByClassName( 'Miao_Autoload_Plugin_Zend_Test' );
		$data[] = array( 'Miao_Autoload_Plugin', $items );

		return $data;
	}

	/**
	 * @dataProvider providerTestGetListFilenameByLibName
	 */
	public function testGetListFilenameByLibName( $libName, $exceptionName = '' )
	{
		if ( !empty( $exceptionName ) )
		{
			$this->setExpectedException( $exceptionName );
		}

		$opts = array( 'no-run' => true );
		$remainingArgs = array( 'Miao_Autoload_Test' );

		$consoleObj = new Miao_PHPUnit_Console( $opts, $remainingArgs );
		$expected = $consoleObj->getListFileListByLibName( $libName );
		$condition = is_array( $expected ) && !empty( $expected );
		$this->assertTrue( $condition );

		$condition2 = true;
		foreach ( $expected as $item )
		{
			if ( false === strpos( $item, 'class.Test.php' ) )
			{
				$condition2 = false;
				break;
			}
		}
		$this->assertTrue( $condition2 );
		//$this->assertEquals( count( $expected ), 36 );
	}

	public function providerTestGetListFilenameByLibName()
	{
		$data = array();

		$data[] = array( 'Miao' );
		$data[] = array( 'ABSDnakjsdnoasdnoad', 'Miao_PHPUnit_Exception' );

		return $data;
	}
}