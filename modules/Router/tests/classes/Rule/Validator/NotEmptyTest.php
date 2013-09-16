<?php
/**
 *
 * @author vpak
 * @date 2013-09-16 16:35:04
 */

namespace Miao\Router\Rule\Validator;

class NotEmptyTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @dataProvider dataProviderTestTest
     */
    public function testTest( $config, $value, $actual, $exceptionName = '' )
    {
        if ( $exceptionName )
        {
            $this->setExpectedException( $exceptionName );
        }

        $obj = new \Miao\Router\Rule\Validator\NotEmpty( $config );
        $expected = $obj->test( $value );

        $this->assertEquals( $expected, $actual );
    }

    public function dataProviderTestTest()
    {
        $data = array();

        $config = array( 'id' => 'section' );

        $data[] = array( $config, 0, true );
        $data[] = array( $config, null, false );
        $data[] = array( $config, 'work', true );
        $data[] = array(
            $config,
            '',
            false );

        $config = array( 'id' => 'section', 'min' => 2 );
        $data[] = array( $config, 'work', true );
        $data[] = array( $config, 'a', false );

        $config = array( 'id' => 'section', 'max' => 3 );
        $data[] = array( $config, 'work', false );

        return $data;
    }
}