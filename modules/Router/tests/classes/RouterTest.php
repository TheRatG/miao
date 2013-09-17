<?php
/**
 * @author vpak
 * @date 2013-09-16 18:06:26
 */

namespace Miao;

class RouterTest extends \PHPUnit_Framework_TestCase
{
    private $_router;

    public function setUp()
    {
        $router = new \Miao\Router( 'Miao\\TestOffice' );
        $rule = new \Miao\Router\Rule( '/', 'Main', \Miao\Autoload\ClassInfo::TYPE_OBJECT_REQUEST_VIEW, \Miao\Office\Request::METHOD_GET );
        $router->add(
            $rule
        );

        $router->add(
            new \Miao\Router\Rule( '/articles', 'Article\\List', \Miao\Autoload\ClassInfo::TYPE_OBJECT_REQUEST_VIEW, \Miao\Office\Request::METHOD_GET )
        );

        $router->add(
            new \Miao\Router\Rule( '/import', 'Import', \Miao\Autoload\ClassInfo::TYPE_OBJECT_REQUEST_VIEW, \Miao\Office\Request::METHOD_GET )
        );

        $router->add(
            new \Miao\Router\Rule( '/import', 'Import', \Miao\Autoload\ClassInfo::TYPE_OBJECT_REQUEST_ACTION, \Miao\Office\Request::METHOD_POST )
        );

        $rule = new \Miao\Router\Rule( '/article/:id', 'Article\\Item', \Miao\Autoload\ClassInfo::TYPE_OBJECT_REQUEST_VIEW, \Miao\Office\Request::METHOD_GET );
        $rule->addValidator( new \Miao\Router\Rule\Validator\Numeric( 'id', 1, false ) );
        $router->add(
            $rule
        );

        $rule = new \Miao\Router\Rule( '/news/:section/:id', 'News\\Item', \Miao\Autoload\ClassInfo::TYPE_OBJECT_REQUEST_VIEW, \Miao\Office\Request::METHOD_GET );
        $rule->addValidator( new \Miao\Router\Rule\Validator\In( 'section', array( 'social', 'finance' ) ) );
        $rule->addValidator( new \Miao\Router\Rule\Validator\Numeric( 'id', 1, false ) );
        $router->add(
            $rule
        );

        $rule = new \Miao\Router\Rule( '/news/:page', 'News\\List', \Miao\Autoload\ClassInfo::TYPE_OBJECT_REQUEST_VIEW, \Miao\Office\Request::METHOD_GET );
        $rule->addValidator( new \Miao\Router\Rule\Validator\Regexp( 'page', 'p([0-9]+)' ) );
        $router->add(
            $rule
        );

        $rule = new \Miao\Router\Rule( '/:pubDate', 'News\\List', \Miao\Autoload\ClassInfo::TYPE_OBJECT_REQUEST_VIEW, \Miao\Office\Request::METHOD_GET );
        $rule->addValidator( new \Miao\Router\Rule\Validator\Regexp( 'pubDate', '\d{4}/\d{2}/\d{2}', 2 ) );
        $router->add(
            $rule
        );

        $this->_router = $router;
    }

    public function tearDown()
    {
    }

    /**
     * @dataProvider providerTestView
     * @param $controller
     * @param array $params
     * @param $expected
     */
    public function testView( $controller, array $params, $expected )
    {
        $router = $this->_router;

        $actual = $router->view( $controller, $params );
        $this->assertEquals( $expected, $actual );
    }

    /**
     * @codeCoverageIgnore
     * @return array
     */
    public function providerTestView()
    {
        $data = array();

        $data[ ] = array( 'Article\\List', array(), '/articles' );
        $data[ ] = array( 'Article\\Item', array( 'id' => '123' ), '/article/123' );
        $data[ ] = array( 'Article\\Item', array( 'id' => '123', 'flag' => '1' ), '/article/123?flag=1' );
        $data[ ] = array(
            'Article\\Item',
            array(
                'id' => '123',
                'flag' => '1',
                'map' => 'google'
            ),
            '/article/123?flag=1&map=google'
        );
        return $data;
    }

    /**
     * @dataProvider providerTestRoute
     * @param $uri
     * @param $actual
     * @param string $method
     * @param bool $throwException
     * @param string $exceptionName
     */
    public function testRoute( $uri, $actual, $method = 'GET', $throwException = true, $exceptionName = '' )
    {
        if ( $exceptionName )
        {
            $this->setExpectedException( $exceptionName );
        }
        $router = $this->_router;
        $expected = $router->route( $uri, $method, $throwException );

        $this->assertEquals( $expected, $actual );
    }

    /**
     * @codeCoverageIgnore
     * @return array
     */
    public function providerTestRoute()
    {
        $data = array();

        $factory = new \Miao\Office\Factory();
        $data[ ] = array(
            '/import',
            array(
                $factory->getPrefixRequestName() => 'Miao\\TestOffice',
                $factory->getViewRequestName() => 'Import'
            )
        );
        $data[ ] = array(
            '/import',
            array(
                $factory->getPrefixRequestName() => 'Miao\\TestOffice',
                $factory->getActionRequestName() => 'Import'
            ),
            \Miao\Office\Request::METHOD_POST
        );
        $data[ ] = array(
            '/article/123',
            array(
                $factory->getPrefixRequestName() => 'Miao\\TestOffice',
                $factory->getViewRequestName() => 'Article\\Item',
                'id' => '123'
            )
        );

        $data[ ] = array(
            '/news/p1',
            array(
                $factory->getPrefixRequestName() => 'Miao\\TestOffice',
                $factory->getViewRequestName() => 'News\\List',
                'page' => 'p1'
            )
        );

        $data[ ] = array(
            '/news/social/123',
            array(
                $factory->getPrefixRequestName() => 'Miao\\TestOffice',
                $factory->getViewRequestName() => 'News\\Item',
                'section' => 'social',
                'id' => '123',
            )
        );

        $data[ ] = array(
            '/2012/10/29',
            array(
                $factory->getPrefixRequestName() => 'Miao\\TestOffice',
                $factory->getViewRequestName() => 'News\\List',
                'pubDate' => '2012/10/29',
            )
        );

        $data[ ] = array(
            '/2012/no_route',
            array(),
            \Miao\Office\Request::METHOD_GET,
            true,
            '\\Miao\\Router\\Exception'
        );
        $data[ ] = array(
            '/2012/no_route',
            false,
            \Miao\Office\Request::METHOD_GET,
            false
        );
        return $data;
    }
}