<?php
/**
 * <?php
 * // #1 преобразование url в параметры для Miao_Office
 * $config = array();
 * $router = new Miao_Router( $config );
 * $params = $router->getCurrentRoute();
 * $params = $router->getOfficeParams( '/news/123' );
 *
 * // #2 генерация ссылок
 * $url = $router->view( 'Artile_Item',
 * 		array( 'id' => '123456', 'dd' => '17', 'mm' => '10', 'yyyy' => '2012' ) );
 * $url = $router->action( 'Auth', array( 'subaction' => 'logout' ) );
 * $url = $router->viewBlock( 'Auth', array( 'subaction' => 'logout' ) );
 *
 * // #3 валидация параметров
 *
 * // #4 генерация правил для .htaccess
 * $router->toHtaccess();
 *
 * // #5 генерация правил для .nginx
 * $router->toNginx();
 *
 * @author vpak
 */
class Miao_Router_Test extends PHPUnit_Framework_TestCase
{

	/**
	 * @dataProvider dataProviderTestView
	 */
	public function testView( $config, $funcName, $param, $actual, $exceptionName = '' )
	{
		$router = Miao_Router::factory( $config );
		$expected = call_user_func_array( array(
			$router,
			$funcName ), $param );
		$this->assertEquals( $expected, $actual );
	}

	public function dataProviderTestView()
	{
		$data = array();

		$config = array(
			'main' => 'Main',
			'error' => '404',
			'defaultPrefix' => 'Test_Office',
			'route' => array(
				array(
					'view' => 'Article_Item',
					'rule' => 'article/:id',
					'validator' => array(
						'type' => 'numeric',
						'param' => 'id' ) ) ) );

		$data[] = array(
			$config,
			'view',
			array(
				'Article_Item',
				array(
					'id' => '123' ) ),
			'/article/123' );
		$data[] = array(
			$config,
			'view',
			array(
				'Article_Item',
				array(
					'id' => '123',
					'flag' => '1' ) ),
			'/article/123?flag=1' );
		$data[] = array(
			$config,
			'view',
			array(
				'Article_Item',
				array(
					'id' => '123',
					'flag' => '1',
					'map' => 'google' ) ),
			'/article/123?flag=1&map=google' );

		$config = array(
			'main' => 'Main',
			'error' => '404',
			'defaultPrefix' => 'Test_Office',
			'route' => array(
				array(
					'view' => 'Import',
					'rule' => 'import',
					'validator' => array() ) ) );
		$data[] = array(
			$config,
			'view',
			array(
				'Import',
				array() ),
			'/import' );

		return $data;
	}

	public function testConstruct()
	{
		$obj = new Miao_Router( 'Main', '404', array() );
		$this->assertTrue( $obj instanceof Miao_Router );
	}

	/**
	 * @dataProvider dataProviderTestGetRoute
	 */
	public function testGetRoute( $config, $url, $actual, $exceptionName = '' )
	{
		if ( $exceptionName )
		{
			$this->setExpectedException( $exceptionName );
		}
		$router = Miao_Router::factory( $config );
		$expected = $router->route( $url );

		$this->assertEquals( $expected, $actual );
	}

