<?php
class Miao_Form_Control_RadioList_Test extends PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider providerTestMain
	 */
	public function testMain( $name, $items, $actual, $value = '', $attributes = array(), $exceptionName = '' )
	{
		if ( !empty( $exceptionName ) )
		{
			$this->setExpectedException( $exceptionName );
		}

		$control = new Miao_Form_Control_RadioList( $name, $attributes, $items );
		$control->setValue( $value );
		$control->setAttributes( $attributes );

		$expected = $control->render();
		$this->assertEquals( $expected, $actual );
	}

	public function providerTestMain()
	{
		$data = array();

		$actual = array();
		$actual[] = '<input name="sex" value="m" type="radio" />';
		$actual[] = '<input name="sex" value="f" type="radio" />';
		$data[] = array(
			'sex',
			array( 'm' => 'male', 'f' => 'female' ),
			implode( chr( 10 ), $actual ),
			'' );

		$actual = array();
		$actual[] = '<input name="sex" value="m" type="radio" checked="checked" />';
		$actual[] = '<input name="sex" value="f" type="radio" />';
		$data[] = array(
			'sex',
			array( 'm' => 'male', 'f' => 'female' ),
			implode( chr( 10 ), $actual ),
			'm' );

		return $data;
	}
}