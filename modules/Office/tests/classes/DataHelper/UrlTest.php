<?php
/**
 * @author vpak
 * @date 2013-09-13 10:08:39
 */

namespace Miao\Office\DataHelper;

class UrlTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
    }

    public function tearDown()
    {
    }

    /**
     * @dataProvider providerTestHref
     * @param $url
     * @param $path
     * @param string $query
     * @param string $fragment
     * @param $actual
     * @param string $exceptionName
     * @internal param $host
     */
    public function testBuild( $url, $path, $query = '', $fragment = '', $actual, $exceptionName = '' )
    {
        if ( $exceptionName )
        {
            $this->setExpectedException( $exceptionName );
        }

        $expected = \Miao\Office\DataHelper\Url::build( $url, $path, $query, $fragment );
        $this->assertEquals( $expected, $actual );
    }

    /**
     * @codeCoverageIgnore
     * @return array
     */
    public function providerTestHref()
    {
        $data = array();

        $host = 'rbcdaily.ru';
        $data[ ] = array( $host, 'news', '', '', 'http://rbcdaily.ru/news' );

        $host = 'http://rbcdaily.ru';
        $data[ ] = array( $host, 'news', '', '', $host . '/news' );

        $data[ ] = array(
            $host,
            'news',
            'page=1',
            '#content',
            'http://rbcdaily.ru/news?page=1#content'
        );
        $data[ ] = array(
            $host,
            'news',
            array( 'page' => 1 ),
            '#content',
            'http://rbcdaily.ru/news?page=1#content'
        );

        $host = 'http://rbcdaily.ru/news/1234.html?type=forum';
        $data[ ] = array(
            $host,
            '',
            array( 'page' => 1 ),
            '#content',
            'http://rbcdaily.ru/news/1234.html?type=forum&page=1#content'
        );
        $host = 'http://rbcdaily.ru/news/1234.html?type=forum&page=2';
        $data[ ] = array(
            $host,
            '',
            array( 'page' => 1 ),
            '#content',
            'http://rbcdaily.ru/news/1234.html?type=forum&page=1#content'
        );

        $host = 'http://rbcdaily.ru/news/1234.html?type=forum&page=2#con2';
        $data[ ] = array(
            $host,
            '',
            array( 'page' => 1 ),
            '#content',
            'http://rbcdaily.ru/news/1234.html?type=forum&page=1#content'
        );

        $host = 'http://rbcdaily.ru/news/1234.html?type=forum&page=2#content';
        $data[ ] = array(
            $host,
            '',
            array( 'page' => 1 ),
            '',
            'http://rbcdaily.ru/news/1234.html?type=forum&page=1#content'
        );

        return $data;
    }
}