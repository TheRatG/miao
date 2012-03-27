<?php
class Miao_Path_Test extends PHPUnit_Framework_TestCase
{
	private $_root;
	private $_mainConfigFilename;

	public function setUp()
	{
		$this->_root = realpath( dirname( __FILE__ ) . '/../../../..' );
		$this->_mainConfigFilename = $this->_root . '/data/config.php';
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
			'Teleprog' => '/project_root/libs/teleprog/trunk/modules',
			'Miao' => '/project_root/libs/miao/tag/modules' );

		$data[] = array(
			$map,
			'Teleprog_FrontOffice',
			$map[ 'Teleprog' ] . '/FrontOffice/templates' );
		$data[] = array(
			$map,
			'Teleprog_FrontOffice_View',
			$map[ 'Teleprog' ] . '/FrontOffice/templates/View' );
		$data[] = array(
			$map,
			'Teleprog_FrontOffice_View_First',
			$map[ 'Teleprog' ] . '/FrontOffice/templates/View/First' );
		$data[] = array(
			$map,
			'Teleprog_FrontOffice_View_First_Second',
			$map[ 'Teleprog' ] . '/FrontOffice/templates/View/First/Second' );
		$data[] = array(
			$map,
			'Teleprog_FrontOffice_ViewBlock_First_Second',
			$map[ 'Teleprog' ] . '/FrontOffice/templates/ViewBlock/First/Second' );

		$data[] = array(
			$map,
			'Miao_BackOffice_View',
			$map[ 'Miao' ] . '/BackOffice/templates/View' );
		$data[] = array(
			$map,
			'Miao_BackOffice_View_First_Second',
			$map[ 'Miao' ] . '/BackOffice/templates/View/First/Second' );

		$data[] = array(
			$map,
			'Miao_DevOffice_View_First',
			$map[ 'Miao' ] . '/DevOffice/templates/View/First' );

		$data[] = array( $map, '', '', 'Miao_Autoload_Exception_InvalidClassName' );
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
	 * @dataProvider providerTestGetModuleRoot
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
		$path = Miao_Path::getDefaultInstance();
		$actual = $path->getModuleRoot( $className );
		$this->assertEquals( $expected, $actual );
	}

	public function providerTestGetModuleRoot()
	{
		$data = array();

		$path = Miao_Path::getDefaultInstance();
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
		$path = Miao_Path::getDefaultInstance();
		$result = $path->getModuleList( 'Miao' );

		$this->assertTrue( is_array( $result ) && !empty( $result ) );
	}

	/**
	 *
	 * @dataProvider providerTestGetFilenameByClassName
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

		$path = Miao_Path::getDefaultInstance();
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