	public function dataProviderTestGetRoute()
	{
		$data = array();

		$config = array(
			'main' => 'Main',
			'error' => '404',
			'defaultPrefix' => 'Daily_FrontOffice',
			'route' => array(
				array(
					'view' => 'Import',
					'rule' => 'import',
					'validator' => array() ) ) );
		$data[] = array(
			$config,
			'/import',
			array(
				'_view' => 'Import',
				'_prefix' => 'Daily_FrontOffice' ) );

		$config = array(
			'main' => 'Main',
			'defaultPrefix' => '',
			'error' => '404',
			'route' => array(
				array(
					'rule' => '/news/:id',
					'view' => 'News_Item',
					'validator' => array(
						'type' => 'numeric',
						'param' => 'id' ) ) ) );
		$data[] = array(
			$config,
			'/news/123',
			array(
				'_view' => 'News_Item',
				'id' => '123' ) );

		$config = array(
			'main' => 'Main',
			'defaultPrefix' => '',
			'error' => '404',
			'route' => array(
				array(
					'rule' => '/news/:page',
					'view' => 'News_List',
					'validator' => array(
						'type' => 'Regexp',
						'param' => 'page',
						'pattern' => 'p([0-9]+)' ) ) ) );
		$data[] = array(
			$config,
			'/news/p1',
			array(
				'_view' => 'News_List',
				'page' => 'p1' ) );

		$config = array(
			'main' => 'Main',
			'defaultPrefix' => 'Daily_FrontOffice',
			'error' => '404',
			'route' => array(
				array(
					'rule' => '/news/:id',
					'view' => 'News_Item',
					'validator' => array(
						'type' => 'numeric',
						'param' => 'id' ) ) ) );
		$data[] = array(
			$config,
			'/news/123',
			array(
				'_view' => 'News_Item',
				'id' => '123',
				'_prefix' => 'Daily_FrontOffice' ) );
		$data[] = array(
			$config,
			'/',
			array(
				'_view' => 'Main',
				'_prefix' => 'Daily_FrontOffice' ) );

		$config = array(
			'main' => 'Main',
			'defaultPrefix' => 'Daily_FrontOffice',
			'error' => '404',
			'route' => array(
				array(
					'rule' => '/news/:section/:id',
					'view' => 'News_Item',
					'validator' => array(
						array(
							'type' => 'in',
							'param' => 'section',
							'variants' => 'social,finance' ),
						array(
							'type' => 'numeric',
							'param' => 'id' ) ) ) ) );
		$data[] = array(
			$config,
			'/news/finance/123',
			array(
				'_view' => 'News_Item',
				'section' => 'finance',
				'id' => '123',
				'_prefix' => 'Daily_FrontOffice' ) );

		$config = array(
			'main' => 'Main',
			'defaultPrefix' => 'Daily_FrontOffice',
			'error' => '404',
			'route' => array(
				array(
					'rule' => '/:pubDate',
					'view' => 'News_Item',
					'validator' => array(
						array(
							'type' => 'regexp',
							'param' => 'pubDate',
							'pattern' => '\d{4}/\d{2}/\d{2}' ) ) ) ) );

		$data[] = array(
			$config,
			'/2012/10/29',
			array(),
			'Miao_Router_Exception' );

		$config[ 'route' ][ 0 ][ 'validator' ][ 0 ][ 'slash' ] = 2;
		$data[] = array(
			$config,
			'/2012/10/29',
			array(
				'_view' => 'News_Item',
				'pubDate' => '2012/10/29',
				'_prefix' => 'Daily_FrontOffice' ) );

		return $data;
	}

	/**
	 * @dataProvider dataProviderTestMakeRewriteApache
	 */
	public function testMakeRewriteApache( $config, $actual )
	{
		$router = Miao_Router::factory( $config, true );
		$expected = $router->makeRewrite( 'apache' );

		$this->assertEquals( $actual, $expected );
	}

