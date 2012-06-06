<?php
class Miao_OfficeOffice_DataHelper_Messages_Test extends PHPUnit_Framework_TestCase
{

	/**
	 * save and get one message
	 */
	public function testMain()
	{
		$dh = Miao_Office_DataHelper_Messages::getInstance();

		$message = '<strong>Heads up!</strong> This alert needs your attention, but it\'s not super important.';
		$dh->add( $message, $dh::INFO );
		$expected = $dh->get();
		$actual = $message;
		$this->assertEquals( $expected, $actual );

		$dh->clear();

		$exceptionName = 'Miao_Office_DataHelper_Exception';
		$this->setExpectedException( $exceptionName );
		$dh->get();
	}

	/**
	 * step access
	 */
	public function testStepAccess()
	{
		$dh = Miao_Office_DataHelper_Messages::getInstance();
		$message = '<strong>Heads up!</strong> This alert needs your attention, but it\'s not super important.';
		$dh->add( $message, $dh::INFO );
		$expected = $dh->get();
		$actual = $message;
		$this->assertEquals( $expected->__toString(), $actual );

		$message = '<strong>Well done!</strong> You successfully read this important alert message. ';
		$dh->add( $message, $dh::SUCCESS );
		$expected = $dh->get();
		$actual = $message;
		$this->assertEquals( $expected->__toString(), $actual );

		$dh->clear();
	}

	public function testKeyAccess()
	{
		$dh = Miao_Office_DataHelper_Messages::getInstance();

		$message = '<strong>Heads up!</strong> This alert needs your attention, but it\'s not super important.';
		$dh->add( $message, $dh::INFO, 'er1' );
		$expected = $dh->get( 'er1' );
		$actual = $message;
		$this->assertEquals( $expected->__toString(), $actual );

		$exceptionName = 'Miao_Office_DataHelper_Exception';
		$this->setExpectedException( $exceptionName );
		$dh->get( 'er1' );

		$dh->clear();
	}
}