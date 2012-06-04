<?php
class Miao_Form_Control_Image_Test extends PHPUnit_Framework_TestCase
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

		$control = new Miao_Form_Control_Image( $name, $attributes );
		$control->setValue( $value );

		$expected = $control->render();

		$this->assertEquals( $expected, $actual );
	}

	public function providerTestMain()
	{
		$data = array();

		$actual = '<input id="push" name="push" value="push" type="image" class="btn" src="but.jpg" />';
		$data[] = array(
			'push',
			$actual,
			'push',
			array( 'class' => 'btn', 'src' => 'but.jpg' ) );

		return $data;
	}
}