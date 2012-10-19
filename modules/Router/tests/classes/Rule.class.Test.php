<?php
class Miao_Router_Rule_Test extends PHPUnit_Framework_TestCase
{

	/**
	 * @dataProvider dataProviderTestFactory
	 */
	public function testFactory( $config, $exceptionName = '' )
	{
		if ( $exceptionName )
		{
			$this->setExpectedException( $exceptionName );
		}

		$rule = Miao_Router_Rule::factory( $config );
	}

	public function dataProviderTestFactory()
	{
		$data = array();

		$data[] = array(
			array(
				'rule' => '/news/:id',
				'type' => 'News_Item',
				'validators' => array(
					array( 'type' => 'numeric', 'id' => 'id' ) ) ),
			'Miao_Router_Exception' );

		$data[] = array(
			array(
				'rule' => '/news/:id',
				'type' => Miao_Router_Rule::TYPE_VIEW,
				'validators' => array(
					array( 'type' => 'numeric', 'id' => 'id' ) ) ),
			'Miao_Router_Exception' );

		$data[] = array(
			array(
				'rule' => '/news/:id',
				'type' => Miao_Router_Rule::TYPE_VIEW,
				'name' => 'News_Item',
				'validators' => array() ) );

		$data[] = array(
			array(
				'rule' => '/news/:id',
				'type' => Miao_Router_Rule::TYPE_VIEW,
				'name' => 'News_Item',
				'validators' => array(
					array( 'type' => 'numeric', 'id' => 'id' ) ) ) );

		return $data;
	}

	/**
	 *
	 * @dataProvider dataProviderTestConstruct
	 */
	public function testConstruct( $prefix, $type, $name, $rule, $validators, $exceptionName = '' )
	{
		if ( !empty( $exceptionName ) )
		{
			$this->setExpectedException( $exceptionName );
		}

		$route = new Miao_Router_Rule( $prefix, $type, $name, $rule, $validators );

		$this->assertEquals( $route->getType(), $type );
		$this->assertEquals( $route->getName(), $name );
	}

	public function dataProviderTestConstruct()
	{
		$data = array();

		$data[] = array(
			'Miao_TestOffice',
			Miao_Router_Rule::TYPE_VIEW,
			'Article_Item',
			'/article/:id',
			array() );

		//check type
		$data[] = array(
			'Miao_TestOffice',
			'viiiieeev',
			'Article_Item',
			'/article/:id',
			array(),
			'Miao_Router_Rule_Exception' );

		return $data;
	}

	/**
	 * @dataProvider dataProviderTestMatch
	 */
	public function testMatch( $config, $uri, $actual )
	{
		$route = Miao_Router_Rule::factory( $config );
		$expected = $route->match( $uri );

		$this->assertEquals( $expected, $actual );
	}

	public function dataProviderTestMatch()
	{
		$data = array();

		$data[] = array(
			array(
				'prefix' => 'Miao_TestOffice',
				'type' => Miao_Router_Rule::TYPE_VIEW,
				'name' => 'Article_Item',
				'rule' => '/article/:id',
				'validators' => array() ),
			'/news/123',
			false );

		$data[] = array(
			array(
				'prefix' => 'Miao_TestOffice',
				'type' => Miao_Router_Rule::TYPE_VIEW,
				'name' => 'Article_Item',
				'rule' => '/article/:id',
				'validators' => array(
					array( 'id' => 'id', 'type' => 'Numeric' ) ) ),
			'/article/123',
			array( '_view' => 'Article_Item', 'id' => '123' ) );

		return $data;
	}
}