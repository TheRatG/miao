<?php
/**
 *
 * @author vpak
 * @date 2013-09-13 17:45:34
 */

namespace Miao\Router\Rule;

class ValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider dataProviderTestFactory
     */
    public function testFactory( $config, $expected, $exceptionName = '' )
    {
        if ( $exceptionName )
        {
            $this->setExpectedException( $exceptionName );
        }

        $actual = \Miao\Router\Rule\Validator::factory( $config );
        $this->assertInstanceOf( $expected, $actual );
    }

    public function dataProviderTestFactory()
    {
        $data = array();

        $config = array( 'id' => null, 'type' => 'Compare' );
        $data[] = array(
            $config,
            '\Miao\Router\Rule\Validator\Compare',
            '\Miao\Router\Rule\Validator\Exception' );

        $config = array( 'id' => null, 'type' => 'Compare', 'str' => 'test' );
        $data[] = array( $config, '\Miao\Router\Rule\Validator\Compare', '' );

        $config = array( 'id' => 'section', 'type' => 'NotEmpty' );
        $data[] = array( $config, '\Miao\Router\Rule\Validator\NotEmpty', '' );

        $config = array( 'id' => 'code', 'type' => 'Numeric' );
        $data[] = array( $config, '\Miao\Router\Rule\Validator\Numeric', '' );

        $config = array( 'id' => 'code', 'type' => 'In', 'variants' => '1,2,3,4,5', 'delimiter' => ',' );
        $data[] = array( $config, '\Miao\Router\Rule\Validator\In', '' );

        $config = array( 'id' => 'code', 'type' => 'Regexp', 'pattern' => "([a-z]+)" );
        $data[] = array( $config, '\Miao\Router\Rule\Validator\Regexp', '' );

        return $data;
    }
}