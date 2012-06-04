<?php
class Miao_Form_Control_Submit_Test extends PHPUnit_Framework_TestCase
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

		$control = new Miao_Form_Control_Submit( $name );
		$control->setValue( $value );
		$control->setAttributes( $attributes );

		$expected = $control->render();

		$this->assertEquals( $expected, $actual );
	}

	public function providerTestMain()
	{
		$data = array();

		$actual = '<input id="push" name="push" value="push" type="submit" class="btn" />';
		$data[] = array(
			'push',
			$actual,
			'push',
			array( 'class' => 'btn' ) );

		return $data;
	}
}