<?php
namespace Miao\Config;

class BaseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider providerTestGet
     *
     * @param $path
     * @param $actual
     * @param $initData
     * @param string $exceptionName
     */
    public function testGet( $path, $actual, $initData, $exceptionName = '' )
    {
        if ( !empty( $exceptionName ) )
        {
            $this->setExpectedException( $exceptionName );
        }

        $config = new Base( $initData );
        $expected = $config->get( $path );

        $this->assertEquals( $expected, $actual );
    }

    /**
     * @codeCoverageIgnore
     */
    public function providerTestGet()
    {
        $data = array();

        $config = array(
            'db' => array(
                'server' => 'test.server',
                'login' => 'test-user',
                'password' => 'test-password' ) );

        $data[] = array( 'db.login', 'test-user', $config );
        $data[] = array( 'db', $config[ 'db' ], $config, '' );
        $data[] = array( '.', $config, $config, '' );

        $exceptionName = '\\Miao\\Config\\Exception\\InvalidPath';
        $data[] = array( '', null, $config, $exceptionName );
        $data[] = array( '.', $config, $config );
        $data[] = array( '..', null, $config, $exceptionName );
        $data[] = array( '...', null, $config, $exceptionName );
        $data[] = array( '.db.password.', null, $config, $exceptionName );
        $data[] = array( '.db.password..', null, $config, $exceptionName );
        $data[] = array( '.db..password', null, $config, $exceptionName );

        $exceptionName = '\\Miao\\Config\\Exception\\PathNotFound';
        $data[] = array( 'password', null, $config, $exceptionName );
        $data[] = array( 'asd', null, $config, $exceptionName );
        $data[] = array( 'db.type', null, $config, $exceptionName );
        $data[] = array( 'db.asd.password', null, $config, $exceptionName );
        $data[] = array( 'lo1gin', null, $config, $exceptionName );
        $data[] = array( 'db.server.wrong-key', null, $config, $exceptionName );

        return $data;
    }
}