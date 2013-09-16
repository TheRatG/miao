<?php
/**
 *
 * @author vpak
 * @date 2013-09-16 16:39:23
 */

namespace Miao\Router\Rule\Validator;

class RegexpTest extends \PHPUnit_Framework_TestCase
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

        $obj = \Miao\Router\Rule\Validator\Regexp::create( $config );
        $expected = $obj->test( $value );

        $this->assertEquals( $expected, $actual );
    }

    public function dataProviderTestTest()
    {
        $data = array();

        $config = array( 'id' => 'section' );
        $data[] = array(
            $config,
            'game',
            false,
            '\Miao\Router\Rule\Validator\Exception' );

        $config = array( 'id' => 'section', 'pattern' => '(business|work|home)' );
        $data[] = array( $config, 'game', false );
        $data[] = array( $config, 'work', true );

        $config = array( 'id' => 'section', 'pattern' => '([0-9a-z]+)' );
        $data[] = array( $config, 'work99', true );

        $data[] = array( $config, '', false );

        $config = array( 'id' => 'section', 'pattern' => '^[a-zA-Z_]+$' );
        $data[] = array( $config, 'p2', false );

        return $data;
    }
}