	public function dataProviderTestMakeRewriteApache()
	{
		$data = array();

		$config = array(
			'main' => 'Main',
			'defaultPrefix' => '',
			'error' => '404',
			'route' => array(
				array(
					'rule' => '/news/:id',
					'view' => 'News_Item',
					'validator' => array(
						'type' => 'numeric',
						'param' => 'id' ) ) ) );
		$data[] = array(
			$config,
			'# view:News_Item' . "\n" . 'RewriteRule ^news/([0-9]+)$ index.php?id=$1&_view=News_Item [L,QSA]' );

		$config = array(
			'main' => 'Main',
			'defaultPrefix' => 'Daily_FrontOffice',
			'error' => '404',
			'route' => array(
				array(
					'rule' => '/news/:section',
					'view' => 'News_List',
					'validator' => array(
						array(
							'type' => 'in',
							'param' => 'section',
							'variants' => 'social,finance' ) ) ),

				array(
					'rule' => '/news/:section/:id3',
					'view' => 'News_Bad_Item2',
					'validator' => array(
						array(
							'type' => 'bad_validator',
							'param' => 'section',
							'variants' => 'social,finance' ),
						array(
							'type' => 'numeric',
							'param' => 'id2' ) ) ),

				array(
					'rule' => '/news/:section/:id',
					'view' => 'News_Item',
					'validator' => array(
						array(
							'type' => 'in',
							'param' => 'section',
							'variants' => 'social,finance' ),
						array(
							'type' => 'numeric',
							'param' => 'id' ) ) ),
				// we want this item to be skipped
				array(
					'rule' => '/news/:section/skip/:id',
					'view' => 'News_Item',
					'norewrite' => 1,
					'validator' => array(
						array(
							'type' => 'in',
							'param' => 'section',
							'variants' => 'social,finance' ),
						array(
							'type' => 'numeric',
							'param' => 'id' ) ) ),
				array(
					'rule' => '/news/:section/:id2',
					'view' => 'News_Bad_Item',
					'validator' => array(
						array(
							'type' => 'bad_validator',
							'param' => 'section',
							'variants' => 'social,finance' ),
						array(
							'type' => 'numeric',
							'param' => 'id2' ) ) ) ) );

		$data[] = array(
			$config,
			'# view:News_List' . "\n" . 'RewriteRule ^news/(social|finance)$ index.php?section=$1&_view=News_List [L,QSA]' . "\n" . '# view:News_Item' . "\n" . 'RewriteRule ^news/(social|finance)/([0-9]+)$ index.php?section=$1&id=$2&_view=News_Item [L,QSA]' . "\n" . '# rule asks to skip it /news/:section/skip/:id' . "\n" . '# error happened while generating rewrite for /news/:section/:id3' . "\n" . '# error happened while generating rewrite for /news/:section/:id2' );

		return $data;
	}

	public function testMakeRewriteBadMode()
	{
		$this->setExpectedException( 'Miao_Router_Rule_Exception' );
		$config = array(
			'main' => 'Main',
			'defaultPrefix' => '',
			'error' => '404',
			'route' => array(
				array(
					'rule' => '/news/:id',
					'view' => 'News_Item',
					'validator' => array(
						'type' => 'numeric',
						'param' => 'id' ) ) ) );

		$router = Miao_Router::factory( $config, true );
		$expected = $router->makeRewrite( 'bad_mode' );
	}

	/**
	 * @dataProvider dataProviderTestMakeRewriteNginx
	 */
	public function testMakeRewriteNginx( $config, $actual )
	{
		$router = Miao_Router::factory( $config, true );
		$expected = $router->makeRewrite( 'nginx' );

		$this->assertEquals( $actual, $expected );
	}

