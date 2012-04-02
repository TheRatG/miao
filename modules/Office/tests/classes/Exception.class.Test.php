<?php
class Miao_Office_Exception_Test extends PHPUnit_Framework_TestCase
{
	public function testConstruct()
	{
		$this->setExpectedException( 'Miao_Office_Exception' );

		throw new Miao_Office_Exception( 'test' );
	}
}