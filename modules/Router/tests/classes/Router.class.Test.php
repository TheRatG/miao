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

	public function testView()
	{
		$config = array(
			'main' => 'Main',
			'error' => '404',
			'defaultPrefix' => 'Test_Office',
			'rules' => array(
				array(
					'view' => 'Article_Item',
					'rule' => 'article/:id',
					'validator' => array( 'type' => 'numeric', 'param' => 'id' ) ) ) );

		$router = Miao_Router::factory( $config );
		$expected = $router->view( 'Article_Item', array( 'id' => '123' ) );
		$actual = 'article/123';

		$this->assertEquals( $expected, $actual );
	}

	public function atestConstruct()
	{
		$obj = new Miao_Router( 'Main', '404', array() );
		$this->assertTrue( $obj instanceof Miao_Router );
	}

	/**
	 * @dataProvider dataProviderTestGetRoute
	 */
	public function atestGetRoute( $config, $url, $actual )
	{
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
			'rules' => array(
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
			'rules' => array(
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

		$config = $config = array(
			'main' => 'Main',
			'defaultPrefix' => 'Daily_FrontOffice',
			'error' => '404',
			'rules' => array(
				array(
					'rule' => '/news/:section/:id',
					'view' => 'News_Item',
					'validator' => array(
						array(
							'type' => 'in',
							'param' => 'section',
							'values' => array( 'social', 'finance' ) ),
						array( 'type' => 'numeric', 'param' => 'id' ) ) ) ) );
		$data[] = array(
			$config,
			'/news/finance/123',
			array(
				'_view' => 'News_Item',
				'section' => 'finance',
				'id' => '123',
				'_prefix' => 'Daily_FrontOffice' ) );

		return $data;
	}
}