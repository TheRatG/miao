<?php
/**
 *
 * @author vpak
 * @date 2013-09-26 16:55:16
 */

namespace Miao;

class SessionTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
    }

    public function tearDown()
    {

    }

    public function testGetContainer()
    {
        $session = new \Miao\Session\Manager();
        $container = $session->getContainer( 'box' );
        $container->item = 'item';

        $this->assertEquals( 'item', $container->item );
    }
}