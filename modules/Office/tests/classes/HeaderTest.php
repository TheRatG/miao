<?php
/**
 * @author vpak
 * @date 2013-09-09 15:31:10
 */

namespace Miao\Office;

class HeaderTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
    }

    public function tearDown()
    {
    }

    public function testDefault()
    {
        $header = new \Miao\Office\Header();
        $this->assertEquals( 'text/html; charset=utf-8', $header->get( 'Content-type' ) );
        $this->assertEquals( 'Content-type: text/html; charset=utf-8', $header->get( 'Content-type', true ) );
    }

    public function testGetList()
    {
        $header = new \Miao\Office\Header();

        $expected = array( 'Content-type: text/html; charset=utf-8', 'X-Accel-Expires: 10' );
        $header->set( 'X-Accel-Expires', 10 );
        $actual = $header->getList();

        $this->assertEquals( $expected, $actual );
    }
}