	public function dataProviderTestMakeRewriteNginx()
	{
		$data = array();

		$config = array(
			'main' => 'Main',
			'defaultPrefix' => '',
			'error' => '404',
			'route' => array(
				array(
					'rule' => '/news/:id',
					'view' => 'News_Item',
					'validator' => array(
						'type' => 'numeric',
						'param' => 'id' ) ) ) );
		$data[] = array(
			$config,
			'# view:News_Item' . "\n" . 'rewrite "^/?news/([0-9]+)$" /index.php?id=$1&_view=News_Item break;' );

		$config = array(
			'main' => 'Main',
			'defaultPrefix' => '',
			'error' => '404',
			'route' => array(
				array(
					'rule' => '/news/:page',
					'view' => 'News_List',
					'validator' => array(
						'type' => 'Regexp',
						'param' => 'page',
						'pattern' => 'p([0-9]+)' ) ) ) );
		$data[] = array(
			$config,
			'# view:News_List' . "\n" . 'rewrite "^/?news/p([0-9]+)$" /index.php?page=$1&_view=News_List break;' );

		$config = array(
			'main' => 'Main',
			'defaultPrefix' => 'Daily_FrontOffice',
			'error' => '404',
			'route' => array(
				array(
					'rule' => '/news/:section',
					'view' => 'News_List',
					'desc' => 'custom desc',
					'validator' => array(
						array(
							'type' => 'in',
							'param' => 'section',
							'variants' => 'social,finance' ) ) ),

				array(
					'rule' => '/news/:section/:id3',
					'view' => 'News_Bad_Item2',
					'validator' => array(
						array(
							'type' => 'bad_validator',
							'param' => 'section',
							'variants' => 'social,finance' ),
						array(
							'type' => 'numeric',
							'param' => 'id2' ) ) ),

				array(
					'rule' => '/news/:section/:id',
					'view' => 'News_Item',
					'validator' => array(
						array(
							'type' => 'in',
							'param' => 'section',
							'variants' => 'social,finance' ),
						array(
							'type' => 'numeric',
							'param' => 'id' ) ) ),
				array(
					'rule' => '/news/:section/:id2',
					'view' => 'News_Bad_Item',
					'validator' => array(
						array(
							'type' => 'bad_validator',
							'param' => 'section',
							'variants' => 'social,finance' ),
						array(
							'type' => 'numeric',
							'param' => 'id2' ) ) ),

				array(
					'rule' => '/news/:p1/:p2/:p3/:p4/:p5/:p6/:p7/:p8/:p9/:p10',
					'view' => 'Many_Params',
					'validator' => array() ) ) );

		$data[] = array(
			$config,
			'# view:News_List custom desc' . "\n" . 'rewrite "^/?news/(social|finance)$" /index.php?section=$1&_view=News_List break;' . "\n" . '# view:News_Item' . "\n" . 'rewrite "^/?news/(social|finance)/([0-9]+)$" /index.php?section=$1&id=$2&_view=News_Item break;' . "\n" . '# error happened while generating rewrite for /news/:p1/:p2/:p3/:p4/:p5/:p6/:p7/:p8/:p9/:p10 (too many params)' . "\n" . '# error happened while generating rewrite for /news/:section/:id3' . "\n" . '# error happened while generating rewrite for /news/:section/:id2' );

		return $data;
	}

	/**
	 * Вызов разных типов с одинаковым шаблоном, но разным типом METHOD_REQUEST
	 * @dataProvider dataProviderTestGetRouteWithAddParams
	 */
	public function testGetRouteWithAddParams( $config, $uri, $method, $actual, $exceptionName = '' )
	{
		if ( $exceptionName )
		{
			$this->setExpectedException( $exceptionName );
		}
		$router = Miao_Router::factory( $config );
		$expected = $router->route( $uri, $method );

		$this->assertEquals( $expected, $actual );
	}

	public function dataProviderTestGetRouteWithAddParams()
	{
		$data = array();

		$config = array(
			'main' => 'Main',
			'defaultPrefix' => '',
			'error' => '404',
			'route' => array(
				array(
					'rule' => '/article/edit/:id',
					'view' => 'Article_Edit',
					'validator' => array(
						'type' => 'Numeric',
						'param' => 'id' ) ),
				array(
					'rule' => '/article/edit/:id',
					'action' => 'Article_Edit',
					'method' => 'POST',
					'validator' => array(
						'type' => 'Numeric',
						'param' => 'id' ) ) ) );

		$data[] = array(
			$config,
			'/article/edit/555',
			'GET',
			array(
				'_view' => 'Article_Edit',
				'id' => '555' ) );
		$data[] = array(
			$config,
			'/article/edit/555',
			'POST',
			array(
				'_action' => 'Article_Edit',
				'id' => '555' ) );

		return $data;
	}

