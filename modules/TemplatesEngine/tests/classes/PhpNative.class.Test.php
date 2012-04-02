<?php
class Miao_TemplatesEngine_PhpNative_Test extends PHPUnit_Framework_TestCase
{
	protected $_templatesDir;
	protected $_tmplName = 'template.tpl';

	public function setUp()
	{
		$this->_templatesDir = Miao_PHPUnit::getSourceFolder( __METHOD__ );
	}

	public function tearDown()
	{
		$tplFilename = $this->_templatesDir . DIRECTORY_SEPARATOR . $this->_tmplName;
		if ( file_exists( $tplFilename ) )
		{
			unlink( $tplFilename );
		}
	}

	/**
	 * @dataProvider data4SetTemplatesDir
	 *
	 */
	public function testSetTemplatesDir( $in, $out )
	{
		$pn = new Miao_TemplatesEngine_PhpNative();
		$pn->setTemplatesDir( $in );
		$this->assertEquals( $pn->getTemplatesDir(), $out );
	}

	/**
	 * @dataProvider data4SetTemplatesDir
	 *
	 */
	public function testGetTemplatesDir( $in, $out )
	{
		$pn = new Miao_TemplatesEngine_PhpNative( $in );
		$this->assertEquals( $pn->getTemplatesDir(), $out );
	}

	public function data4SetTemplatesDir()
	{
		return array(
			array( '/www/tmp/', '/www/tmp/' ),
			array( '/www/tmp', '/www/tmp/' ) );
	}

	/**
	 * @dataProvider data4SetValueOf
	 *
	 */
	public function testSetValueOf( $name, $value )
	{
		$this->_generateTemplate();
		$pn = new Miao_TemplatesEngine_PhpNative( $this->_templatesDir );

		$pn->setValueOf( $name, $value );
		$res2 = $pn->fetch( $this->_tmplName );
		$this->assertEquals( $res2, $value );
	}

	public function data4SetValueOf()
	{
		return array(
			array( 'val_name', 59 ),
			array( 'val_name', '756' ),
			array( 'val_name', 'текст строки' ),
			array( 'val_name', 'бяка' ) );
	}

	/**
	 * @dataProvider providerTestSetValueOfByArray
	 * @param array $data
	 */
	public function testSetValueOfByArray( array $data, $actual )
	{
		$keys = array_keys( $data );
		array_shift( $keys );
		$this->_generateTemplate( 'val_name', $keys );
		$pn = new Miao_TemplatesEngine_PhpNative( $this->_templatesDir );

		$pn->setValueOfByArray( $data );
		$expected = $pn->fetch( $this->_tmplName );

		$this->assertEquals( $expected, $actual );
	}

	public function providerTestSetValueOfByArray()
	{
		$data = array();

		$data[] = array( array( 'val_name' => 59, 'val_name2' => '69' ), '5969' );
		$data[] = array( array( 'val_name' => 59 ), '59' );
		$data[] = array( array( 'val_name' => 59, 'val_name2' => '69', 'habr' => '79' ), '596979' );

		return $data;
	}

	public function testExceptionOnVariableNotFound()
	{
		$exceptionName = 'Miao_TemplatesEngine_Exception_OnVariableNotFound';

		$pn = new Miao_TemplatesEngine_PhpNative( $this->_templatesDir, true );
		$this->_generateTemplate( 'name1', array( 'name2', 'name3', 'name4' ) );
		$res = $pn->fetch( $this->_tmplName );

		$condition = !( false === strpos( $res, 'Tmp variable (name1) not found' ) );
		$this->assertTrue( $condition );
	}

	public function testResetTemplateVariables()
	{
		$pn = new Miao_TemplatesEngine_PhpNative( $this->_templatesDir, false );

		$pn->setValueOf( 'name1', 'Начало' );
		$pn->setValueOf( 'name2', ' середина' );
		$pn->setValueOf( 'name3', ' середина2' );
		$pn->setValueOf( 'name4', ' конец.' );

		$this->_generateTemplate( 'name1', array( 'name2', 'name3', 'name4' ) );
		$res = $pn->fetch( $this->_tmplName );
		$this->assertEquals( $res, 'Начало середина середина2 конец.' );

		$pn->resetTemplateVariables();
		$res = $pn->fetch( $this->_tmplName );
		$this->assertEquals( $res, '' );
	}

	/**
	 * @dataProvider data4fetch
	 */
	public function testFetch( $tpl_name, $file_name = null )
	{
		$pn = new Miao_TemplatesEngine_PhpNative( $this->_templatesDir, false );

		$pn->setValueOf( 'name1', 'Начало' );
		$pn->setValueOf( 'name2', ' середина' );
		$pn->setValueOf( 'name3', 5 );
		$pn->setValueOf( 'name4', ' конец.' );

		$this->_generateTemplate( 'name1', array( 'name2', 'name3', 'name4' ) );

		if ( !is_null( $file_name ) )
		{
			$file_name = $this->_templatesDir . DIRECTORY_SEPARATOR . $file_name;
		}

		$res = $pn->fetch( $this->_tmplName, $file_name );

		if ( $file_name )
		{
			$res = file_get_contents( $file_name );
		}
		$this->assertEquals( $res, 'Начало середина5 конец.' );
	}

	public function data4fetch()
	{
		return array(
			array( $this->_tmplName ),
			array( $this->_tmplName, 'test_output.php' ) );
	}

	public function testDisplay()
	{
		$pn = new Miao_TemplatesEngine_PhpNative( $this->_templatesDir );

		$pn->setValueOf( 'name1', 'Начало' );
		$pn->setValueOf( 'name2', ' середина' );
		$pn->setValueOf( 'name3', 5 );
		$pn->setValueOf( 'name4', ' конец.' );

		$this->_generateTemplate( 'name1', array( 'name2', 'name3', 'name4' ) );

		ob_start();
		$res = $pn->display( $this->_tmplName );
		$res = ob_get_contents();
		ob_end_clean();

		$this->assertEquals( $res, 'Начало середина5 конец.' );
	}

	protected function _generateTemplate( $valName = 'val_name', $additionalVars = false )
	{
		$tplFilename = $this->_templatesDir . DIRECTORY_SEPARATOR . $this->_tmplName;
		if ( file_exists( $tplFilename ) )
		{
			unlink( $tplFilename );
		}
		$s = '<?=$this->_getValueOf(\'' . $valName . '\' )?>';
		if ( is_array( $additionalVars ) && count( $additionalVars ) > 0 )
		{
			foreach ( $additionalVars as $v )
			{
				$s .= '<?=$this->_getValueOf(\'' . $v . '\' )?>';
			}
		}
		file_put_contents( $tplFilename, $s );
	}

	/**
	 * @dataProvider providerTestException
	 * @param unknown_type $tmplDir
	 * @param unknown_type $debugMode
	 * @param unknown_type $templateFilename
	 * @param unknown_type $actual
	 * @param unknown_type $exception
	 */
	public function testException( $tmplDir, $debugMode, $templateFilename, $expected, $exception = '' )
	{
		$tplObj = new Miao_TemplatesEngine_PhpNative( $tmplDir, $debugMode );
		$actual = $tplObj->fetch( $templateFilename );

		$this->assertEquals( $expected, $actual );
	}

	public function providerTestException()
	{
		$data = array();

		$sourceDir = Miao_PHPUnit::getSourceFolder( __METHOD__ );

		$tmplDir = $sourceDir;
		$data[] = array(
			$tmplDir,
			false,
			'test_1.tpl',
			'test debug mode a continue' );

		return $data;
	}
}