<?php
class Miao_Session_Test extends PHPUnit_Framework_TestCase
{
	public function testGetSessionId()
	{
		$this->assertEquals( Miao_Session::getInstance()->getSessionId(), session_id() );
		Miao_Session::getInstance()->destroySession();
		$this->assertEquals( Miao_Session::getInstance()->getSessionId(), '' );
	}

	public function data4ScalarOperations()
	{
		return array(
					array( 'new_name', 'name value' ),
					array( '', 'error value' ),
					array( '', null, 'default_value' ),
					array( 'null_name', null, 'default_value' ),
					array( 'null_name', false, false ),
					array( 'unexistent_name', '' )
					);
	}

	public function data4ObjectOperation()
	{
		$obj1 = new TestA( '1' );
		$obj2 = new TestA( 2 );

		return array(
					array( 'obj1', $obj1 ),
					array( 'obj2', $obj2 ),
					array( null, $obj2 ),
					);
	}

	/**
	 * @dataProvider data4ScalarOperations
	 *
	 */
	public function testSaveScalar( $name, $value )
	{
		if ( $name == 'unexistent_name' )
		{
			return ;
		}
		Miao_Session::getInstance()->saveScalar( $name, $value );
		$this->assertEquals( $_SESSION[ $name ], $value );
	}

	/**
	 * @dataProvider data4ScalarOperations
	 *
	 */
	public function testLoadScalar( $name, $res, $default = '' )
	{
		if ( $name != 'unexistent_name' )
		{
			Miao_Session::getInstance()->saveScalar( $name, $res );
		}
		$this->assertEquals( ( !is_null( $res ) || $name == 'unexistent_name' ) ? $res : $default, Miao_Session::getInstance()->loadScalar( $name, $default ) );
	}

	/**
	 * @dataProvider data4ObjectOperation
	 *
	 */
	public function testSaveObject( $name, $value )
	{
		if ( $name == 'unexistent_name' )
		{
			return ;
		}
		Miao_Session::getInstance()->saveObject( $name, $value );
		$this->assertEquals( unserialize( $_SESSION[ $name ] ), $value );
		if ( is_object( unserialize( $_SESSION[ $name ] ) ) )
		{
			$this->assertEquals( unserialize( $_SESSION[ $name ] )->getProperty(), $value->getProperty() );
		}
	}

	/**
	 * @dataProvider data4ObjectOperation
	 *
	 */
	public function testLoadObject( $name, $res, $default = null, $use_default = false )
	{
		Miao_Session::getInstance()->saveObject( $name, $res, $default, $use_default );

		$this->assertEquals( $res, Miao_Session::getInstance()->loadObject( $name ) );
	}

	public function testLoadException()
	{
		$this->setExpectedException( 'Miao_Session_Exception_OnVariableNotExists' );

		Miao_Session::getInstance()->loadObject( 'unexistningname' );
	}

	public function testLoadUnexistentName()
	{
		$this->assertEquals(
			null,
			Miao_Session::getInstance()->loadScalar( 'unexistningname1', null, true )
		);

		$this->assertEquals(
			1,
			Miao_Session::getInstance()->loadScalar( 'unexistningname2', 1, true )
		);

		$obj1 = new TestA( '1' );

		$this->assertEquals(
			$obj1,
			Miao_Session::getInstance()->loadObject( 'unexistningname3', $obj1, true )
		);

		$this->assertEquals(
			null,
			Miao_Session::getInstance()->loadScalar( 'unexistningname4', null, true )
		);
	}

	public function testSaveObjectException()
	{
		$this->setExpectedException( 'Miao_Session_Exception' );

		Miao_Session::getInstance()->saveObject( 'non_object', 'non_object' );
		Miao_Session::getInstance()->saveObject( 'non_object', null );
	}
}


// класс для тестирования сохране6ния объектов
class TestA
{
	protected $_property;

	public function __construct( $property )
	{
		$this->setProperty( $property );
	}

	public function getProperty()
	{
		return $this->_property;
	}
	public function setProperty( $property )
	{
		$this->_property = $property;
	}
}