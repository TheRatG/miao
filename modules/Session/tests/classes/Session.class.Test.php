<?php
class Miao_Session_Test extends PHPUnit_Framework_TestCase
{
	public function tearDown()
	{
		if ( session_id() )
		{
			session_destroy();
		}
	}

	/**
	 * @dataProvider providerGetInstance
	 *
	 * @param $namespace unknown_type
	 * @param $expected unknown_type
	 * @param $exceptionName unknown_type
	 */
	public function testGetInstance( $namespace, $expected, $exceptionName = '' )
	{
		if ( !empty( $exceptionName ) )
		{
			$this->setExpectedException( $exceptionName );
		}
		$session = Miao_Session::getNamespace( $namespace );

		$this->assertInstanceOf( $expected, $session );
	}

	public function providerGetInstance()
	{
		$data = array();

		$exceptionName = 'Miao_Session_Exception';

		$data[] = array( '', 'Miao_Session_Namespace' );
		$data[] = array( 'user', 'Miao_Session_Namespace' );
		$data[] = array( 'user+', 'Miao_Session_Namespace' );
		$data[] = array( 'user?', 'Miao_Session_Namespace' );

		return $data;
	}

	/**
	 * @dataProvider providerTestConstruct
	 *
	 * @param $options unknown_type
	 * @param $handler unknown_type
	 * @param $expectedOptions unknown_type
	 * @param $expectedHandler unknown_type
	 */
	public function testConstruct( $options, $handler, $expectedOptions, $expectedHandler, $exceptionName = '' )
	{
		if ( !empty( $exceptionName ) )
		{
			$this->setExpectedException( $exceptionName );
		}

		$session = Miao_Session::getInstance();
		$session->setOptions( $options );
		$session->setHandler( $handler );

		$actualOptions = $session->getOptions();

		unset( $expectedOptions[ 'session.save_path' ] );
		unset( $expectedOptions[ 'session.save_handler' ] );
		unset( $actualOptions[ 'session.save_path' ] );
		unset( $actualOptions[ 'session.save_handler' ] );

		$this->assertEquals( $expectedOptions, $actualOptions );

		$actualHandler = $session->getHandler();
		$this->assertEquals( $expectedHandler, $actualHandler );
	}

	public function providerTestConstruct()
	{
		$data = array();

		$exceptionName = 'Miao_Session_Exception';
		$localOptions = array(
			'session.save_path' => '',
			'session.name' => 'PHPSESSID',
			'session.save_handler' => 'files',
			'session.gc_probability' => '1',
			'session.gc_divisor' => '1000',
			'session.gc_maxlifetime' => '1440',
			'session.serialize_handler' => 'php',
			'session.cookie_lifetime' => '0',
			'session.cookie_path' => '/',
			'session.cookie_domain' => '',
			'session.cookie_secure' => '',
			'session.cookie_httponly' => '',
			'session.use_cookies' => '1',
			'session.use_only_cookies' => '1',
			'session.referer_check' => '',
			'session.entropy_file' => '',
			'session.entropy_length' => '0',
			'session.cache_limiter' => 'nocache',
			'session.cache_expire' => '180',
			'session.use_trans_sid' => '0',
			'session.bug_compat_42' => '1',
			'session.bug_compat_warn' => '1',
			'session.hash_function' => '0',
			'session.hash_bits_per_character' => '5' );

		$options = array();
		$handler = null;
		$expectedOptions = $localOptions;
		$expectedHandler = new Miao_Session_Handler_None();
		$data[] = array( $options, $handler, $expectedOptions, $expectedHandler );

		$options = array();
		$handler = new Miao_Session_Handler_Memcache( 'localhost' );
		$expectedOptions = $localOptions;
		$expectedOptions[ 'session.save_handler' ] = 'memcache';
		$expectedOptions[ 'session.save_path' ] =
		'tcp://localhost:11211?persistent=1';
		$expectedHandler = $handler;
		$data[] = array( $options, $handler, $expectedOptions,
		$expectedHandler );

		// check options
		$options = array( 'cache_expire' => '200' );
		$handler = null;
		$expectedOptions = $localOptions;
		$expectedOptions[ 'session.cache_expire' ] = '200';
		$expectedHandler = new Miao_Session_Handler_None();
		$data[] = array( $options, $handler, $expectedOptions, $expectedHandler );

		$options = array( 'unnamed_option' => '200' );
		$handler = null;
		$expectedOptions = $localOptions;
		$expectedHandler = new Miao_Session_Handler_None();
		$data[] = array(
			$options,
			$handler,
			$expectedOptions,
			$expectedHandler,
			$exceptionName );

		// check handler
		$options = array();
		$handler = new stdClass();
		$expectedOptions = $localOptions;
		$expectedHandler = new Miao_Session_Handler_None();
		$data[] = array(
			$options,
			$handler,
			$expectedOptions,
			$expectedHandler,
			$exceptionName );

		return $data;
	}

	public function testSessionVar()
	{
		$session = Miao_Session::getInstance();
		$session->start();

		$_SESSION[ 'var' ] = 'value';
		$this->assertEquals( 'value', $_SESSION[ 'var' ] );
	}
}