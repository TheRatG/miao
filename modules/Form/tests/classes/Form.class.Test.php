<?php
class Miao_Form_Test extends PHPUnit_Framework_TestCase
{

	/**
	 * @dataProvider providerTestConstruct
	 */
	public function testConstruct( $id, $action, $attributes, $actual, $exceptionName = '' )
	{
		if ( !empty( $exceptionName ) )
		{
			$this->setExpectedException( $exceptionName );
		}

		$form = new Miao_Form( $id, $action, $attributes );
		$expected = array();

		$expected[] = $form->begin();
		$expected[] = $form->end();

		$expected = implode( '', $expected );

		$this->assertEquals( $expected, $actual );
	}

	public function providerTestConstruct()
	{
		$data = array();

		$exceptionName = 'Miao_Form_Exception';

		$data[] = array(
			'frm_search',
			'/',
			array(),
			'<form id="frm_search" name="frm_search" action="/" method="POST" enctype="multipart/form-data"></form>' );

		$data[] = array(
			'frm_search',
			'/',
			array( 'class' => 'form' ),
			'<form id="frm_search" name="frm_search" action="/" method="POST" enctype="multipart/form-data" class="form"></form>' );

		$data[] = array(
			'frm_search',
			'/',
			array( 'method' => 'GET' ),
			'',
			$exceptionName );

		return $data;
	}

	/**
	 * @dataProvider providerTestAddText
	 */
	public function testAddText( $name, $attributes, $actual, $exceptionName = '' )
	{
		if ( !empty( $exceptionName ) )
		{
			$this->setExpectedException( $exceptionName );
		}

		$form = new Miao_Form( 'frm_contact' );
		$form->addText( $name, $attributes );

		$expected = $form->$name->__toString();
		$this->assertEquals( $expected, $actual );
	}

	public function providerTestAddText()
	{
		$data = array();

		$actual = '<input id="name" name="name" value="" type="text" class="input-xlarge" />';
		$data[] = array( 'name', array( 'class' => 'input-xlarge' ), $actual );

		return $data;
	}

	/**
	 * @dataProvider providerTestLoadValue
	 */
	public function testLoadValue( Miao_Form $form, $data, $actual, $exceptionName = '' )
	{
		if ( !empty( $exceptionName ) )
		{
			$this->setExpectedException( $exceptionName );
		}

		$form->loadValue( $data );
		$expected = $form->render();
		$this->assertEquals( $expected, $actual );
	}

	public function providerTestLoadValue()
	{
		$data = array();

		$form = new Miao_Form( 'frm_test' );
		$form->addText( 'name', array( 'class' => 'input-xlarge' ) );
		$postData = array( 'name' => 'Mister Smit' );
		$actual = '<input id="name" name="name" value="Mister Smit" type="text" class="input-xlarge" />';
		$data[] = array( $form, $postData, $actual );

		return $data;
	}

	/**
	 * @dataProvider providerTestRender
	 */
	public function testRender( $num, $form, $exceptionName = '' )
	{
		$sourceFolder = Miao_PHPUnit::getSourceFolder( __METHOD__ );
		$actualFilename = $sourceFolder . '/form_' . $num . '_actual.html';
		$actual = file_get_contents( $actualFilename );
		$tmplFilename = 'form_' . $num . '.tpl';

		$tmpl = new Miao_TemplatesEngine_PhpNative( $sourceFolder, true );
		$tmpl->setValueOf( 'form', $form );
		$expected = $tmpl->fetch( $tmplFilename );
		$this->assertEquals($expected, $actual );
	}

	public function providerTestRender()
	{
		$data = array();

		$form = new Miao_Form( 'frm_form1' );
		$form->addText( 'name' )->setLabel( 'Name:' );
		$data[] = array( 1, $form, '' );
		return $data;
	}
}