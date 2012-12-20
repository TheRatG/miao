<?php
class Miao_OfficeOffice_DataHelper_Url_Test extends PHPUnit_Framework_TestCase
{
	protected $_moduleRoot;

	public function setUp()
	{
		$path = Miao_Path::getInstance();
		$this->_path = $path;

		$sourceDir = Miao_PHPUnit::getSourceFolder(
			'Miao_Office_TestOffice_Test' );
		$moduleRoot = $path->getModuleRoot( 'Miao_TestOffice' );
		Miao_PHPUnit::copyr( $sourceDir, $moduleRoot );

		$this->_moduleRoot = $moduleRoot;
	}

	public function tearDown()
	{
		Miao_PHPUnit::rmdirr( $this->_moduleRoot );
	}

	/**
	 *
	 * @dataProvider providerTestGet
	 *
	 */
	public function testGet( $instanceNum, $exceptionName )
	{
		if ( $exceptionName )
		{
			$this->setExpectedException( $exceptionName );
		}

		$className = 'Miao_TestOffice_DataHelper_Url_Instance' . $instanceNum;
		$h = $className::getInstance();
	}

	public function providerTestGet()
	{
		$data = array();

		$exceptionName = 'Miao_Office_DataHelper_Url_Exception';
		$data[] = array( 1, $exceptionName );
		$data[] = array( 2, $exceptionName );
		$data[] = array( 3, $exceptionName );
		$data[] = array( 4, '' );

		return $data;
	}

	/**
	 *
	 * @dataProvider providerTestSrc
	 *
	 */
	public function testSrc( $picsHost, $path, $query, $actual, $exceptionName = '' )
	{
		if ( $exceptionName )
		{
			$this->setExpectedException( $exceptionName );
		}

		$obj = Miao_TestOffice_DataHelper_Url_Rbc::getInstance();
		$obj->setPics( $picsHost );
		$expected = $obj->src( $path, $query );

		$this->assertEquals( $expected, $actual );
	}

	public function providerTestSrc()
	{
		$data = array();

		$picsHost = 'http://pics.rbc.ru/images';
		$data[] = array( $picsHost, 'image.gif', '', $picsHost . '/image.gif' );
		$data[] = array(
			$picsHost,
			'jslib/jquery.js',
			'',
			$picsHost . '/jslib/jquery.js' );
		$data[] = array(
			$picsHost,
			'skin/reset.css',
			'',
			$picsHost . '/skin/reset.css' );

		$picsHost = 'http://pics.rbc.ru/';
		$data[] = array(
			$picsHost,
			'skin/reset.css',
			'',
			$picsHost . '/skin/reset.css' );

		return $data;
	}

	/**
	 *
	 * @dataProvider providerTestHref
	 */
	public function testHref( $host, $path, $query = '', $fragment = '', $actual, $exceptionName = '' )
	{
		if ( $exceptionName )
		{
			$this->setExpectedException( $exceptionName );
		}

		$obj = Miao_TestOffice_DataHelper_Url_Rbc::getInstance();
		$obj->setHost( $host );
		$expected = $obj->href( $path, $query, $fragment );

		$this->assertEquals( $expected, $actual );
	}

	public function providerTestHref()
	{
		$data = array();

		$host = 'rbcdaily.ru';
		$data[] = array( $host, 'news', '', '', 'http://rbcdaily.ru/news' );

		$host = 'http://rbcdaily.ru';
		$data[] = array( $host, 'news', '', '', $host . '/news' );

		$data[] = array(
			$host,
			'news',
			'page=1',
			'#content',
			'http://rbcdaily.ru/news?page=1#content' );
		$data[] = array(
			$host,
			'news',
			array( 'page' => 1 ),
			'#content',
			'http://rbcdaily.ru/news?page=1#content' );

		$host = 'http://rbcdaily.ru/news/1234.html?type=forum';
		$data[] = array(
			$host,
			'',
			array( 'page' => 1 ),
			'#content',
			'http://rbcdaily.ru/news/1234.html?type=forum&page=1#content' );
		$host = 'http://rbcdaily.ru/news/1234.html?type=forum&page=2';
		$data[] = array(
			$host,
			'',
			array( 'page' => 1 ),
			'#content',
			'http://rbcdaily.ru/news/1234.html?type=forum&page=1#content' );

		$host = 'http://rbcdaily.ru/news/1234.html?type=forum&page=2#con2';
		$data[] = array(
			$host,
			'',
			array( 'page' => 1 ),
			'#content',
			'http://rbcdaily.ru/news/1234.html?type=forum&page=1#content' );

		$host = 'http://rbcdaily.ru/news/1234.html?type=forum&page=2#content';
		$data[] = array(
			$host,
			'',
			array( 'page' => 1 ),
			'',
			'http://rbcdaily.ru/news/1234.html?type=forum&page=1#content' );

		return $data;
	}
}