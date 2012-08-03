<?php
class Miao_Office_Request_Test extends PHPUnit_Framework_TestCase
{

	public function setUp()
	{
		$_GET = array();
		$_SERVER[ 'REQUEST_METHOD' ] = 'get';

		$_GET[ 'test_var_string' ] = 'тестовая строка';
		$_GET[ 'test_var_int' ] = 59;
		$_GET[ 'test_var_bool' ] = true;
		$_GET[ 'test_zero_int' ] = 0;
		$_GET[ 'test_zero_string' ] = '0';
		$_GET[ 'test_null' ] = null;

		Miao_Office_Request::getInstance()->resetVars();
	}

	public function tearDown()
	{
	}

	public function data4getValueOf()
	{
		$data = array();

		$data[] = array(
			'test_var_string',
			'тестовая строка',
			false,
			null,
			false );
		$data[] = array( 'test_var_int', 59, false, 80, false );
		$data[] = array( 'test_var_int', 59, false, 80, true );
		$data[] = array( 'test_var_int2', 80, false, 80, true );
		$data[] = array( 'test_var_int2', '', true, null, false );
		$data[] = array( 'test_var_int2', null, false, null, true );


		$data[] = array( 'test_zero_int', 0, false, null, false );
		$data[] = array( 'test_zero_string', '0', false, null, false );
		$data[] = array( 'test_null', null, false, null, false );

		return $data;
	}

	/**
     * @dataProvider data4getValueOf
     *
     */
	public function testGetValueOf( $var_name, $expected_value, $expected_exception, $default_value, $use_null_as_default )
	{
		$request = Miao_Office_Request::getInstance();
		if ( $expected_exception == true )
		{
			$this->setExpectedException( 'Miao_Office_Request_Exception_OnVarNotExists' );
		}
		$value = $request->getValueOf( $var_name, $default_value, $use_null_as_default );
		$this->assertEquals( $value, $expected_value );
	}

	/**
	 * Преобразует специальные символы в HTML сущности и удаляет теги.
	 *
	 * @param string $data
	 * @param string $allowable_tags указания тэгов, которые не должны удаляться
	 * @return string
	 */
	public function data4stripRequestedString()
	{
		return array(
			array( 'тест', 'тест', '' ),
			array( 'тест', 'тест', 'a' ),
			array( '<b>тест</b><i>после</i>', '<b>тест</b>после', '<b>' ),
			array(
				'<b>тест</b><i>после</i>',
				'<b>тест</b><i>после</i>',
				'<b>,<i>' ),
			array( '<b>тест</b><i>после</i>', 'тестпосле', '' ) );
	}

	/**
	 * @dataProvider data4stripRequestedString
	 *
	 */
	public function testStripRequestedString( $data, $expected_res, $allowable_tags = '' )
	{
		$request = Miao_Office_Request::getInstance();
		$res = $request->stripRequestedString( $data, $allowable_tags );
		$this->assertEquals( htmlspecialchars( $expected_res ), $res );
	}

	public function data4stripHTMLAttributes()
	{
		return array(
			array( 'тест', 'тест' ),
			array( '<b>тест</b><i>после</i>', '<b>тест</b><i>после</i>' ),
			array( '<b gh="33">тест</b><i>после</i>', '<b>тест</b><i>после</i>' ) );
	}

	/**
	 * @dataProvider data4stripHTMLAttributes
	 *
	 */
	public function stripHTMLAttributes( $data, $expected_res )
	{
		$request = Miao_Office_Request::getInstance();
		$res = $request->stripHTMLAttributes( $data );
		$this->assertEquals( $expected_res, $res );
	}

	public function testGetVars()
	{
		$request = Miao_Office_Request::getInstance();
		$res = $_GET;
		//$res[ Miao_Core_Config::Modules( 'Miao_Office' )->Request->varname->data_store ] = array();
		$this->assertEquals( $request->getVars(), $res );
	}

	public function testGetServerVar()
	{
		$_SERVER = array( 'REQUEST_URI' => '/' );
		$r = Miao_Office_Request::getInstance();

		$this->assertSame( $r->getServerVar( 'HTTP_HOST' ), false );

		$_SERVER[ 'HTTP_HOST' ] = 'test.rbc.ru';

		$this->assertSame( $r->getServerVar( 'HTTP_HOST' ), 'test.rbc.ru' );

		$_SERVER[ 'SERVER_NAME' ] = 'test2.rbc.ru';

		$this->assertSame( $r->getServerVar( 'HTTP_HOST' ), 'test2.rbc.ru' );
		$this->assertSame( $r->getServerVar( 'SERVER_NAME' ), 'test2.rbc.ru' );

		unset( $_SERVER[ 'SERVER_NAME' ] );

		$this->assertSame( $r->getServerVar( 'SERVER_NAME' ), 'test.rbc.ru' );
		$this->assertSame( $r->getServerVar( 'REQUEST_URI' ), '/' );
	}
}
