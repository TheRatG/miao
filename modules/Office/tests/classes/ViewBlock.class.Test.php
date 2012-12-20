<?php
class Miao_Office_ViewBlock_Test extends PHPUnit_Framework_TestCase
{
	protected $_path;
	protected $_moduleRoot;

	public function setUp()
	{
		$path = Miao_Path::getInstance();
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
	 * @dataProvider providerTestFetch
	 *
	 * @param Miao_Office_ViewBlock $viewBlock
	 * @param array $templates
	 * @param unknown_type $expected
	 * @param unknown_type $exception
	 */
	public function testFetch( $viewBlock, $templates, $expected, $exception = '' )
	{
		$viewBlock->process();
		$actual = $viewBlock->fetch( $templates );
		$this->assertEquals( $expected, $actual );
	}

	public function providerTestFetch()
	{
		$data = array();

		$this->setUp();

		$path = $this->_path;

		$templatesDir = $path->getTemplateDir(
			'Miao_TestOffice_ViewBlock_TArticle' );
		$viewBlock = new Miao_TestOffice_ViewBlock_TArticle();
		$viewBlock->setTemplatesDir( $templatesDir );
		$data[] = array( $viewBlock, array( 'index.tpl' ), 'Article test' );
		$data[] = array( $viewBlock, 'index.tpl', 'Article test' );
		$data[] = array( $viewBlock, '', 'Article test' );

		return $data;
	}


	public function testTmplArray()
	{
		$path = $this->_path;
		$templatesDir = $path->getTemplateDir(
				'Miao_TestOffice_ViewBlock_TmplArray' );
		$viewBlock = new Miao_TestOffice_ViewBlock_TmplArray();
		$viewBlock->setTemplatesDir( $templatesDir );
		$viewBlock->process();
		$actual = $viewBlock->fetch( array('index.tpl') );

		$expected = 'title: title, body: TmplArray body';
		$this->assertEquals( $expected, $actual );
	}
}