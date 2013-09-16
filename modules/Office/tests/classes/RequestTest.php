<?php
/**
 * @author vpak
 * @date 2013-09-16 11:09:36
 */

namespace Miao\Office;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
    }

    public function tearDown()
    {
    }

    /**
     * @dataProvider providerTestGetValueOf
     *
     * @param $name
     * @param $defaultValue
     * @param $throwException
     * @param $expected
     * @param string $exceptionName
     */
    public function testGetValueOf( $name, $defaultValue, $throwException, $expected, $exceptionName = '' )
    {
        if ( $exceptionName )
        {
            $this->setExpectedException( $exceptionName );
        }

        $request = new \Miao\Office\Request( array(
                                                  'val1' => '1',
                                                  'val2' => '2',
                                                  'val3' => '3',
                                                  'val4' => array( 1, 2, 3, 4 )
                                             ) );
        $actual = $request->getValueOf( $name, $defaultValue, $throwException );
        $this->assertEquals( $expected, $actual );
    }

    /**
     * @codeCoverageIgnore
     * @return array
     */
    public function providerTestGetValueOf()
    {
        $data = array();

        $data[ ] = array( 'val1', null, true, '1' );
        $data[ ] = array( 'val2', null, true, '2' );
        $data[ ] = array( 'val_is_not', '1', false, '1' );
        $data[ ] = array( 'val_is_not', null, true, null, '\\Miao\\Office\\Request\\Exception\\OnVarNotExists' );

        return $data;
    }
}