<?php
namespace Miao\Autoload;

class ClassInfoTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider providerTestParse
     * @param $string
     * @param array $expected
     */
    public function testParse( $string, array $expected )
    {
        $actual = ClassInfo::parse( $string )
            ->toArray();
        $this->assertEquals( $expected, $actual );
    }

    /**
     * @codeCoverageIgnore
     * @return array
     */
    public function providerTestParse()
    {
        $data = array();

        $data[ ] = array(
            'Miao_Office',
            array(
                'parsedString' => 'Miao_Office',
                'lib' => 'Miao',
                'module' => 'Office',
                'class' => 'Miao_Office'
            )
        );

        $data[ ] = array(
            '\\Miao\\Office',
            array(
                'parsedString' => '\\Miao\\Office',
                'lib' => 'Miao',
                'module' => 'Office',
                'class' => 'Miao\\Office'
            )
        );

        $data[ ] = array(
            'Miao\Office\View::__construct',
            array(
                'parsedString' => 'Miao\Office\View::__construct',
                'lib' => 'Miao',
                'module' => 'Office',
                'class' => 'Miao\\Office\\View'
            )
        );

        return $data;
    }

    /**
     * @dataProvider providerTestIsTest
     * @param $className
     * @param $expected
     */
    public function testIsTest( $className, $expected )
    {
        $actual = ClassInfo::parse( $className )
            ->isTest();
        $this->assertEquals( $expected, $actual );
    }

    /**
     * @codeCoverageIgnore
     * @return array
     */
    public function providerTestIsTest()
    {
        $data = array();

        $data[ ] = array( 'AutoloadTest', true );
        $data[ ] = array( 'AutoloadTestTest', true );
        $data[ ] = array( 'Miao\\Autoload\\AutoloadTest', true );
        $data[ ] = array( 'Miao\\Autoload\\Autoload', false );
        $data[ ] = array( 'Miao\\Autoload\\AutoloadTest2', false );

        return $data;
    }

    /**
     * @dataProvider providerTestGetClass
     * @param $string
     * @param $short
     * @param $expected\
     */
    public function testGetClass( $string, $short, $expected )
    {
        $actual = ClassInfo::parse( $string )
            ->getClass( $short );
        $this->assertEquals( $expected, $actual );
    }

    /**
     * @codeCoverageIgnore
     * @return array
     */
    public function providerTestGetClass()
    {
        $data = array();

        $data[ ] = array( 'Miao_Office', false, 'Miao_Office' );
        $data[ ] = array( 'Miao_Office_View', true, 'View' );
        $data[ ] = array( 'Miao_Office_View_Main', true, 'View_Main' );
        $data[ ] = array( 'Miao_Office_ViewBlock_Slave_Article', true, 'ViewBlock_Slave_Article' );

        $data[ ] = array( 'Miao\\Office', false, 'Miao\\Office' );
        $data[ ] = array( 'Miao\\Office\\View', true, 'View' );
        $data[ ] = array( 'Miao\\Office\\View\\Main', true, 'View\\Main' );
        $data[ ] = array( 'Miao\\Office\\ViewBlock\\Slave\\Article', true, 'ViewBlock\\Slave\\Article' );

        return $data;
    }
}