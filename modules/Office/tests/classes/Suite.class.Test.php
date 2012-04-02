<?php
class Miao_Office_Test_Suite
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite();
		$suite->setName( __CLASS__ );

		$suite->addTestSuite( 'Miao_Office_Exception_Test' );
		$suite->addTestSuite( 'Miao_Office_Request_Test' );

		$suite->addTestSuite( 'Miao_Office_TemplatesEngine_PhpNative_Test' );
		$suite->addTestSuite( 'Miao_Office_ViewBlock_Test' );

		$suite->addTestSuite( 'Miao_Office_Factory_Test' );

		$suite->addTestSuite( 'Miao_Office_Header_Test' );

		return $suite;
	}
}
