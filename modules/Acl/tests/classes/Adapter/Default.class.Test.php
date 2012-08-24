<?php
class Miao_Acl_Adapter_Default_Test extends PHPUnit_Framework_TestCase
{

	public function testLoadData()
	{
		$data = array(
			'group' => array( 'root', 'guest' ),
			'resource' => array( "Article", "Foto" ),
			'allow' => array( array( 'group' => '*', 'resource' => '*' ) ),
			'deny' => array( array(
				'group' => 'guest',
				'resource' => 'Foto',
				'privileges' => array( 'edit' ) ) ) );

		$adapter = new Miao_Acl_Adapter_Default();
		$adapter->loadConfig( $data );

		$this->assertTrue( $adapter->isAllowed( 'root', 'Article' ) );
		$this->assertTrue( $adapter->isAllowed( 'root', 'Article', 'view' ) );
		$this->assertTrue( $adapter->isAllowed( 'root', 'Article', 'edit' ) );
		$this->assertTrue( $adapter->isAllowed( 'root', 'Foto' ) );

		$this->assertTrue( $adapter->isAllowed( 'guest', 'Article' ) );
		$this->assertTrue( $adapter->isAllowed( 'guest', 'Article', 'view' ) );
		$this->assertTrue( $adapter->isAllowed( 'guest', 'Foto' ) );
		$this->assertFalse( $adapter->isAllowed( 'guest', 'Foto', 'edit' ) );
	}

	public function testAllowOne()
	{
		$adapter = new Miao_Acl_Adapter_Default();
		$acl = new Miao_Acl( $adapter );

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
		$adapter = new Miao_Acl_Adapter_Default();
		$acl = new Miao_Acl( $adapter );

		$acl->addGroup( 'root' );
		$acl->addGroup( 'guest' );
		$acl->addResource( 'Article' );

		$acl->allow( 'root' );

		$this->assertTrue( $acl->isAllowed( 'root', 'Article' ) );
		$this->assertFalse( $acl->isAllowed( 'guest', 'Article' ) );
	}

	public function testAllowThree()
	{
		$adapter = new Miao_Acl_Adapter_Default();
		$acl = new Miao_Acl( $adapter );

		$acl->addGroup( 'root' );
		$acl->addResource( 'Article' );

		$acl->allow( 'root' );

		$this->assertTrue( $acl->isAllowed( 'root', 'Article' ) );
		$this->assertFalse( $acl->isAllowed( 'guest', 'Article' ) );
	}

	public function testAllowFour()
	{
		$adapter = new Miao_Acl_Adapter_Default();
		$acl = new Miao_Acl( $adapter );

		$acl->addGroup( 'root' );
		$acl->addGroup( 'guest' );
		$acl->addGroup( 'editor' );
		$acl->addResource( 'Article' );

		$acl->allow( 'root', 'Article' );
		$acl->allow( 'guest', 'Article', array( 'view' ) );
		$acl->allow( 'editor', 'Article', array( 'view', 'edit' ) );

		$this->assertTrue( $acl->isAllowed( 'guest', 'Article' ) );
		$this->assertTrue( $acl->isAllowed( 'guest', 'Article', 'view' ) );
		$this->assertFalse( $acl->isAllowed( 'guest', 'Article', 'edit' ) );
		$this->assertTrue( $acl->isAllowed( 'root', 'Article', 'edit' ) );
		$this->assertTrue( $acl->isAllowed( 'editor', 'Article', 'edit' ) );
	}

	public function testAllowFive()
	{
		$adapter = new Miao_Acl_Adapter_Default();
		$acl = new Miao_Acl( $adapter );

		$acl->addGroup( 'root' );
		$acl->addResource( 'Article' );

		$this->assertFalse( $acl->isAllowed( 'root', 'Article' ) );
		$acl->allow( '*', '*' );
		$this->assertTrue( $acl->isAllowed( 'root', 'Article' ) );
	}

