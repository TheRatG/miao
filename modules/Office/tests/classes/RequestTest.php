<?php
/**
 * @author vpak
 * @date 2013-08-13 11:38:02
 */

namespace Miao\Office;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Miao\Office\Request
     */
    protected $_request;

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

        $this->_request = new \Miao\Office\Request();
    }

    public function tearDown()
    {
        unset( $this->_request );
    }

    public function data4getValueOf()
    {
        $data = array();

        $data[ ] = array(
            'test_var_string',
            'тестовая строка',
            false,
            null,
            false
        );
        $data[ ] = array( 'test_var_int', 59, false, 80, false );
        $data[ ] = array( 'test_var_int', 59, false, 80, true );
        $data[ ] = array( 'test_var_int2', 80, false, 80, true );
        $data[ ] = array( 'test_var_int2', '', true, null, false );
        $data[ ] = array( 'test_var_int2', null, false, null, true );

        $data[ ] = array( 'test_zero_int', 0, false, null, false );
        $data[ ] = array( 'test_zero_string', '0', false, null, false );
        $data[ ] = array( 'test_null', null, false, null, false );

        return $data;
    }

    /**
     * @dataProvider data4getValueOf
     */
    public function testGetValueOf( $varName, $expected, $expectedException, $defaultValue = null )
    {
        $request = $this->_request;
        if ( $expectedException == true )
        {
            $this->setExpectedException( '\\Miao\\Office\\Request\\Exception\\OnVarNotExists' );
        }
        $value = $request->getValueOf( $varName, $defaultValue, $expectedException );
        $this->assertEquals( $value, $expected );
    }

    /**
     * Преобразует специальные символы в HTML сущности и удаляет теги.
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
                '<b>,<i>'
            ),
            array( '<b>тест</b><i>после</i>', 'тестпосле', '' )
        );
    }

    /**
     * @dataProvider data4stripRequestedString
     */
    public function testStripRequestedString( $data, $expected_res, $allowable_tags = '' )
    {
        $request = $this->_request;
        $res = $request->stripRequestedString( $data, $allowable_tags );
        $this->assertEquals( htmlspecialchars( $expected_res ), $res );
    }

    public function data4stripHTMLAttributes()
    {
        return array(
            array( 'тест', 'тест' ),
            array( '<b>тест</b><i>после</i>', '<b>тест</b><i>после</i>' ),
            array( '<b gh="33">тест</b><i>после</i>', '<b>тест</b><i>после</i>' )
        );
    }

    /**
     * @dataProvider data4stripHTMLAttributes
     */
    public function stripHTMLAttributes( $data, $expected_res )
    {
        $request = $this->_request;
        $res = $request->stripHTMLAttributes( $data );
        $this->assertEquals( $expected_res, $res );
    }

    public function testGetVars()
    {
        $request = $this->_request;
        $res = $_GET;
        $this->assertEquals( $request->getVars(), $res );
    }

    public function testValuesDoesNotExist()
    {
        $request = $this->_request;
        $res = $request->getValueOf( 'variable_does_not_exists', null, false );
        $this->assertNull( $res );
    }
}