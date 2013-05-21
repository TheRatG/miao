<?php
class Miao_Form_Validate_Test extends PHPUnit_Framework_TestCase
{

	/**
	 * @dataProvider providerTestAddValidator
	 */
	public function testAddValidator( $mixed, $actual, $exceptionName = '' )
	{
		if ( !empty( $exceptionName ) )
		{
			$this->setExpectedException( $exceptionName );
		}

		$validator = new Miao_Form_Validate();
		$validator->addValidator( $mixed );

		$expected = $validator->getValidators();
		$this->assertEquals( $expected, $actual );
	}

	public function providerTestAddValidator()
	{
		$data = array();

		$data[] = array(
			'require',
			array(
				array(
					'instance' => new Miao_Form_Validate_Require(),
					'breakChainOnFailure' => false ) ) );

		$data[] = array(
			'NoClassValidate',
			'',
			'Miao_Autoload_Exception_FileNotFound' );

		$data[] = array(
			array( 'NoClassValidate', array() ),
			'',
			'Miao_Autoload_Exception_FileNotFound' );

		return $data;
	}

	/**
	 * @dataProvider providerTestSeveralValidator
	 * @param unknown_type $value
	 */
	public function testSeveralValidator( $value )
	{
		$control = new Miao_Form_Control_Text( 'title' );
		$control->setRequired( 'require' );

		$control->addValidator( new Miao_Form_Validate_Length( 5 ), false );
		$control->addValidator( new Miao_Form_Validate_Length( 6 ), false );

		$control->setValue( $value );
		$control->validate();

		$expected = $control->error()->__toString();
	}

	public function providerTestSeveralValidator()
	{
		$data = array();

		$data[] = array( '123' );
		$data[] = array( '12345' );
		$data[] = array( '123456' );
		$data[] = array( '1234567' );

		return $data;
	}
}