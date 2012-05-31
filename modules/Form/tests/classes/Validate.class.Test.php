<?php
class Miao_Form_Validate_Test extends PHPUnit_Framework_TestCase
{

	/**
	 * @dataProvider providerTestAddValidator
	 */
	public function testAddValidator( $mixed, $actual, $exceptionName = '' )
	{
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

		return $data;
	}
}