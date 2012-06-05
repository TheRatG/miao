<?php
class Miao_Form_Control_Text_Test extends PHPUnit_Framework_TestCase
{

	/**
	 * @dataProvider providerTestMain
	 */
	public function testMain( $name, $actual, $value = '', $attributes = array(), $exceptionName = '' )
	{
		if ( !empty( $exceptionName ) )
		{
			$this->setExpectedException( $exceptionName );
		}

		$control = new Miao_Form_Control_Text( $name, $attributes );
		$control->setValue( $value );

		$expected = $control->render();

		$this->assertEquals( $expected, $actual );
	}

	public function providerTestMain()
	{
		$data = array();

		$actual = '<input name="name" value="" type="text" class="input-xlarge" />';
		$data[] = array(
			'name',
			$actual,
			'',
			array( 'class' => 'input-xlarge' ) );

		return $data;
	}
}