	/**
	 * @dataProvider dataProviderTestMakeUrl
	 */
	public function testMakeUrl( $config, $type, $name, array $params, $method, $actual, $exceptionName = '' )
	{
		if ( $exceptionName )
		{
			$this->setExpectedException( $exceptionName );
		}
		$router = Miao_Router::factory( $config, true );
		$expected = $router->makeUrl( $name, $type, $params, $method );

		$this->assertEquals( $actual, $expected );
	}

	public function dataProviderTestMakeUrl()
	{
		$data = array();

		$config = array(
			'main' => 'Main',
			'defaultPrefix' => '',
			'error' => '404',
			'route' => array(
				array(
					'rule' => '/article/edit/:id',
					'view' => 'Article_Edit',
					'validator' => array(
						'type' => 'Numeric',
						'param' => 'id' ) ),
				array(
					'rule' => '/article/edit/:id',
					'action' => 'Article_Edit',
					'method' => 'POST',
					'validator' => array(
						'type' => 'Numeric',
						'param' => 'id' ) ) ) );

		$data[] = array(
			$config,
			Miao_Router_Rule::TYPE_VIEW,
			'Article_Edit',
			array(),
			'GET',
			'',
			'Miao_Router_Exception' );

		$data[] = array(
			$config,
			Miao_Router_Rule::TYPE_VIEW,
			'Article_Edit',
			array(
				'id' => '123' ),
			'GET',
			'/article/edit/123' );

		$data[] = array(
			$config,
			Miao_Router_Rule::TYPE_ACTION,
			'Article_Edit',
			array(
				'id' => '123' ),
			'POST',
			'/article/edit/123' );

		$data[] = array(
			$config,
			Miao_Router_Rule::TYPE_ACTION,
			'Article_Edit',
			array(
				'id' => '123',
				'flag' => '1' ),
			'POST',
			'/article/edit/123?flag=1' );

		$config = array(
			'main' => 'Main',
			'defaultPrefix' => '',
			'error' => '404',
			'route' => array(
				array(
					'rule' => '/news/:page',
					'view' => 'News_List',
					'validator' => array(
						'type' => 'Regexp',
						'param' => 'page',
						'pattern' => 'p[0-9]+' ) ),
				array(
					'rule' => '/news',
					'view' => 'News_List',
					'validator' => array() ) ) );
		$data[] = array(
			$config,
			Miao_Router_Rule::TYPE_VIEW,
			'News_List',
			array(
				'page' => 'p1' ),
			'GET',
			'/news/p1' );

		$data[] = array(
			$config,
			Miao_Router_Rule::TYPE_VIEW,
			'News_List',
			array(),
			'GET',
			'/news' );

		return $data;
	}

	/**
	 * Вызов разных uri с одинаковым типом rule (View,Action,ViewBlock)
	 * @dataProvider dataProviderTestGetRouteWithSimilarRuleType
	 */
	public function testGetRouteWithSimilarRuleType( $config, $url, $actual, $exceptionName = '' )
	{
		if ( $exceptionName )
		{
			$this->setExpectedException( $exceptionName );
		}
		$router = Miao_Router::factory( $config );
		$expected = $router->route( $url );

		$this->assertEquals( $expected, $actual );
	}

	public function dataProviderTestGetRouteWithSimilarRuleType()
	{
		$data = array();

		$config = array(
			'main' => 'Main',
			'defaultPrefix' => '',
			'error' => '404',
			'route' => array(
				array(
					'rule' => '/news/:page',
					'view' => 'News_List',
					'validator' => array(
						'type' => 'Regexp',
						'param' => 'page',
						'pattern' => 'p[0-9]+' ) ),
				array(
					'rule' => '/news',
					'view' => 'News_List',
					'validator' => array() ) ) );

		$data[] = array(
			$config,
			'/news/123',
			array(),
			'Miao_Router_Exception' );

		$data[] = array(
			$config,
			'/news/p1',
			array(
				'_view' => 'News_List',
				'page' => 'p1' ) );

		$data[] = array(
			$config,
			'/news',
			array(
				'_view' => 'News_List' ) );

		return $data;
	}
}
