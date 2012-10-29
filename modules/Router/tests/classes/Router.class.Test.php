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
		$expected = call_user_func_array( array( $router, $funcName ), $param );
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
					'validator' => array( 'type' => 'numeric', 'param' => 'id' ) ) ) );

		$data[] = array(
			$config,
			'view',
			array( 'Article_Item', array( 'id' => '123' ) ),
			'/article/123' );
		$data[] = array(
			$config,
			'view',
			array( 'Article_Item', array( 'id' => '123', 'flag' => '1' ) ),
			'/article/123?flag=1' );
		$data[] = array(
			$config,
			'view',
			array(
				'Article_Item',
				array( 'id' => '123', 'flag' => '1', 'map' => 'google' ) ),
			'/article/123?flag=1&map=google' );

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
			'defaultPrefix' => '',
			'error' => '404',
			'route' => array(
				array(
					'rule' => '/news/:id',
					'view' => 'News_Item',
					'validator' => array( 'type' => 'numeric', 'param' => 'id' ) ) ) );
		$data[] = array(
			$config,
			'/news/123',
			array( '_view' => 'News_Item', 'id' => '123' ) );

		$config = array(
			'main' => 'Main',
			'defaultPrefix' => 'Daily_FrontOffice',
			'error' => '404',
			'route' => array(
				array(
					'rule' => '/news/:id',
					'view' => 'News_Item',
					'validator' => array( 'type' => 'numeric', 'param' => 'id' ) ) ) );
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
			array( '_view' => 'Main', '_prefix' => 'Daily_FrontOffice' ) );

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
						array( 'type' => 'numeric', 'param' => 'id' ) ) ) ) );
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

		$data[] = array( $config, '/2012/10/29', array(), 'Miao_Router_Exception' );

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
					'validator' => array( 'type' => 'numeric', 'param' => 'id' ) ) ) );
		$data[] = array(
			$config,
			'RewriteRule ^news/([0-9]+)$ index.php?id=$1&_view=News_Item [L]' );

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
						array( 'type' => 'numeric', 'param' => 'id2' ) ) ),

				array(
					'rule' => '/news/:section/:id',
					'view' => 'News_Item',
					'validator' => array(
						array(
							'type' => 'in',
							'param' => 'section',
							'variants' => 'social,finance' ),
						array( 'type' => 'numeric', 'param' => 'id' ) ) ),
				array(
					'rule' => '/news/:section/:id2',
					'view' => 'News_Bad_Item',
					'validator' => array(
						array(
							'type' => 'bad_validator',
							'param' => 'section',
							'variants' => 'social,finance' ),
						array( 'type' => 'numeric', 'param' => 'id2' ) ) ) ) );

		$data[] = array(
			$config,
			'RewriteRule ^news/(social|finance)$ index.php?section=$1&_view=News_List [L]' . "\n" . 'RewriteRule ^news/(social|finance)/([0-9]+)$ index.php?section=$1&id=$2&_view=News_Item [L]' . "\n" . '# error happened while generating rewrite for /news/:section/:id3' . "\n" . '# error happened while generating rewrite for /news/:section/:id2' );

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
					'validator' => array( 'type' => 'numeric', 'param' => 'id' ) ) ) );

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
					'validator' => array( 'type' => 'numeric', 'param' => 'id' ) ) ) );
		$data[] = array(
			$config,
			'rewrite "^/?news/([0-9]+)$" /index.php?id=$1&_view=News_Item break;' );

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
						array( 'type' => 'numeric', 'param' => 'id2' ) ) ),

				array(
					'rule' => '/news/:section/:id',
					'view' => 'News_Item',
					'validator' => array(
						array(
							'type' => 'in',
							'param' => 'section',
							'variants' => 'social,finance' ),
						array( 'type' => 'numeric', 'param' => 'id' ) ) ),
				array(
					'rule' => '/news/:section/:id2',
					'view' => 'News_Bad_Item',
					'validator' => array(
						array(
							'type' => 'bad_validator',
							'param' => 'section',
							'variants' => 'social,finance' ),
						array( 'type' => 'numeric', 'param' => 'id2' ) ) ),

				array(
					'rule' => '/news/:p1/:p2/:p3/:p4/:p5/:p6/:p7/:p8/:p9/:p10',
					'view' => 'Many_Params',
					'validator' => array() ) ) );

		$data[] = array(
			$config,
			'rewrite "^/?news/(social|finance)$" /index.php?section=$1&_view=News_List break;' . "\n" . 'rewrite "^/?news/(social|finance)/([0-9]+)$" /index.php?section=$1&id=$2&_view=News_Item break;' . "\n" . '# error happened while generating rewrite for /news/:p1/:p2/:p3/:p4/:p5/:p6/:p7/:p8/:p9/:p10 (too many params)' . "\n" . '# error happened while generating rewrite for /news/:section/:id3' . "\n" . '# error happened while generating rewrite for /news/:section/:id2' );

		return $data;
	}
}
