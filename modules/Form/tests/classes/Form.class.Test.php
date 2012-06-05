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
			'<form name="frm_search" action="/" method="POST" enctype="multipart/form-data"></form>' );

		$data[] = array(
			'frm_search',
			'/',
			array( 'class' => 'form' ),
			'<form name="frm_search" action="/" method="POST" enctype="multipart/form-data" class="form"></form>' );

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

		$actual = '<input name="name" value="" type="text" class="input-xlarge" />';
		$data[] = array( 'name', array( 'class' => 'input-xlarge' ), $actual );

		return $data;
	}

	/**
	 * @dataProvider providerTestLoad
	 */
	public function testLoad( Miao_Form $form, $data, $actual, $exceptionName = '' )
	{
		if ( !empty( $exceptionName ) )
		{
			$this->setExpectedException( $exceptionName );
		}

		$form->load( $data );
		$expected = $form->render();
		$this->assertEquals( $expected, $actual );
	}

	public function providerTestLoad()
	{
		$data = array();

		$form = new Miao_Form( 'frm_test' );
		$form->addText( 'name', array( 'class' => 'input-xlarge' ) );
		$postData = array( 'name' => 'Mister Smit' );
		$actual = '<input name="name" value="Mister Smit" type="text" class="input-xlarge" />';
		$data[] = array( $form, $postData, $actual );

		$form = new Miao_Form( 'frm_test' );
		$form->addText( 'name[1]', array( 'class' => 'input-xlarge' ) );
		$postData = array( 'name' => array( 1 => 'Mister Smit' ) );
		$actual = '<input name="name[1]" value="Mister Smit" type="text" class="input-xlarge" />';
		$data[] = array( $form, $postData, $actual );

		$form = new Miao_Form( 'frm_test' );
		$form->addText( 'name[1][2]', array( 'class' => 'input-xlarge' ) );
		$postData = array( 'name' => array( 1 => array( 2 => 'Mister Smit' ) ) );
		$actual = '<input name="name[1][2]" value="Mister Smit" type="text" class="input-xlarge" />';
		$data[] = array( $form, $postData, $actual );

		return $data;
	}

	/**
	 * @dataProvider providerTestRender
	 */
	public function testRender( $name, $form, $exceptionName = '' )
	{
		$sourceFolder = Miao_PHPUnit::getSourceFolder( __METHOD__ );
		$actualFilename = $sourceFolder . '/form_' . $name . '_actual.html';
		$actual = file_get_contents( $actualFilename );
		$tmplFilename = 'form_' . $name . '.tpl';

		$tmpl = new Miao_TemplatesEngine_PhpNative( $sourceFolder, true );
		$tmpl->setValueOf( 'form', $form );
		$expected = $tmpl->fetch( $tmplFilename );
		$this->assertEquals( $this->_clearSpace( $expected ), $this->_clearSpace( $actual ) );
	}

	public function providerTestRender()
	{
		$data = array();

		$form = new Miao_Form( 'frm_form' );
		$form->addText( 'name' )->setLabel( 'Name:' );
		$data[] = array( 'simple1', $form, '' );

		$form = new Miao_Form( 'frm_form' );
		$form->addText( 'name' )->setLabel( 'Name:' )->addValidator( new Miao_Form_Validate_Length( 3 ) );
		$form->isValid( array( 'name' => '123456' ) );
		$data[] = array( 'simple2', $form, '' );

		$form = new Miao_Form( 'frm_form' );
		$form->addText( 'name' )->setLabel( 'Name:' )->addValidator( new Miao_Form_Validate_Length( 250 ) )->setRequired();
		$form->addText( 'email' )->setLabel( 'Email:' )->addValidator( 'email' )->setRequired( 'Please fill your email.' );
		$form->addTextArea( 'descr' )->setLabel( 'Description:' )->addValidator( new Miao_Form_Validate_Length( 250 ) );
		$form->addSubmit( 'send' )->setLabel('Send');
		$form->addReset( 'clear' )->setLabel('Clear');
		$form->isValid( array( 'email' => '123456' ) );
		$data[] = array( 'contact', $form, '' );

		return $data;
	}

	protected function _clearSpace( $string )
	{
		$search = array( "\n", "\t", " " );
		$replace = array( '', '', '' );
		$result = str_replace( $search, $replace, $string );
		return $result;
	}

	/**
	 * @dataProvider providerTestGetHtmlName
	 */
	public function testGetHtmlName( $data, $actual, $exceptionName = '' )
	{
		if ( !empty( $exceptionName ) )
		{
			$this->setExpectedException( $exceptionName );
		}
		$expected = Miao_Form::getHtmlName( $data );
		$this->assertEquals( $expected, $actual );
	}

	public function providerTestGetHtmlName()
	{
		$data = array();

		$data[] = array( array( 'name' => 'fio' ), array( 'name' => 'fio' ) );
		$data[] = array(
			array( 'name' => array( 'fio' ) ),
			array( 'name[0]' => 'fio' ) );
		$data[] = array(
			array( 'fio' => array( 'first' => 'f', 'second' => 's' ) ),
			array( 'fio[first]' => 'f', 'fio[second]' => 's' ) );

		return $data;
	}
}