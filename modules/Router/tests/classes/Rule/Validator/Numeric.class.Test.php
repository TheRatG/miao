<?php
class Miao_Router_Rule_Validator_Numeric_Test extends PHPUnit_Framework_TestCase
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

		$obj = new Miao_Router_Rule_Validator_Numeric( $config );
		$expected = $obj->test( $value );

		$this->assertEquals( $expected, $actual );
	}

	public function dataProviderTestTest()
	{
		$data = array();

		$config = array( 'id' => 'objectid' );

		$data[] = array( $config, 0, true );
		$data[] = array( $config, '123', true );
		$data[] = array( $config, '0123', true );
		$data[] = array( $config, '', false );
		$data[] = array( $config, 'asd', false );

		$config = array( 'id' => 'objectid', 'min' => 2 );
		$data[] = array( $config, '1', false );
		$data[] = array( $config, '12345', true );

		$config = array( 'id' => 'objectid', 'min' => 2, 'max' => 5 );
		$data[] = array( $config, '1', false );
		$data[] = array( $config, '12345', true );
		$data[] = array( $config, '123456', false );

		return $data;
	}
}
