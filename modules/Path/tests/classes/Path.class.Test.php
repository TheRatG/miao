<?php
class Miao_Path_Test extends PHPUnit_Framework_TestCase
{
	private $_root;
	private $_mainConfigFilename;

	public function setUp()
	{
		$this->_root = realpath( dirname( __FILE__ ) . '/../../../..' );
		$this->_mainConfigFilename = $this->_root . '/modules/Path/tests/sources/config.php';
	}

	public function testCreate()
	{
		$path = new Miao_Path( $this->_root, $this->_mainConfigFilename, array(
			'test' => 'test' ) );
		$this->assertTrue( $path instanceof Miao_Path );
	}

	/**
	 * @dataProvider providerTestGetRoot
	 */
	public function testGetRoot( $map, $libName, $expectedPath, $expectedException = '' )
	{
		if ( $expectedException )
		{
			$this->setExpectedException( $expectedException );
		}
		$path = new Miao_Path( $this->_root, $this->_mainConfigFilename, $map );
		$this->assertEquals( $expectedPath, $path->getRootByLibName( $libName ) );
	}
	public function providerTestGetRoot()
	{
		$data = array();
		$data[] = array( array(), '', '', 'Miao_Path_Exception_EmptyMap' );
		$data[] = array(
			array( 'First' => '' ),
			'Second',
			'',
			'Miao_Path_Exception_LibNotFound' );
		$data[] = array(
			array(
				'Project' => '/project/trunk/modules',
				'Miao' => '/miao/tag/modules' ),
			'Project',
			'/project/trunk/modules' );
		$data[] = array(
			array(
				'Project' => '/project/trunk/modules',
				'Miao' => '/miao/tag/modules' ),
			'Miao',
			'/miao/tag/modules' );
		return $data;
	}

	/**
	 * @dataProvider providerTestGetTemplateDir
	 */
	public function testGetTemplateDir( $map, $className, $expectedPath, $expectedException = '' )
	{
		if ( $expectedException )
		{
			$this->setExpectedException( $expectedException );
		}
		$path = new Miao_Path( $this->_root, $this->_mainConfigFilename, $map );
		$actualPath = $path->getTemplateDir( $className );
		$this->assertEquals( $expectedPath, $actualPath );
	}
	public function providerTestGetTemplateDir()
	{
		$data = array();

		$map = array(
			'Teleprog' => '/project_root/libs/teleprog/trunk',
			'Miao' => '/project_root/libs/miao/tag' );

		$data[] = array(
			$map,
			'Teleprog_FrontOffice',
			$map[ 'Teleprog' ] . '/modules/FrontOffice/templates' );
		$data[] = array(
			$map,
			'Teleprog_FrontOffice_View',
			$map[ 'Teleprog' ] . '/modules/FrontOffice/templates/View' );
		$data[] = array(
			$map,
			'Teleprog_FrontOffice_View_First',
			$map[ 'Teleprog' ] . '/modules/FrontOffice/templates/View/First' );
		$data[] = array(
			$map,
			'Teleprog_FrontOffice_View_First_Second',
			$map[ 'Teleprog' ] . '/modules/FrontOffice/templates/View/First/Second' );
		$data[] = array(
			$map,
			'Teleprog_FrontOffice_ViewBlock_First_Second',
			$map[ 'Teleprog' ] . '/modules/FrontOffice/templates/ViewBlock/First/Second' );

		$data[] = array(
			$map,
			'Miao_BackOffice_View',
			$map[ 'Miao' ] . '/modules/BackOffice/templates/View' );
		$data[] = array(
			$map,
			'Miao_BackOffice_View_First_Second',
			$map[ 'Miao' ] . '/modules/BackOffice/templates/View/First/Second' );

		$data[] = array(
			$map,
			'Miao_DevOffice_View_First',
			$map[ 'Miao' ] . '/modules/DevOffice/templates/View/First' );

		$data[] = array(
			$map,
			'',
			'',
			'Miao_Autoload_Exception_InvalidClassName' );
		$data[] = array(
			$map,
			'Miao',
			'',
			'Miao_Autoload_Exception_InvalidClassName' );
		$data[] = array( $map, 'Error_', '', 'Miao_Path_Exception_LibNotFound' );
		$data[] = array( $map, '_Error', '', 'Miao_Path_Exception_LibNotFound' );

		return $data;
	}

	/**
	 *
	 *
	 * @dataProvider providerTestGetModuleRoot
	 *
	 * @param unknown_type $className
	 * @param unknown_type $expected
	 * @param unknown_type $exceptionName
	 */
	public function testGetModuleRoot( $className, $expected, $expectedException = '' )
	{
		if ( $expectedException )
		{
			$this->setExpectedException( $expectedException );
		}
		$path = Miao_Path::getInstance();
		$actual = $path->getModuleRoot( $className );
		$this->assertEquals( $expected, $actual );
	}
	public function providerTestGetModuleRoot()
	{
		$data = array();

		$path = Miao_Path::getInstance();
		$libDir = $path->getRootByLibName( 'Miao' ) . '/modules';

		$data[] = array( 'Miao_Autoload', $libDir . '/Autoload' );
		$data[] = array( 'Miao_Path', $libDir . '/Path' );
		$data[] = array( 'Miao_FrontOffice', $libDir . '/FrontOffice' );
		$data[] = array(
			'Miao_FrontOffice_View_Exception',
			$libDir . '/FrontOffice' );
		$data[] = array( 'Miao_Path_Test', $libDir . '/Path' );

		$expectedException = 'Miao_Path_Exception_LibNotFound';
		$data[] = array(
			'DRD_FrontOffice_View_Exception',
			'',
			$expectedException );

		return $data;
	}
	public function testGetModuleList()
	{
		$path = Miao_Path::getInstance();
		$result = $path->getModuleList( 'Miao' );

		$this->assertTrue( is_array( $result ) && !empty( $result ) );
	}

	/**
	 *
	 *
	 * @dataProvider providerTestGetFilenameByClassName
	 *
	 * @param unknown_type $className
	 * @param unknown_type $actual
	 * @param unknown_type $expectedException
	 */
	public function testGetFilenameByClassName( $className, $actual, $expectedException = '' )
	{
		if ( $expectedException )
		{
			$this->setExpectedException( $expectedException );
		}

		$path = Miao_Path::getInstance();
		$expected = $path->getFilenameByClassName( $className );
		$this->assertEquals( $expected, $actual );
	}
	public function providerTestGetFilenameByClassName()
	{
		$data = array();

		$this->setUp();

		$data[] = array(
			'Miao_Autoload',
			$this->_root . '/modules/Autoload/classes/Autoload.class.php' );

		$data[] = array(
			'Miao_Autoload_Plugin',
			$this->_root . '/modules/Autoload/classes/Plugin.class.php' );

		return $data;
	}
}