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

	/**
	 * @dataProvider dataProviderTestMakeUrl
	 */
	public function testMakeUrl( array $config, array $params, $actual, $exceptionName = '' )
	{
		if ( $exceptionName )
		{
			$this->setExpectedException( $exceptionName );
		}
		$route = Miao_Router_Rule::factory( $config );
		$expected = $route->makeUrl( $params );
		$this->assertEquals( $expected, $actual );
	}

    public function dataProviderTestMakeUrl()
	{
		$data = array();

		$config = array(
			'prefix' => 'Miao_TestOffice',
			'type' => Miao_Router_Rule::TYPE_VIEW,
			'name' => 'Article_Item',
			'rule' => '/article/:id',
			'validators' => array() );
		$data[] = array( $config, array( 'id' => 123 ), 'article/123' );

		$config = array(
			'prefix' => 'Miao_TestOffice',
			'type' => Miao_Router_Rule::TYPE_VIEW,
			'name' => 'Article_Item',
			'rule' => '/article/:id',
			'validators' => array( array( 'id' => 'id', 'type' => 'Numeric' ) ) );
		$data[] = array( $config, array( 'id' => 123 ), 'article/123' );

		$data[] = array(
			$config,
			array( 'id' => 'not numeric' ),
			'',
			'Miao_Router_Rule_Exception' );

		$config = array(
			'prefix' => 'Miao_TestOffice',
			'type' => Miao_Router_Rule::TYPE_VIEW,
			'name' => 'Article_Item',
			'rule' => '/article/:section',
			'validators' => array(
				array(
					'id' => 'section',
					'type' => 'In',
					'variants' => 'lifestyle,finance' ) ) );
		$data[] = array(
			$config,
			array( 'section' => '' ),
			'',
			'Miao_Router_Rule_Exception' );
		$data[] = array(
			$config,
			array( 'section' => 'finance' ),
			'article/finance' );
		$data[] = array(
			$config,
			array( 'section' => 'focus' ),
			'',
			'Miao_Router_Rule_Exception' );

		$config = array(
			'prefix' => 'Miao_TestOffice',
			'type' => Miao_Router_Rule::TYPE_VIEW,
			'name' => 'Article_Item',
			'rule' => '/article/:section',
			'validators' => array(
				array(
					'id' => 'section',
					'type' => 'In',
					'variants' => 'lifestyle,finance' ),
				array(
					'id' => 'section2',
					'type' => 'In',
					'variants' => 'lifestyle,finance' ) ) );
		$data[] = array(
			$config,
			array( 'section' => '' ),
			'',
			'Miao_Router_Rule_Exception' );

		return $data;
	}

    /**
	 * @dataProvider dataProviderTestMakeRewrite
	 */
	public function testMakeRewrite( array $config, $actual )
	{
       // try
        {
            $route = Miao_Router_Rule::factory( $config );
            $expected = $route->makeRewrite();
            $this->assertEquals( $actual, $expected );
        }
        //catch (Exception $e)
        {

        }
	}

    public function dataProviderTestMakeRewrite()
	{
		$data = array();

		$config = array(
			'prefix' => 'Miao_TestOffice2',
			'type' => Miao_Router_Rule::TYPE_VIEW,
			'name' => 'Article_Item',
            'desc' => 'Article item page. Some notes.',
			'rule' => '/article/:id',
			'validators' => array() );
		$data[] = array( $config, '# view:Article_Item Article item page. Some notes.' . "\n" . 'RewriteRule ^article/([^/]+)$ index.php?id=$1&_view=Article_Item [L,QSA]' );

		$config = array(
			'prefix' => 'Miao_TestOffice',
			'type' => Miao_Router_Rule::TYPE_VIEW,
			'name' => 'Article_Item',
			'rule' => '/article/:id',
			'validators' => array( array( 'id' => 'id', 'type' => 'Numeric' ) ) );
		$data[] = array( $config, '# view:Article_Item' . "\n" . 'RewriteRule ^article/([0-9]+)$ index.php?id=$1&_view=Article_Item [L,QSA]' );

		$config = array(
			'prefix' => 'Miao_TestOffice',
			'type' => Miao_Router_Rule::TYPE_ACTION,
			'name' => 'Article_Item',
			'rule' => '/article/:section',
			'validators' => array(
				array(
					'id' => 'section',
					'type' => 'In',
					'variants' => 'lifestyle,finance' ) ) );

		$data[] = array( $config, '# action:Article_Item' . "\n" . 'RewriteRule ^article/(lifestyle|finance)$ index.php?section=$1&_action=Article_Item [L,QSA]' );

        $config = array(
			'prefix' => 'Miao_TestOffice',
			'type' => Miao_Router_Rule::TYPE_VIEW,
			'name' => 'Article_Item',

			'rule' => '/:page/:id/:part/:user/:mode/:param',
            'validators' => array(
                    array( 'id' => 'id', 'type' => 'Numeric' )
                    , array( 'id' => 'part', 'type' => 'Numeric', 'max' => 5, 'min' => 0 )
                    , array( 'id' => 'user', 'type' => 'Numeric', 'min' => 32, 'max' => 32 )
                    , array( 'id' => 'page', 'type' => 'Numeric', 'min' => 1 )
                    , array( 'id' => 'mode', 'type' => 'Numeric', 'min' => 2 )
                    , array( 'id' => 'param', 'type' => 'Numeric', 'min' => 3, 'max' => 5 )

                )
            );
		$data[] = array( $config, '# view:Article_Item' . "\n" . 'RewriteRule ^([0-9]+)/([0-9]+)/([0-9]+)/([0-9]{32})/([0-9]{2,})/([0-9]{3,5})$ index.php?page=$1&id=$2&part=$3&user=$4&mode=$5&param=$6&_view=Article_Item [L,QSA]' );

		return $data;
	}
}