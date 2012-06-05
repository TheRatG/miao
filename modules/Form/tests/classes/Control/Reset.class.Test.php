<?php
class Miao_Form_Control_Reset_Test extends PHPUnit_Framework_TestCase
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

		$control = new Miao_Form_Control_Reset( $name, $attributes );
		$control->setValue( $value );

		$expected = $control->render();

		$this->assertEquals( $expected, $actual );
	}

	public function providerTestMain()
	{
		$data = array();

		$actual = '<input name="reset" value="clear" type="reset" class="btn" />';
		$data[] = array(
			'reset',
			$actual,
			'clear',
			array( 'class' => 'btn' ) );

		return $data;
	}
}