	public function testAllowSix()
	{
		$adapter = new Miao_Acl_Adapter_Default();
		$acl = new Miao_Acl( $adapter );

		$acl->addGroup( 'root' );
		$acl->addResource( 'Article' );

		$acl->allow( 'root', '*' );
		$this->assertTrue( $acl->isAllowed( 'root', 'Article' ) );
	}

	public function testAllowSeven()
	{
		$adapter = new Miao_Acl_Adapter_Default();
		$acl = new Miao_Acl( $adapter );

		$acl->addGroup( 'guest' );
		$acl->addResource( 'Article' );

		$acl->allow( '*', 'Article' );
		$this->assertTrue( $acl->isAllowed( 'guest', 'Article' ) );
	}

	public function testAllowEight()
	{
		$adapter = new Miao_Acl_Adapter_Default();
		$acl = new Miao_Acl( $adapter );

		$acl->addGroup( 'root' );
		$acl->addResource( 'Article' );
		$acl->addResource( 'Foto' );

		$acl->allow( 'root', '*' );
		$acl->allow( '*', 'Article' );

		$this->assertTrue( $acl->isAllowed( 'root', 'Foto' ) );
		$this->assertTrue( $acl->isAllowed( 'root', 'Article' ) );
	}


	public function testAllowExOne()
	{
		$exceptionName = 'Miao_Acl_Exception';
		$this->setExpectedException( $exceptionName );

		$adapter = new Miao_Acl_Adapter_Default();
		$acl = new Miao_Acl( $adapter );

		$acl->addGroup( 'root' );
		$acl->addResource( 'Article' );

		$acl->allow( 'guest', 'Article' );
	}

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
		$acl->deny( 'root', 'Foto', array( 'edit' ) );

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

		$acl->allow( 'root', 'Foto', array( 'view' ) );
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

		$acl->deny( null, 'Article', array( 'archive' ) );
		$this->assertFalse( $acl->isAllowed( 'root', 'Article', 'archive' ) );
	}

	public function testMain()
	{
		$adapter = new Miao_Acl_Adapter_Default();
		$acl = new Miao_Acl( $adapter );

		$acl->addGroup( 'root' );
		$acl->addGroup( 'guest' );
		$acl->addGroup( 'editor' );

		$acl->addResource( 'Article' );
		$acl->addResource( 'Foto' );

		$acl->allow( 'root' );
		$acl->allow( 'guest', 'Article', array( 'view' ) );
		$acl->allow( 'guest', 'Foto', array( 'view' ) );

		$acl->allow( 'editor', 'Article' );
		$acl->allow( 'editor', 'Foto', array( 'view', 'edit' ) );

		$this->assertTrue( $acl->isAllowed( 'root', 'Foto' ) );
		$this->assertTrue( $acl->isAllowed( 'root', 'Article', 'view' ) );
		$this->assertTrue( $acl->isAllowed( 'root', 'Article', 'edit' ) );

		$this->assertTrue( $acl->isAllowed( 'guest', 'Article' ) );
		$this->assertTrue( $acl->isAllowed( 'guest', 'Article', 'view' ) );
		$this->assertFalse( $acl->isAllowed( 'guest', 'Article', 'edit' ) );

		$this->assertTrue( $acl->isAllowed( 'editor', 'Article', 'view' ) );
		$this->assertTrue( $acl->isAllowed( 'editor', 'Article', 'edit' ) );
		$this->assertTrue( $acl->isAllowed( 'editor', 'Foto', 'view' ) );
		$this->assertFalse( $acl->isAllowed( 'editor', 'Foto', 'del' ) );

		$acl->deny( 'guest', 'Article' );
		$this->assertFalse( $acl->isAllowed( 'guest', 'Article', 'view' ) );

		$this->assertTrue( $acl->isAllowed( 'root', 'Article', 'archive' ) );
		$acl->deny( null, 'Article', array( 'archive' ) );
		$this->assertFalse( $acl->isAllowed( 'root', 'Article', 'archive' ) );
	}
}