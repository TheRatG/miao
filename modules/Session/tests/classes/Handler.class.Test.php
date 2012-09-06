<?php
class Miao_Session_Handler_Test extends PHPUnit_Framework_TestCase
{
	public function tearDown()
	{
		if ( session_id() )
		{
			session_destroy();
		}
	}

	public function testHandler()
	{
		$session = Miao_Session::getInstance();
		$sessionNamespace = Miao_Session::getNamespace( 'Test' );
		$sessionNamespace->habr = 'habr';
		$this->assertTrue( !empty( $_SESSION ) );

		$handlerClassNameActual = get_class( $session->getHandler() );
		$config = Miao_Config::Libs( 'Miao_Session' );
		$handlerConfig = $config->get( 'Handler' );
		$handlerClassNameExpected = 'Miao_Session_Handler_' . key( $handlerConfig );

		$this->assertEquals( $handlerClassNameExpected, $handlerClassNameActual );
	}
}