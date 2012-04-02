<?php
class Miao_Office_View_Test extends PHPUnit_Framework_TestCase
{
	protected $_templatesObj;
	protected $_moduleRoot;

	public function setUp()
	{
		$path = Miao_Path::getDefaultInstance();
		$this->_path = $path;

		$sourceDir = Miao_PHPUnit::getSourceFolder(
			'Miao_Office_TestOffice_Test' );
		$moduleRoot = $path->getModuleRoot( 'Miao_TestOffice' );
		Miao_PHPUnit::copyr( $sourceDir, $moduleRoot );

		$this->_moduleRoot = $moduleRoot;

		$path = Miao_Path::getDefaultInstance();
		$templatesDir = $path->getModuleRoot( 'Miao_TestOffice_View_Main' ) . '/templates/layouts';

		$debugMode = true;
		$this->_templatesObj = new Miao_Office_TemplatesEngine_PhpNative( $templatesDir, $debugMode );
	}

	public function tearDown()
	{
		Miao_PHPUnit::rmdirr( $this->_moduleRoot );
	}

	/**
	 *
	 * @dataProvider providerTestConstruct
	 * @param unknown_type $templateObj
	 */
	public function testConstruct( $templateObj, $expected )
	{
		$obj = new Miao_TestOffice_View_Main( $templateObj );
		$actual = $obj->getTemplateObj();

		$this->assertEquals( $expected, $actual );
	}

	public function providerTestConstruct()
	{
		$data = array();

		$obj = new Miao_Office_TemplatesEngine_PhpNative( '/real.path', false );

		$data[] = array( $obj, $obj );

		return $data;
	}

	/**
	 * @dataProvider providerTestLayout
	 * Set and Get Test
	 * @param unknown_type $layout
	 */
	public function testLayout( $layout, $expected )
	{
		$view = new Miao_TestOffice_View_Main( $this->_templatesObj );
		$view->setLayout( $layout );

		$actual = $view->getLayout();

		$this->assertEquals( $expected, $actual );
	}

	public function providerTestLayout()
	{
		$data = array();

		$data[] = array( '', 'layouts/index.tpl' );
		$data[] = array( 'index.tpl', 'index.tpl' );
		$data[] = array( 'rss_includes.xml.tpl', 'rss_includes.xml.tpl' );

		return $data;
	}

	public function testAddBlock()
	{

	}

	/**
	 *
	 * @dataProvider providerTestFetch
	 * @param string $layout
	 */
	public function testFetch( $layout, $expected )
	{
		$view = new Miao_TestOffice_View_Main( $this->_templatesObj );

		$actual = $view->fetch( $layout );
		$this->assertEquals( $expected, $actual );
	}

	public function providerTestFetch()
	{
		$data = array();

		$data[] = array( 'index.tpl', 'index layout' );
		$data[] = array( 'rss_include.xml.tpl', 'rss_include.xml layout' );

		return $data;
	}

	/**
	 *
	 * @dataProvider providerTestFetchWithViewTemplate
	 *
	 * @param unknown_type $viewClassName
	 * @param unknown_type $layout
	 */
	public function testFetchWithViewTemplate( $viewClassName, $layout, $expected )
	{
		$view = new $viewClassName( $this->_templatesObj );
		$actual = $view->fetch( $layout );
		$this->assertEquals( $expected, $actual );
	}

	public function providerTestFetchWithViewTemplate()
	{
		$data = array();

		$data[] = array(
			'Miao_TestOffice_View_Main',
			'with_view.tpl',
			'with view view main' );
		$data[] = array(
			'Miao_TestOffice_View_Article_Item',
			'with_view.tpl',
			'with view view article item' );

		$source = Miao_PHPUnit::getSourceFolder( __METHOD__ );
		$data[] = array(
			'Miao_TestOffice_View_Article_List',
			'with_view.tpl',
			file_get_contents( $source . '/res1.html' ) );

		$data[] = array(
			'Miao_TestOffice_View_Article_Slave',
			'with_view.tpl',
			file_get_contents( $source . '/res2.html' ) );

		return $data;
	}
}