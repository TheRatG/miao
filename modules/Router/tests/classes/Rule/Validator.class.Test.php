<?php
class Miao_Router_Rule_Validator_Test extends PHPUnit_Framework_TestCase
{

	/**
	 * @dataProvider dataProviderTestFactory
	 */
	public function testFactory( $config, $expected, $exceptionName = '' )
	{
		if ( $exceptionName )
		{
			$this->setExpectedException( $exceptionName );
		}

		$actual = Miao_Router_Rule_Validator::factory( $config );
		$this->assertInstanceOf( $expected, $actual );
	}

	public function dataProviderTestFactory()
	{
		$data = array();

		$config = array( 'id' => null, 'type' => 'Compare' );
		$data[] = array(
			$config,
			'Miao_Router_Rule_Validator_Compare',
			'Miao_Router_Rule_Validator_Exception' );

		$config = array( 'id' => null, 'type' => 'Compare', 'str' => 'test' );
		$data[] = array( $config, 'Miao_Router_Rule_Validator_Compare', '' );

		$config = array( 'id' => 'section', 'type' => 'NotEmpty' );
		$data[] = array( $config, 'Miao_Router_Rule_Validator_NotEmpty', '' );

		$config = array( 'id' => 'code', 'type' => 'Numeric' );
		$data[] = array( $config, 'Miao_Router_Rule_Validator_Numeric', '' );

		$config = array( 'id' => 'code', 'type' => 'In', 'variants' => '1,2,3,4,5', 'delimiter' => ',' );
		$data[] = array( $config, 'Miao_Router_Rule_Validator_In', '' );

		$config = array( 'id' => 'code', 'type' => 'Regexp', 'pattern' => "([a-z]+)" );
		$data[] = array( $config, 'Miao_Router_Rule_Validator_Regexp', '' );

		return $data;
	}
}