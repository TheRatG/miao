<?php
class Miao_Office_TemplatesEngine_PhpNative_Test extends PHPUnit_Framework_TestCase
{
	protected $_path;
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
	}

	public function tearDown()
	{
		Miao_PHPUnit::rmdirr( $this->_moduleRoot );
	}

	/**
	 *
	 * @dataProvider providerTestConstruct
	 * @param unknown_type $templateDir
	 * @param unknown_type $debugMode
	 */
	public function testConstruct( $templatesDir, $debugMode )
	{
		$obj = new Miao_Office_TemplatesEngine_PhpNative( $templatesDir, $debugMode );

		$expected = $templatesDir . DIRECTORY_SEPARATOR;
		$actual = $obj->getTemplatesDir();
		$this->assertEquals( $expected, $actual );

		$expected = $debugMode;
		$actual = $obj->getDebugMode();
		$this->assertEquals( $expected, $actual );
	}

	public function providerTestConstruct()
	{
		$data = array();

		$templatesDir = '/www/templates';
		$debugMode = false;
		$data[] = array( $templatesDir, $debugMode );

		$templatesDir = '/www/templates/aaaaaaa';
		$debugMode = true;
		$data[] = array( $templatesDir, $debugMode );

		return $data;
	}

	/**
	 *
	 * @dataProvider providerTestAddBlock
	 * @param unknown_type $name
	 * @param unknown_type $viewBlock
	 */
	public function testAddBlock( $name, $viewBlock )
	{
		$obj = new Miao_Office_TemplatesEngine_PhpNative( '/qqqq', false );
		$obj->addBlock( $name, $viewBlock );
		$actual = $obj->getBlock( $name );
		$expected = $viewBlock;
		$this->assertEquals( $expected, $actual );
	}

	public function providerTestAddBlock()
	{
		$data = array();

		$this->setUp();

		$path = $this->_path;
		$templatesDir = $path->getTemplateDir(
			'Miao_Office_ViewBlock_TArticle' );
		$viewBlock = new Miao_TestOffice_ViewBlock_TArticle();
		$viewBlock->setTemplatesDir( $templatesDir );
		$data[] = array( 'TArticle', $viewBlock );

		return $data;
	}
}