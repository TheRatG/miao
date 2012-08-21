<?php
class Miao_Form_Control_Select_Test extends PHPUnit_Framework_TestCase
{

	public function testValue()
	{
		$form = new Miao_Form( 'test-select' );
		$form->addSelect( 'users', array(), array( 'root', 'guest', 'editor' ) );
		$form->load( array( 'users' => '2' ) );

		$actual = array( 'users' => '2' );
		$expected = $form->getValues();

		$this->assertEquals( $expected, $actual );
	}
}