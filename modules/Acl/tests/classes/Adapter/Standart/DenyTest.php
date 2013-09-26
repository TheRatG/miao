<?php
/**
 * @author vpak
 * @date 2013-09-26 10:15:21
 */

namespace Miao\Acl\Adapter\Standart;

class DenyTest extends \PHPUnit_Framework_TestCase
{
    public function testDenyOne()
    {
        $adapter = new \Miao\Acl\Adapter\Standart();
        $acl = new \Miao\Acl( $adapter );

        $acl->addGroup( 'root' );
        $acl->addResource( 'Article' );
        $acl->addResource( 'Photo' );

        $acl->allow( 'root' );
        $acl->deny( 'root', 'Photo' );

        $this->assertTrue( $acl->isAllowed( 'root', 'Article' ) );
        $this->assertFalse( $acl->isAllowed( 'root', 'Photo' ) );
    }

    public function testDenyTwo()
    {
        $adapter = new \Miao\Acl\Adapter\Standart();
        $acl = new \Miao\Acl( $adapter );

        $acl->addGroup( 'root' );
        $acl->addResource( 'Article' );
        $acl->addResource( 'Photo' );

        $acl->allow( 'root' );
        $acl->deny(
            'root', 'Photo', array(
                                  'edit'
                             )
        );

        $this->assertTrue( $acl->isAllowed( 'root', 'Article' ) );
        $this->assertTrue( $acl->isAllowed( 'root', 'Photo' ) );
        $this->assertFalse( $acl->isAllowed( 'root', 'Photo', 'edit' ) );
    }

    public function testDenyThree()
    {
        $adapter = new \Miao\Acl\Adapter\Standart();
        $acl = new \Miao\Acl( $adapter );

        $acl->addGroup( 'root' );
        $acl->addResource( 'Photo' );

        $acl->allow(
            'root', 'Photo', array(
                                  'view'
                             )
        );
        $this->assertTrue( $acl->isAllowed( 'root', 'Photo' ) );

        $acl->deny( 'root', 'Photo' );
        $this->assertFalse( $acl->isAllowed( 'root', 'Photo', 'view' ) );
    }

    public function testDenyFour()
    {
        $adapter = new \Miao\Acl\Adapter\Standart();
        $acl = new \Miao\Acl( $adapter );

        $acl->addGroup( 'root' );
        $acl->addGroup( 'guest' );
        $acl->addResource( 'Article' );

        $acl->allow( 'root' );

        $acl->deny(
            null, 'Article', array(
                                  'archive'
                             )
        );
        $this->assertFalse( $acl->isAllowed( 'root', 'Article', 'archive' ) );
    }

    /**
     * Deny all and allow some resource
     */
    public function testDenyFive()
    {
        $adapter = new \Miao\Acl\Adapter\Standart();
        $acl = new \Miao\Acl( $adapter );

        $acl->addGroup( 'root' );
        $acl->addGroup( 'manager1' );
        $acl->addGroup( 'manager2' );
        $acl->addGroup( 'guest' );
        $acl->addResource( 'Article' );
        $acl->addResource( 'Photo' );

        $acl->deny( '*', 'Article' );
        $acl->allow( 'manager1', 'Article' );

        $this->assertTrue( $acl->isAllowed( 'manager1', 'Article' ) );
        $this->assertFalse( $acl->isAllowed( 'manager2', 'Article' ) );
    }
}