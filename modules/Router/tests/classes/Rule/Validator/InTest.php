<?php
/**
 * @author vpak
 * @date 2013-09-16 16:36:25
 */

namespace Miao\Router\Rule\Validator;

class InTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider dataProviderTestTest
     */
    public function testTest( $config, $value, $actual, $exceptionName = '' )
    {
        if ( $exceptionName )
        {
            $this->setExpectedException( $exceptionName );
        }

        $obj = new \Miao\Router\Rule\Validator\In( $config );
        $expected = $obj->test( $value );

        $this->assertEquals( $expected, $actual );
    }

    public function dataProviderTestTest()
    {
        $data = array();

        $config = array( 'id' => 'section', 'variants' => 'business,work,home' );
        $data[ ] = array( $config, 'game', false );

        $config = array(
            'id' => 'section',
            'variants' => 'business:work:home',
            'delimiter' => ':'
        );
        $data[ ] = array( $config, 'work', true );

        $config = array(
            'id' => 'section',
            'variants' => 'business,work,home,,,'
        );
        $data[ ] = array(
            $config,
            'work',
            false,
            '\Miao\Router\Rule\Validator\Exception'
        );

        return $data;
    }
}