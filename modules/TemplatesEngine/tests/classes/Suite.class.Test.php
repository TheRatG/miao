<?php
class Miao_TemplatesEngine_Test_Suite
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite();

		 // Одиночные тесты
		$suite->addTestSuite( 'Miao_TemplatesEngine_PhpNative_Test' );

		return $suite;
	}
}