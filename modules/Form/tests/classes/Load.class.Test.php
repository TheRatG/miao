<?php
class Miao_Form_Load_Test extends PHPUnit_Framework_TestCase
{
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
}