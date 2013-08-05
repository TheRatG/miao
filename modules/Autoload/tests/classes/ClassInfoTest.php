<?php
namespace Miao\Autoload;

class ClassInfoTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider providerTestIsTest
     * @param $className
     * @param $actual
     */
    public function testIsTest( $className, $actual )
    {
        $expected = ClassInfo::isTest( $className );
        $this->assertEquals( $expected, $actual );
    }

    public function providerTestIsTest()
    {
        $data = array();

        $data[] = array( 'AutoloadTest', true );
        $data[] = array( 'AutoloadTestTest', true );
        $data[] = array( 'Miao\\Autoload\\AutoloadTest', true );
        $data[] = array( 'Miao\\Autoload\\Autoload', false );
        $data[] = array( 'Miao\\Autoload\\AutoloadTest2', false );

        return $data;
    }
}