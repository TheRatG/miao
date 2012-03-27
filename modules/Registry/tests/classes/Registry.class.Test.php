<?php
class Miao_Registry_Test extends PHPUnit_Framework_TestCase
{
	static $_registryList;

	public static function setUpBeforeClass()
	{
		self::$_registryList = Miao_Registry::toArray();
	}

	public static function tearDownAfterClass()
	{
		foreach ( self::$_registryList as $key => $value )
		{
			Miao_Registry::set( $key, $value );
		}
	}

	public function setUp()
	{
		Miao_Registry::unsetInstance();
	}

	public function tearDown()
	{
		Miao_Registry::unsetInstance();
	}

	public function testRegistryUninitIsRegistered()
	{
		// checking entry is set returns false,
		// but does not initialize instance
		$this->assertFalse( Miao_Registry::isRegistered( 'objectname' ) );
	}

	public function testRegistryUninitGetInstance()
	{
		// getting instance initializes instance
		$registry = Miao_Registry::getInstance();
		$this->assertInstanceOf( 'Miao_Registry', $registry );
	}

	public function testRegistryUninitSet()
	{
		// setting value initializes instance
		Miao_Registry::set( 'foo', 'bar' );
		$registry = Miao_Registry::getInstance();
		$this->assertInstanceOf( 'Miao_Registry', $registry );
	}

	public function testRegistryUninitGet()
	{
		// getting value initializes instance
		// but throws different exception because
		// entry is not registered
		try
		{
			Miao_Registry::get( 'foo' );
			$this->fail( 'Expected exception when trying to fetch a non-existent key.' );
		}
		catch ( Miao_Registry_Exception $e )
		{
			$this->assertContains( 'No entry is registered for key', $e->getMessage() );
		}
		$registry = Miao_Registry::getInstance();
		$this->assertInstanceOf( 'Miao_Registry', $registry );
	}

	public function testRegistrySingletonSameness()
	{
		$registry1 = Miao_Registry::getInstance();
		$registry2 = Miao_Registry::getInstance();
		$this->assertInstanceOf( 'Miao_Registry', $registry1 );
		$this->assertInstanceOf( 'Miao_Registry', $registry2 );
		$this->assertEquals( $registry1, $registry2 );
		$this->assertSame( $registry1, $registry2 );
	}

	public function testRegistryEqualContents()
	{
		Miao_Registry::set( 'foo', 'bar' );
		$registry1 = Miao_Registry::getInstance();
		$registry2 = new Miao_Registry( array( 'foo' => 'bar' ) );
		$this->assertEquals( $registry1, $registry2 );
		$this->assertNotSame( $registry1, $registry2 );
	}

	public function testRegistryUnequalContents()
	{
		$registry1 = Miao_Registry::getInstance();
		$registry2 = new Miao_Registry( array( 'foo' => 'bar' ) );
		$this->assertNotEquals( $registry1, $registry2 );
		$this->assertNotSame( $registry1, $registry2 );
	}

	public function testRegistrySetAndIsRegistered()
	{
		$this->assertFalse( Miao_Registry::isRegistered( 'foo' ) );
		Miao_Registry::set( 'foo', 'bar' );
		$this->assertTrue( Miao_Registry::isRegistered( 'foo' ) );
	}

	public function testRegistryGet()
	{
		Miao_Registry::set( 'foo', 'bar' );
		$bar = Miao_Registry::get( 'foo' );
		$this->assertEquals( 'bar', $bar );
	}

	public function testRegistryArrayObject()
	{
		$registry = Miao_Registry::getInstance();
		$registry[ 'emptyArray' ] = array();
		$registry[ 'null' ] = null;

		$this->assertTrue( isset( $registry[ 'emptyArray' ] ) );
		$this->assertTrue( isset( $registry[ 'null' ] ) );
		$this->assertFalse( isset( $registry[ 'noIndex' ] ) );

		$this->assertTrue( Miao_Registry::isRegistered( 'emptyArray' ) );
		$this->assertTrue( Miao_Registry::isRegistered( 'null' ) );
		$this->assertFalse( Miao_Registry::isRegistered( 'noIndex' ) );
	}

	public function testRegistryArrayAsProps()
	{
		$registry = new Miao_Registry( array(), ArrayObject::ARRAY_AS_PROPS );
		$registry->foo = 'bar';
		$this->assertTrue( isset( $registry->foo ) );
		$this->assertEquals( 'bar', $registry->foo );
	}

	/**
	 * NB: We cannot make a unit test for the class not Miao_Registry or
	 * a subclass, because that is enforced by type-hinting in the
	 * Miao_Registry::setInstance() method. Type-hinting violations throw
	 * an error, not an exception, so it cannot be caught in a unit test.
	 */

	public function testRegistryExceptionNoEntry()
	{
		try
		{
			$foo = Miao_Registry::get( 'foo' );
			$this->fail( 'Expected exception when trying to fetch a non-existent key.' );
		}
		catch ( Miao_Registry_Exception $e )
		{
			$this->assertContains( 'No entry is registered for key', $e->getMessage() );
		}
	}

	public function testDefaultRegistryArrayAsPropsZF4654()
	{
		$registry = Miao_Registry::getInstance();
		$registry->bar = "baz";
		$this->assertEquals( 'baz', Miao_Registry::get( 'bar' ) );
	}
}