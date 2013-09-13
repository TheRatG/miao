<?php
/**
 * @author vpak
 * @date 2013-09-13 15:52:26
 */

namespace Miao\Router;

class RuleTest extends \PHPUnit_Framework_TestCase
{
    public function getFactory()
    {
        $result = new \Miao\Office\Factory();
        return $result;
    }

    public function tearDown()
    {
    }

    /**
     * @dataProvider providerTestMakeUrl
     * @param Rule $rule
     * @param array $params
     * @param $method
     * @param $expected
     */
    public function testMakeUrl( \Miao\Router\Rule $rule, array $params, $method, $expected )
    {
        $actual = $rule->makeUrl( $params, $method );
        $this->assertEquals( $expected, $actual );
    }

    /**
     * @codeCoverageIgnore
     * @return array
     */
    public function providerTestMakeUrl()
    {
        $data = array();

        $factory = $this->getFactory();

        $rule = new \Miao\Router\Rule( '/article', 'Article\List', \Miao\Autoload\ClassInfo::TYPE_OBJECT_REQUEST_VIEW, 'get' );
        $data[ ] = array( $rule, array( $factory->getViewRequestName() => 'Article', 'page' => 2 ), 'get', '/article' );

        return $data;
    }
}