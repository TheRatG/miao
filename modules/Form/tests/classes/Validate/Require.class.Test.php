<?php
class Miao_Form_Validate_Require_Test extends PHPUnit_Framework_TestCase
{

	/**
	 * @dataProvider providerTestIsValid
	 */
	public function testIsValid( $value, $actual, $actualMessages = array() )
	{
		$validator = new Miao_Form_Validate();
		$validator->addValidator( 'require' );

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

		$data[] = array(
			'',
			false,
			array(
				Miao_Form_Validate_Require::IS_EMPTY => "Value is required and can't be empty" ) );
		$data[] = array( null, false );

		$data[] = array( '0', true );
		$data[] = array( 0, true );
		$data[] = array( 'text', true );

		$data[] = array( array( 'text' ), false );

		return $data;
	}
}