<?php
class Miao_Form_Control_Button_Test extends PHPUnit_Framework_TestCase
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

		$control = new Miao_Form_Control_Button( $name );
		$control->setValue( $value );
		$control->setAttributes( $attributes );

		$expected = $control->render();

		$this->assertEquals( $expected, $actual );
	}

	public function providerTestMain()
	{
		$data = array();

		$actual = '<input id="push" name="push" value="push" type="button" class="btn" />';
		$data[] = array(
			'push',
			$actual,
			'push',
			array( 'class' => 'btn' ) );

		return $data;
	}
}