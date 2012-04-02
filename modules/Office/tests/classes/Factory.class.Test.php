<?php
class Miao_Office_Factory_Test extends PHPUnit_Framework_TestCase
{
	/**
	 *
	 * @dataProvider providerTestGetClassName
	 * @param array $config
	 * @param unknown_type $type
	 * @param unknown_type $name
	 * @param unknown_type $prefix
	 * @param unknown_type $expected
	 * @param unknown_type $exceptionName
	 */
	public function testGetClassName( array $config, $type, $name, $prefix, $expected, $exceptionName = '' )
	{
		if ( $exceptionName )
		{
			$this->setExpectedException( $exceptionName );
		}
		$obj = new Miao_Office_Factory( $config );
		$actual = $obj->getClassName( $type, $name, $prefix );

		$this->assertEquals( $expected, $actual );
	}

	public function providerTestGetClassName()
	{
		$data = array();

		$this->setUp();

		$config = array( 'defaultPrefix' => 'Teleprog_Office' );

		$data[] = array(
			$config,
			Miao_Office::TYPE_ACTION,
			'Article_Del',
			'',
			'Teleprog_Office_Action_Article_Del' );
		$data[] = array(
			$config,
			Miao_Office::TYPE_ACTION,
			'Article_Add',
			'',
			'Teleprog_Office_Action_Article_Add' );
		$data[] = array(
			$config,
			Miao_Office::TYPE_VIEW,
			'Main',
			'',
			'Teleprog_Office_View_Main' );
		$data[] = array(
			$config,
			Miao_Office::TYPE_VIEW,
			'Article_List',
			'',
			'Teleprog_Office_View_Article_List' );

		return $data;
	}
	/**
	 *
	 * @dataProvider providerTestGetClassList
	 * @param array $config
	 * @param array $requestParams
	 * @param unknown_type $excpected
	 * @param unknown_type $exceptionName
	 */
	public function testGetClassList( array $config, array $requestParams, array $default, $expected, $exceptionName = '' )
	{
		if ( $exceptionName )
		{
			$this->setExpectedException( $exceptionName );
		}

		$obj = new Miao_Office_Factory( $config );
		$actual = $obj->getClassList( $requestParams, $default );

		$this->assertEquals( $expected, $actual );
	}

	public function providerTestGetClassList()
	{
		$data = array();

		$config = array( 'defaultPrefix' => 'Teleprog_Office' );
		$data[] = array(
			$config,
			array( '_view' => 'Main' ),
			array(),
			array(
				'resource' => 'Miao_Office_Resource_Get',
				'view' => 'Teleprog_Office_View_Main',
				'viewBlock' => null,
				'action' => null ),
			'' );

		$config = array( 'defaultPrefix' => 'Miao_BackOffice' );
		$data[] = array(
			$config,
			array( '_view' => 'Main' ),
			array(),
			array(
				'resource' => 'Miao_Office_Resource_Get',
				'view' => 'Miao_BackOffice_View_Main',
				'viewBlock' => null,
				'action' => null ),
			'' );

		$config = array(
			'defaultPrefix' => 'Miao_BackOffice',
			'requestMethod' => 'post' );
		$data[] = array(
			$config,
			array( '_action' => 'Article_Add' ),
			array(),
			array(
				'resource' => 'Miao_Office_Resource_Post',
				'view' => '',
				'viewBlock' => '',
				'action' => 'Miao_BackOffice_Action_Article_Add' ) );

		//test exception
		//		$config = array(
		//			'defaultPrefix' => 'Miao_BackOffice',
		//			'requestMethod' => 'post' );
		//		$data[] = array(
		//			$config,
		//			array( '_view' => 'Main' ),
		//			array(),
		//			array(
		//				'resource' => 'Miao_Office_Resource_Post',
		//				'view' => 'Miao_BackOffice_View_Main',
		//				'viewBlock' => null,
		//				'action' => null ),
		//			'Miao_Office_Factory_Exception' );


		// test default
		$config = array(
			'defaultPrefix' => 'Miao_BackOffice',
			'requestMethod' => 'get' );
		$data[] = array(
			$config,
			array(),
			array( '_view' => 'Main' ),
			array(
				'resource' => 'Miao_Office_Resource_Get',
				'view' => 'Miao_BackOffice_View_Main',
				'viewBlock' => null,
				'action' => null ) );

		$config = array(
			'defaultPrefix' => 'Miao_BackOffice',
			'requestMethod' => 'get' );
		$data[] = array(
			$config,
			array( '_view' => 'Main2' ),
			array( '_view' => 'Main' ),
			array(
				'resource' => 'Miao_Office_Resource_Get',
				'view' => 'Miao_BackOffice_View_Main2',
				'viewBlock' => null,
				'action' => null ) );

		$config = array(
			'defaultPrefix' => 'Miao_BackOffice',
			'requestMethod' => 'get' );
		$data[] = array(
			$config,
			array( '_view' => 'Main2' ),
			array( '_action' => 'Main' ),
			array(
				'resource' => 'Miao_Office_Resource_Get',
				'view' => 'Miao_BackOffice_View_Main2',
				'viewBlock' => null,
				'action' => null ) );

		$config = array(
			'defaultPrefix' => 'Miao_BackOffice',
			'requestMethod' => 'post' );
		$data[] = array(
			$config,
			array( '_view' => '' ),
			array( '_action' => 'Main' ),
			array(
				'resource' => 'Miao_Office_Resource_Post',
				'view' => null,
				'viewBlock' => null,
				'action' => 'Miao_BackOffice_Action_Main' ) );

		$config = array(
			'defaultPrefix' => 'Miao_BackOffice',
			'requestMethod' => 'get' );
		$data[] = array(
			$config,
			array(),
			array( '_viewBlock' => 'Main' ),
			array(
				'resource' => 'Miao_Office_Resource_Get',
				'view' => null,
				'viewBlock' => 'Miao_BackOffice_ViewBlock_Main',
				'action' => null ) );

		return $data;
	}

	/**
	 *
	 * @dataProvider providerTestGetOffice
	 * @param array $config
	 * @param array $params
	 * @param array $expected
	 * @param unknown_type $exceptionName
	 */
	public function atestGetOffice( array $config, array $params, array $expected, $exceptionName = '' )
	{
		if ( $exceptionName )
		{
			$this->setExpectedException( $exceptionName );
		}

		$obj = new Miao_Office_Factory( $config );
		$frontOffice = $obj->getOffice( $params );

		$types = Miao_Office::getTypesObject();
		foreach ( $types as $type )
		{
			$this->assertEquals( $expected[ $type ],
				get_class( $frontOffice->getObject( $type ) ) );
		}
	}

	public function providerTestGetOffice()
	{
		$data = array();

		$types = Miao_Office::getTypesObject();

		$config = array( 'defaultPrefix' => 'Teleprog_Office' );
		$params = array( '_view' => 'Main' );
		$types[ Miao_Office::TYPE_VIEW ] = 'Teleprog_Office_View_Main';
		$types[ Miao_Office::TYPE_RESOURCE ] = 'Mia_Office_Resource_Get';
		$types[ Miao_Office::TYPE_FACTORY ] = 'Mia_Office_Factory';
		$types[ Miao_Office::TYPE_HEADER ] = 'Mia_Office_Header';

		$data[] = array( $config, $data, $types );

		return $data;
	}
}