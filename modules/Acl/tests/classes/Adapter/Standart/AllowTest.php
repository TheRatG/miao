<?php
/**
 * @author vpak
 * @date 2013-09-26 10:08:09
 */

namespace Miao\Acl\Adapter\Standart;

class AllowTest extends \PHPUnit_Framework_TestCase
{
    public function testAllowOne()
    {
        $adapter = new \Miao\Acl\Adapter\Standart();
        $acl = new \Miao\Acl( $adapter );

        $acl->addGroup( 'root' );
        $acl->addGroup( 'guest' );
        $acl->addResource( 'Article' );

        $acl->allow( 'root' );
        $acl->allow( 'guest', 'Article' );

        $this->assertTrue( $acl->isAllowed( 'root', 'Article' ) );
        $this->assertTrue( $acl->isAllowed( 'guest', 'Article' ) );
    }

    public function testAllowTwo()
    {
        $adapter = new \Miao\Acl\Adapter\Standart();
        $acl = new \Miao\Acl( $adapter );

        $acl->addGroup( 'root' );
        $acl->addGroup( 'guest' );
        $acl->addResource( 'Article' );

        $acl->allow( 'root' );

        $this->assertTrue( $acl->isAllowed( 'root', 'Article' ) );
        $this->assertFalse( $acl->isAllowed( 'guest', 'Article' ) );
    }

    public function testAllowThree()
    {
        $adapter = new \Miao\Acl\Adapter\Standart();
        $acl = new \Miao\Acl( $adapter );

        $acl->addGroup( 'root' );
        $acl->addResource( 'Article' );

        $acl->allow( 'root' );

        $this->assertTrue( $acl->isAllowed( 'root', 'Article' ) );
        $this->assertFalse( $acl->isAllowed( 'guest', 'Article' ) );
    }

    public function testAllowFour()
    {
        $adapter = new \Miao\Acl\Adapter\Standart();
        $acl = new \Miao\Acl( $adapter );

        $acl->addGroup( 'root' );
        $acl->addGroup( 'guest' );
        $acl->addGroup( 'editor' );
        $acl->addResource( 'Article' );

        $acl->allow( 'root', 'Article' );
        $acl->allow(
            'guest', 'Article', array(
                                     'view'
                                )
        );
        $acl->allow(
            'editor', 'Article', array(
                                      'view',
                                      'edit'
                                 )
        );

        $this->assertTrue( $acl->isAllowed( 'guest', 'Article' ) );
        $this->assertTrue( $acl->isAllowed( 'guest', 'Article', 'view' ) );
        $this->assertFalse( $acl->isAllowed( 'guest', 'Article', 'edit' ) );
        $this->assertTrue( $acl->isAllowed( 'root', 'Article', 'edit' ) );
        $this->assertTrue( $acl->isAllowed( 'editor', 'Article', 'edit' ) );
    }

    public function testAllowFive()
    {
        $adapter = new \Miao\Acl\Adapter\Standart();
        $acl = new \Miao\Acl( $adapter );

        $acl->addGroup( 'root' );
        $acl->addResource( 'Article' );

        $this->assertFalse( $acl->isAllowed( 'root', 'Article' ) );
        $acl->allow( '*', '*' );
        $this->assertTrue( $acl->isAllowed( 'root', 'Article' ) );
    }

    public function testAllowSix()
    {
        $adapter = new \Miao\Acl\Adapter\Standart();
        $acl = new \Miao\Acl( $adapter );

        $acl->addGroup( 'root' );
        $acl->addResource( 'Article' );

        $acl->allow( 'root', '*' );
        $this->assertTrue( $acl->isAllowed( 'root', 'Article' ) );
    }

    public function testAllowSeven()
    {
        $adapter = new \Miao\Acl\Adapter\Standart();
        $acl = new \Miao\Acl( $adapter );

        $acl->addGroup( 'guest' );
        $acl->addResource( 'Article' );

        $acl->allow( '*', 'Article' );
        $this->assertTrue( $acl->isAllowed( 'guest', 'Article' ) );
    }

    public function testAllowEight()
    {
        $adapter = new \Miao\Acl\Adapter\Standart();
        $acl = new \Miao\Acl( $adapter );

        $acl->addGroup( 'root' );
        $acl->addResource( 'Article' );
        $acl->addResource( 'Photo' );

        $acl->allow( 'root', '*' );
        $acl->allow( '*', 'Article' );

        $this->assertTrue( $acl->isAllowed( 'root', 'Photo' ) );
        $this->assertTrue( $acl->isAllowed( 'root', 'Article' ) );
    }

    public function testAllowExOne()
    {
        $exceptionName = 'Miao_Acl_Exception';
        $this->setExpectedException( $exceptionName );

        $adapter = new \Miao\Acl\Adapter\Standart();
        $acl = new \Miao\Acl( $adapter );

        $acl->addGroup( 'root' );
        $acl->addResource( 'Article' );

        $acl->allow( 'guest', 'Article' );
    }
}