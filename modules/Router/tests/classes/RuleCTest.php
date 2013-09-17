<?php
/**
 * @author vpak
 * @date 2013-09-17 10:31:46
 */

namespace Miao\Router;

class RuleCTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
    }

    public function tearDown()
    {
    }

    public function testEmpty()
    {
        $rule = new \Miao\Router\Rule( '/article/:id', 'Article\\Item', \Miao\Autoload\ClassInfo::TYPE_OBJECT_REQUEST_VIEW, \Miao\Office\Request::METHOD_GET );
        $rule->addValidator( new \Miao\Router\Rule\Validator\Numeric( 'id', 1, false ) );

        $expected = '/article/123?_miaoView=Article%5CItem';
        $actual = $rule->makeUrl( array( 'id' => 123, '_miaoView' => 'Article\\Item' ) );
        $this->assertEquals( $expected, $actual );
    }
}