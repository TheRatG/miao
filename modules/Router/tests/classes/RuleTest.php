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
     * @dataProvider providerMakeUrlSoft
     * @param Rule $rule
     * @param array $params
     * @param $method
     * @param $expected
     */
    public function testMakeUrlSoft( \Miao\Router\Rule $rule, array $params, $method, $expected )
    {
        $actual = $rule->makeUrl( $params, $method );
        $this->assertEquals( $expected, $actual );
    }

    /**
     * @codeCoverageIgnore
     * @return array
     */
    public function providerMakeUrlSoft()
    {
        $data = array();

        $factory = $this->getFactory();

        $rule = new \Miao\Router\Rule( '/article', 'Article\List', \Miao\Autoload\ClassInfo::TYPE_OBJECT_REQUEST_VIEW, 'get' );
        $page = 2;
        $expected = sprintf( '/article?%s=Article&page=%s', $factory->getViewRequestName(), $page );
        $data[ ] = array(
            $rule,
            array( $factory->getViewRequestName() => 'Article', 'page' => $page ),
            'get',
            $expected
        );
        return $data;
    }

    /**
     * @dataProvider dataProviderTestFactory
     */
    public function testFactory( $config, $exceptionName = '' )
    {
        if ( $exceptionName )
        {
            $this->setExpectedException( $exceptionName );
        }

        $rule = \Miao\Router\Rule::factory( $config );
        $this->assertInstanceOf( '\\Miao\\Router\\Rule', $rule );
    }

    public function dataProviderTestFactory()
    {
        $data = array();

        $data[ ] = array(
            array(
                'rule' => '/news/:id',
                'type' => 'News\\Item',
                'validators' => array(
                    array( 'type' => 'numeric', 'id' => 'id' )
                )
            ),
            '\\Miao\\Router\\Exception'
        );

        $data[ ] = array(
            array(
                'rule' => '/news/:id',
                'type' => \Miao\Autoload\ClassInfo::TYPE_OBJECT_REQUEST_VIEW,
                'validators' => array(
                    array( 'type' => 'numeric', 'id' => 'id' )
                )
            ),
            '\\Miao\\Router\\Exception'
        );

        $data[ ] = array(
            array(
                'rule' => '/news/:id',
                'type' => \Miao\Autoload\ClassInfo::TYPE_OBJECT_REQUEST_VIEW,
                'name' => 'News\\Item',
                'validators' => array()
            )
        );

        $data[ ] = array(
            array(
                'rule' => '/news/:id',
                'type' => \Miao\Autoload\ClassInfo::TYPE_OBJECT_REQUEST_VIEW,
                'name' => 'News\\Item',
                'validators' => array(
                    array( 'type' => 'numeric', 'id' => 'id' )
                )
            )
        );

        return $data;
    }

    /**
     * @dataProvider dataProviderTestConstruct
     */
    public function testConstruct( $prefix, $type, $name, $rule, $validators, $exceptionName = '' )
    {
        if ( !empty( $exceptionName ) )
        {
            $this->setExpectedException( $exceptionName );
        }

        $route = new \Miao\Router\Rule( $rule, $name, $type, 'get', $validators, 'desc', $prefix );

        $this->assertEquals( $route->getControllerType(), $type );
        $this->assertEquals( $route->getController(), $name );
    }

    public function dataProviderTestConstruct()
    {
        $data = array();

        $data[ ] = array(
            'Miao\\TestOffice',
            \Miao\Autoload\ClassInfo::TYPE_OBJECT_REQUEST_VIEW,
            'Article\\Item',
            '/article/:id',
            array()
        );

        //check type
        $data[ ] = array(
            'Miao\\TestOffice',
            'viiiieeev',
            'Article\\Item',
            '/article/:id',
            array(),
            '\\Miao\\Router\\Rule\\Exception'
        );

        return $data;
    }

    /**
     * @dataProvider dataProviderTestMatch
     */
    public function testMatch( $config, $uri, $actual )
    {
        $route = \Miao\Router\Rule::factory( $config );
        $expected = $route->match( $uri );

        $this->assertEquals( $expected, $actual );
    }

    public function dataProviderTestMatch()
    {
        $data = array();

        $data[ ] = array(
            array(
                'prefix' => 'Miao\\TestOffice',
                'type' => \Miao\Autoload\ClassInfo::TYPE_OBJECT_REQUEST_VIEW,
                'name' => 'Article\\Item',
                'rule' => '/article/:id',
                'validators' => array()
            ),
            '/news/123',
            false
        );

        $data[ ] = array(
            array(
                'prefix' => 'Miao\\TestOffice',
                'type' => \Miao\Autoload\ClassInfo::TYPE_OBJECT_REQUEST_VIEW,
                'name' => 'Article\\Item',
                'rule' => '/article/:id',
                'validators' => array(
                    array( 'id' => 'id', 'type' => 'Numeric' )
                )
            ),
            '/article/123',
            array( '_view' => 'Article\\Item', 'id' => '123' )
        );

        return $data;
    }

    /**
     * @dataProvider dataProviderTestMakeUrl
     */
    public function testMakeUrl( array $config, array $params, $actual, $exceptionName = '' )
    {
        if ( $exceptionName )
        {
            $this->setExpectedException( $exceptionName );
        }
        $route = \Miao\Router\Rule::factory( $config );
        $expected = $route->makeUrl( $params );
        $this->assertEquals( $expected, $actual );
    }

    public function dataProviderTestMakeUrl()
    {
        $data = array();

        $config = array(
            'prefix' => 'Miao\\TestOffice',
            'type' => \Miao\Autoload\ClassInfo::TYPE_OBJECT_REQUEST_VIEW,
            'name' => 'Article\\Item',
            'rule' => '/article/:id',
            'validators' => array()
        );
        $data[ ] = array( $config, array( 'id' => 123 ), 'article/123' );

        $config = array(
            'prefix' => 'Miao\\TestOffice',
            'type' => \Miao\Autoload\ClassInfo::TYPE_OBJECT_REQUEST_VIEW,
            'name' => 'Article\\Item',
            'rule' => '/article/:id',
            'validators' => array( array( 'id' => 'id', 'type' => 'Numeric' ) )
        );
        $data[ ] = array( $config, array( 'id' => 123 ), 'article/123' );

        $data[ ] = array(
            $config,
            array( 'id' => 'not numeric' ),
            '',
            '\\Miao\\Router\\Rule\\Exception'
        );

        $config = array(
            'prefix' => 'Miao\\TestOffice',
            'type' => \Miao\Autoload\ClassInfo::TYPE_OBJECT_REQUEST_VIEW,
            'name' => 'Article\\Item',
            'rule' => '/article/:section',
            'validators' => array(
                array(
                    'id' => 'section',
                    'type' => 'In',
                    'variants' => 'lifestyle,finance'
                )
            )
        );
        $data[ ] = array(
            $config,
            array( 'section' => '' ),
            '',
            '\\Miao\\Router\\Rule\\Exception'
        );
        $data[ ] = array(
            $config,
            array( 'section' => 'finance' ),
            'article/finance'
        );
        $data[ ] = array(
            $config,
            array( 'section' => 'focus' ),
            '',
            '\\Miao\\Router\\Rule\\Exception'
        );

        $config = array(
            'prefix' => 'Miao\\TestOffice',
            'type' => \Miao\Autoload\ClassInfo::TYPE_OBJECT_REQUEST_VIEW,
            'name' => 'Article\\Item',
            'rule' => '/article/:section',
            'validators' => array(
                array(
                    'id' => 'section',
                    'type' => 'In',
                    'variants' => 'lifestyle,finance'
                ),
                array(
                    'id' => 'section2',
                    'type' => 'In',
                    'variants' => 'lifestyle,finance'
                )
            )
        );
        $data[ ] = array(
            $config,
            array( 'section' => '' ),
            '',
            '\\Miao\\Router\\Rule\\Exception'
        );

        return $data;
    }

    /**
     * @dataProvider dataProviderTestMakeRewrite
     */
    public function testMakeRewrite( array $config, $actual )
    {
        $route = \Miao\Router\Rule::factory( $config );
        $expected = $route->makeRewrite();
        $this->assertEquals( $actual, $expected );
    }

    public function dataProviderTestMakeRewrite()
    {
        $data = array();

        $config = array(
            'prefix' => 'Miao\\TestOffice2',
            'type' => \Miao\Autoload\ClassInfo::TYPE_OBJECT_REQUEST_VIEW,
            'name' => 'Article\\Item',
            'desc' => 'Article item page. Some notes.',
            'rule' => '/article/:id',
            'validators' => array()
        );
        $data[ ] = array(
            $config,
            '# view:Article\\Item Article item page. Some notes.' . "\n" . 'RewriteRule ^article/([^/]+)$ index.php?id=$1&_view=Article\\Item&_prefix=Miao\\TestOffice2 [L,QSA]'
        );

        $config = array(
            'prefix' => 'Miao\\TestOffice',
            'type' => \Miao\Autoload\ClassInfo::TYPE_OBJECT_REQUEST_VIEW,
            'name' => 'Article\\Item',
            'rule' => '/article/:id',
            'validators' => array( array( 'id' => 'id', 'type' => 'Numeric' ) )
        );
        $data[ ] = array(
            $config,
            '# view:Article\\Item' . "\n" . 'RewriteRule ^article/([0-9]+)$ index.php?id=$1&_view=Article\\Item&_prefix=Miao\\TestOffice [L,QSA]'
        );

        $config = array(
            'prefix' => 'Miao\\TestOffice',
            'type' => \Miao\Autoload\ClassInfo::TYPE_OBJECT_REQUEST_ACTION,
            'name' => 'Article\\Item',
            'rule' => '/article/:section',
            'validators' => array(
                array(
                    'id' => 'section',
                    'type' => 'In',
                    'variants' => 'lifestyle,finance'
                )
            )
        );

        $data[ ] = array(
            $config,
            '# action:Article\\Item' . "\n" . 'RewriteRule ^article/(lifestyle|finance)$ index.php?section=$1&_action=Article\\Item&_prefix=Miao\\TestOffice [L,QSA]'
        );

        $config = array(
            'prefix' => 'Miao\\TestOffice',
            'type' => \Miao\Autoload\ClassInfo::TYPE_OBJECT_REQUEST_VIEW,
            'name' => 'Article\\Item',
            'rule' => '/:page/:id/:part/:user/:mode/:param',
            'validators' => array(
                array( 'id' => 'id', 'type' => 'Numeric' ),
                array( 'id' => 'part', 'type' => 'Numeric', 'max' => 5, 'min' => 0 ),
                array( 'id' => 'user', 'type' => 'Numeric', 'min' => 32, 'max' => 32 ),
                array( 'id' => 'page', 'type' => 'Numeric', 'min' => 1 ),
                array( 'id' => 'mode', 'type' => 'Numeric', 'min' => 2 ),
                array( 'id' => 'param', 'type' => 'Numeric', 'min' => 3, 'max' => 5 )

            )
        );
        $data[ ] = array(
            $config,
            '# view:Article\\Item' . "\n" . 'RewriteRule ^([0-9]+)/([0-9]+)/([0-9]+)/([0-9]{32})/([0-9]{2,})/([0-9]{3,5})$ index.php?page=$1&id=$2&part=$3&user=$4&mode=$5&param=$6&_view=Article\\Item&_prefix=Miao\\TestOffice [L,QSA]'
        );

        return $data;
    }
}