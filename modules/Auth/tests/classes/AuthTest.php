<?php
/**
 * @author vpak
 * @date 2013-09-26 10:59:46
 */

namespace Miao;

class AuthTest extends \PHPUnit_Framework_TestCase
{
    private $_auth;

    public function setUp()
    {
        $data = array( 'michael' => '123', 'natalya' => 'qwe' );
        $adapter = new \Miao\Auth\Adapter\Standart( $data );
        $this->_auth = new \Miao\Auth( $adapter );
    }

    public function tearDown()
    {
        unset( $this->_auth );
    }

    /**
     * @dataProvider providerTestLogin
     * @param $login
     * @param $pass
     * @param $excepted
     */
    public function testLogin( $login, $pass, $expected )
    {
        $result = $this->_auth->login( $login, $pass );
        $actual = $result->getCode();
        $this->assertEquals( $expected, $actual );
    }

    /**
     * @codeCoverageIgnore
     * @return array
     */
    public function providerTestLogin()
    {
        $data = array();

        $data[ ] = array( 'michael', '123', \Miao\Auth\Result::SUCCESS );
        $data[ ] = array( 'michael', '1234', \Miao\Auth\Result::FAILURE );
        $data[ ] = array( 'natalya', '1234', \Miao\Auth\Result::FAILURE );
        $data[ ] = array( 'natalya', 'qwe', \Miao\Auth\Result::SUCCESS );
        $data[ ] = array( 'michael', 'qwe', \Miao\Auth\Result::FAILURE );

        return $data;
    }
}