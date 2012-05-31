<?php
class Miao_Form_Validate_Length_Test extends PHPUnit_Framework_TestCase
{

	/**
	 * @dataProvider providerTestIsValid
	 */
	public function testIsValid( $value, $min, $max, $actual, $actualMessages = array(), $exceptionName = '' )
	{
		if ( !empty( $exceptionName ) )
		{
			$this->setExpectedException( $exceptionName );
		}

		$validator = new Miao_Form_Validate();
		$validator->addValidator( new Miao_Form_Validate_Length( $max, $min ) );

		$expected = $validator->isValid( $value );
		$this->assertEquals( $expected, $actual );

		if ( !empty( $actualMessages ) )
		{
			$expectedMessages = $validator->getMessages();
			$this->assertEquals( $expectedMessages, $actualMessages );
		}
	}

	public function providerTestIsValid()
	{
		$data = array();

		$data[] = array( 'val', 0, 3, true );
		$data[] = array( 'val', 0, 2, false );
		$data[] = array( 'val', 4, 5, false );

		$data[] = array( 'val', 3, 2, false, array(), 'Miao_Form_Validate_Exception' );

		return $data;
	}
}