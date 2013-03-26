<?php
class Miao_Acl_Adapter_Deny_Test extends PHPUnit_Framework_TestCase
{

	public function testDenyOne()
	{
		$adapter = new Miao_Acl_Adapter_Default();
		$acl = new Miao_Acl( $adapter );

		$acl->addGroup( 'root' );
		$acl->addResource( 'Article' );
		$acl->addResource( 'Foto' );

		$acl->allow( 'root' );
		$acl->deny( 'root', 'Foto' );

		$this->assertTrue( $acl->isAllowed( 'root', 'Article' ) );
		$this->assertFalse( $acl->isAllowed( 'root', 'Foto' ) );
	}

	public function testDenyTwo()
	{
		$adapter = new Miao_Acl_Adapter_Default();
		$acl = new Miao_Acl( $adapter );

		$acl->addGroup( 'root' );
		$acl->addResource( 'Article' );
		$acl->addResource( 'Foto' );

		$acl->allow( 'root' );
		$acl->deny( 'root', 'Foto', array(
			'edit' ) );

		$this->assertTrue( $acl->isAllowed( 'root', 'Article' ) );
		$this->assertTrue( $acl->isAllowed( 'root', 'Foto' ) );
		$this->assertFalse( $acl->isAllowed( 'root', 'Foto', 'edit' ) );
	}

	public function testDenyThree()
	{
		$adapter = new Miao_Acl_Adapter_Default();
		$acl = new Miao_Acl( $adapter );

		$acl->addGroup( 'root' );
		$acl->addResource( 'Foto' );

		$acl->allow( 'root', 'Foto', array(
			'view' ) );
		$this->assertTrue( $acl->isAllowed( 'root', 'Foto' ) );

		$acl->deny( 'root', 'Foto' );
		$this->assertFalse( $acl->isAllowed( 'root', 'Foto', 'view' ) );
	}

	public function testDenyFour()
	{
		$adapter = new Miao_Acl_Adapter_Default();
		$acl = new Miao_Acl( $adapter );

		$acl->addGroup( 'root' );
		$acl->addGroup( 'guest' );
		$acl->addResource( 'Article' );

		$acl->allow( 'root' );

		$acl->deny( null, 'Article', array(
			'archive' ) );
		$this->assertFalse( $acl->isAllowed( 'root', 'Article', 'archive' ) );
	}

	/**
	 * Deny all and allow some resource
	 */
	public function testDenyFive()
	{
		$adapter = new Miao_Acl_Adapter_Default();
		$acl = new Miao_Acl( $adapter );

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