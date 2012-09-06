<?php
class Miao_Session_Namespace_Test extends PHPUnit_Framework_TestCase
{
	public function tearDown()
	{
		if ( session_id() )
		{
			session_destroy();
		}
	}

	/**
	 * @dataProvider providerTestGet
	 *
	 * @param $namespace unknown_type
	 * @param $vars unknown_type
	 * @param $exceptionName unknown_type
	 */
	public function testGet( $namespace, $vars, $exceptionName = '' )
	{
		$sessionNamespace = Miao_Session::getNamespace( $namespace );

		foreach ( $vars as $key => $val )
		{
			$sessionNamespace->$key = $val;
		}

		$nick = Miao_Session_Namespace::getNick( $namespace );
		$data = unserialize( $_SESSION[ $nick ] );

		foreach ( $data as $key => $value )
		{
			$actual = $value;
			$expected = $vars[ $key ];

			$this->assertEquals( $expected, $actual );
		}
		$sessionNamespace->clear();
	}

	public function providerTestGet()
	{
		$data = array();

		$data[] = array( 'Test', array( 'var_1' => 1 ) );
		$data[] = array( 'Test', array( 'var_1' => 1, 'var_2' => 2 ) );

		$data[] = array( 'Test1', array( 'var_1' => '1' ) );

		$obj = new stdClass();
		$obj->a = 'a';
		$data[] = array( 'Test2', array( 'var_1' => $obj ) );

		return $data;
	}

	public function testGetTwo()
	{
		$namespace = 'LLL';
		$sessionNamespace = Miao_Session::getNamespace( $namespace );
		$nick = Miao_Session_Namespace::getNick( $namespace );

		$sessionNamespace->a = array( 'a', 'b', 'c' );

		// modify inner array
		$sessionNamespace->a = array( 1 => 'c' ) + $sessionNamespace->a;

		$expected = unserialize( $_SESSION[ $nick ] );
		$expected = $expected[ 'a' ];
		$actual = $sessionNamespace->a;
		$this->assertEquals( $expected, $actual );

		/*
		 * $obj = new stdClass(); $obj->var1 = 1; $obj->var2 = 2; $expected =
		 * clone $obj; $sessionNamespace->b = $obj; $sessionNamespace->b->var2 =
		 * 'b'; $expected->var2 = 'b'; $actual = $sessionNamespace->b;
		 * $this->assertEquals( $expected, $actual );
		 */

		$sessionNamespace->clear();
	}

	public function testIterator()
	{
		$namespace = 'LLL';
		$sessionNamespace = Miao_Session::getNamespace( $namespace );
		$nick = Miao_Session_Namespace::getNick( $namespace );

		$sessionNamespace->a = 1;
		$sessionNamespace->b = 2;
		$sessionNamespace->c = 3;

		$expected = array( 'a' => 1, 'b' => 2, 'c' => 3 );

		foreach ( $sessionNamespace as $key => $value )
		{
			$this->assertTrue( isset( $expected[ $key ] ) );
			$this->assertEquals( $expected[ $key ], $value );
		}

		$this->assertFalse( isset( $sessionNamespace[ 'd' ] ) );
		$this->assertTrue( isset( $sessionNamespace[ 'a' ] ) );
		unset( $sessionNamespace[ 'a' ] );
		$this->assertFalse( isset( $sessionNamespace[ 'a' ] ) );

		$sessionNamespace->clear();
	}
}