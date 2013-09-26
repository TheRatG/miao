<?php
/**
 * @author vpak
 * @date 2013-09-26 10:08:20
 */

namespace Miao\Acl\Adapter;

class StandartTest extends \PHPUnit_Framework_TestCase
{
    public function testLoadData()
    {
        $data = array(
            'group' => array(
                'root',
                'guest'
            ),
            'resource' => array(
                "Article",
                "Photo"
            ),
            'allow' => array(
                array(
                    'group' => '*',
                    'resource' => '*'
                )
            ),
            'deny' => array(
                array(
                    'group' => 'guest',
                    'resource' => 'Photo',
                    'privileges' => array(
                        'edit'
                    )
                )
            )
        );

        $adapter = new \Miao\Acl\Adapter\Standart();
        $adapter->loadConfig( $data );

        $this->assertTrue( $adapter->isAllowed( 'root', 'Article' ) );
        $this->assertTrue( $adapter->isAllowed( 'root', 'Article', 'view' ) );
        $this->assertTrue( $adapter->isAllowed( 'root', 'Article', 'edit' ) );
        $this->assertTrue( $adapter->isAllowed( 'root', 'Photo' ) );

        $this->assertTrue( $adapter->isAllowed( 'guest', 'Article' ) );
        $this->assertTrue( $adapter->isAllowed( 'guest', 'Article', 'view' ) );
        $this->assertTrue( $adapter->isAllowed( 'guest', 'Photo' ) );
        $this->assertFalse( $adapter->isAllowed( 'guest', 'Photo', 'edit' ) );
    }

    public function testMain()
    {
        $adapter = new Miao_Acl_Adapter_Default();
        $acl = new Miao_Acl( $adapter );

        $acl->addGroup( 'root' );
        $acl->addGroup( 'guest' );
        $acl->addGroup( 'editor' );

        $acl->addResource( 'Article' );
        $acl->addResource( 'Photo' );

        $acl->allow( 'root' );
        $acl->allow(
            'guest', 'Article', array(
                                     'view'
                                )
        );
        $acl->allow(
            'guest', 'Photo', array(
                                  'view'
                             )
        );

        $acl->allow( 'editor', 'Article' );
        $acl->allow(
            'editor', 'Photo', array(
                                   'view',
                                   'edit'
                              )
        );

        $this->assertTrue( $acl->isAllowed( 'root', 'Photo' ) );
        $this->assertTrue( $acl->isAllowed( 'root', 'Article', 'view' ) );
        $this->assertTrue( $acl->isAllowed( 'root', 'Article', 'edit' ) );

        $this->assertTrue( $acl->isAllowed( 'guest', 'Article' ) );
        $this->assertTrue( $acl->isAllowed( 'guest', 'Article', 'view' ) );
        $this->assertFalse( $acl->isAllowed( 'guest', 'Article', 'edit' ) );

        $this->assertTrue( $acl->isAllowed( 'editor', 'Article', 'view' ) );
        $this->assertTrue( $acl->isAllowed( 'editor', 'Article', 'edit' ) );
        $this->assertTrue( $acl->isAllowed( 'editor', 'Photo', 'view' ) );
        $this->assertFalse( $acl->isAllowed( 'editor', 'Photo', 'del' ) );

        $acl->deny( 'guest', 'Article' );
        $this->assertFalse( $acl->isAllowed( 'guest', 'Article', 'view' ) );

        $this->assertTrue( $acl->isAllowed( 'root', 'Article', 'archive' ) );
        $acl->deny(
            null, 'Article', array(
                                  'archive'
                             )
        );
        $this->assertFalse( $acl->isAllowed( 'root', 'Article', 'archive' ) );
    }
}