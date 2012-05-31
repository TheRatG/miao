<?php
class Miao_Form_Validate_Email_Test extends PHPUnit_Framework_TestCase
{

	/**
	 * @dataProvider providerTestIsValid
	 */
	public function testIsValid( $value, $actual, $actualMessages = array() )
	{
		$validator = new Miao_Form_Validate();
		$validator->addValidator( 'email' );

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

		$data[] = array( 'test@test.com', true );
		$data[] = array( '', false );

		$data[] = array(
			'test.com',
			false,
			array(
				Miao_Form_Validate_Email::INVALID_FORMAT => "'test.com' is no valid email address in the basic format local-part@hostname" ) );

		$data[] = array(
			array( 'test@test.com' ),
			false,
			array(
				Miao_Form_Validate_Email::INVALID => 'Invalid type given. String expected' ) );

		return $data;
	}
}