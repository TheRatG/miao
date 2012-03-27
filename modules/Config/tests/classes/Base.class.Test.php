<?php
class Miao_Config_Base_Test extends PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider providerTestGet
	 */
	public function testGet( $path, $actual, $initData, $exceptionName = '' )
	{
		if ( !empty( $exceptionName ) )
		{
			$this->setExpectedException( $exceptionName );
		}

		$config = new Miao_Config_Base( $initData );
		$expected = $config->get( $path );

		$this->assertEquals( $expected, $actual );
	}

	public function providerTestGet()
	{
		$data = array();

		$config = array(
			'db' => array(
				'server' => 'test.server',
				'login' => 'test-user',
				'password' => 'test-password' ) );

		$data[] = array( '/db/login', 'test-user', $config );
		$data[] = array( '/db', $config[ 'db' ], $config, '' );
		$data[] = array( '/', $config, $config, '' );

		$exceptionName = 'Miao_Config_Exception_InvalidPath';
		$data[] = array( '', null, $config, $exceptionName );
		$data[] = array( '/', $config, $config );
		$data[] = array( '//', null, $config, $exceptionName );
		$data[] = array( '///', null, $config, $exceptionName );
		$data[] = array( '/db/password/', null, $config, $exceptionName );
		$data[] = array( '/db/password//', null, $config, $exceptionName );
		$data[] = array( '/db//password', null, $config, $exceptionName );

		$exceptionName = 'Miao_Config_Exception_PathNotFound';
		$data[] = array( 'password', null, $config, $exceptionName );
		$data[] = array( '/asd', null, $config, $exceptionName );
		$data[] = array( '/db/type', null, $config, $exceptionName );
		$data[] = array( '/db/asd/password', null, $config, $exceptionName );
		$data[] = array( '/lo1gin', null, $config, $exceptionName );
		$data[] = array( '/db/server/wrong-key', null, $config, $exceptionName );

		return $data;
	}
}