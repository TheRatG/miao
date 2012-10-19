<?php
class Miao_Router_Rule_Validator_In_Test extends PHPUnit_Framework_TestCase
{

	/**
	 *
	 * @dataProvider dataProviderTestTest
	 */
	public function testTest( $config, $value, $actual, $exceptionName = '' )
	{
		if ( $exceptionName )
		{
			$this->setExpectedException( $exceptionName );
		}

		$obj = new Miao_Router_Rule_Validator_In( $config );
		$expected = $obj->test( $value );

		$this->assertEquals( $expected, $actual );
	}

	public function dataProviderTestTest()
	{
		$data = array();

		$config = array( 'id' => 'section', 'variants' => 'business,work,home' );
		$data[] = array( $config, 'game', false );

		$config = array(
			'id' => 'section',
			'variants' => 'business:work:home',
			'delimiter' => ':' );
		$data[] = array( $config, 'work', true );

		$config = array(
			'id' => 'section',
			'variants' => 'business,work,home,,,' );
		$data[] = array(
			$config,
			'work',
			false,
			'Miao_Router_Rule_Validator_Exception' );

		return $data;
	}
}