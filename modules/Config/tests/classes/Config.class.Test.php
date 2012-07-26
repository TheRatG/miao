<?php
class Miao_Config_Test extends PHPUnit_Framework_TestCase
{
	public function testMain()
	{
		$config = Miao_Config::Main();
		$this->assertInstanceOf( 'Miao_Config_Base', $config );
		$this->assertTrue( is_array( $config->get( 'paths' ) ) );
	}

	public function testLibs()
	{
		$config = Miao_Config::Libs( 'Miao' );
		$this->assertInstanceOf( 'Miao_Config_Base', $config );
		$this->assertTrue( is_array( $config->get( 'deploy' ) ) );
	}
}