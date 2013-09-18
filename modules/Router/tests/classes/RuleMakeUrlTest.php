<?php
/**
 * @author vpak
 * @date 2013-09-17 10:31:46
 */

namespace Miao\Router;

class RuleMakeUrlTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
    }

    public function tearDown()
    {
    }

    public function getFactory()
    {
        $result = new \Miao\Office\Factory();
        return $result;
    }

    public function testFirst()
    {
        $rule = new \Miao\Router\Rule( '/article/:id', 'Article\\Item', \Miao\Autoload\ClassInfo::TYPE_OBJECT_REQUEST_VIEW, \Miao\Office\Request::METHOD_GET );
        $rule->addValidator( new \Miao\Router\Rule\Validator\Numeric( 'id', 1, false ) );

        $expected = '/article/123';
        $actual = $rule->makeUrl( array( 'id' => 123 ) );
        $this->assertEquals( $expected, $actual );
    }

    public function testEmpty()
    {
        $rule = new \Miao\Router\Rule( '/', 'Main', \Miao\Autoload\ClassInfo::TYPE_OBJECT_REQUEST_VIEW, \Miao\Office\Request::METHOD_GET );

        $expected = '/';
        $actual = $rule->makeUrl();
        $this->assertEquals( $expected, $actual );
    }
}