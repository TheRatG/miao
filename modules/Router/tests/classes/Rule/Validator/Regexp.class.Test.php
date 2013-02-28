<?php
class Miao_Router_Rule_Validator_Regexp_Test extends PHPUnit_Framework_TestCase
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

		$obj = new Miao_Router_Rule_Validator_Regexp( $config );
		$expected = $obj->test( $value );

		$this->assertEquals( $expected, $actual );
	}

	public function dataProviderTestTest()
	{
		$data = array();

		$config = array( 'id' => 'section' );
		$data[] = array(
			$config,
			'game',
			false,
			'Miao_Router_Rule_Validator_Exception' );

		$config = array( 'id' => 'section', 'pattern' => '(business|work|home)' );
		$data[] = array( $config, 'game', false );
		$data[] = array( $config, 'work', true );

		$config = array( 'id' => 'section', 'pattern' => '([0-9a-z]+)' );
		$data[] = array( $config, 'work99', true );

		$data[] = array( $config, '', false );

		$config = array( 'id' => 'section', 'pattern' => '^[a-zA-Z_]+$' );
		$data[] = array( $config, 'p2', false );

		return $data;
	}
}