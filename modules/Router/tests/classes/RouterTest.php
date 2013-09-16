<?php
/**
 * @author vpak
 * @date 2013-09-16 18:06:26
 */

namespace Miao;

class RouterTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
    }

    public function tearDown()
    {
    }

    public function testEmpty()
    {
        $router = new \Miao\Router();
        $rule = new \Miao\Router\Rule( '/', 'Main', \Miao\Autoload\ClassInfo::TYPE_OBJECT_REQUEST_VIEW, 'GET' );
        $router->setDefaultRule( $rule );

        $router->add(
            new \Miao\Router\Rule( '/articles', 'Article\\List', \Miao\Autoload\ClassInfo::TYPE_OBJECT_REQUEST_VIEW, 'GET' )
        );

        $rule = new \Miao\Router\Rule( '/article/:id', 'Article\\Item', \Miao\Autoload\ClassInfo::TYPE_OBJECT_REQUEST_VIEW, 'GET' );
        $rule->addValidator( new \Miao\Router\Rule\Validator\Numeric( 'id', 1, false ) );
        $router->add(
            $rule
        );

        $rule = new \Miao\Router\Rule( '/news/:section/:id', 'News\\Item', \Miao\Autoload\ClassInfo::TYPE_OBJECT_REQUEST_VIEW, 'GET' );
        $rule->addValidator( new \Miao\Router\Rule\Validator\Numeric( 'id', 1, false ) );
        $rule->addValidator( new \Miao\Router\Rule\Validator\In( 'section', array( 'social,finance' ), ',' ) );
        $router->add(
            $rule
        );
    }
}