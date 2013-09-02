<?php
/**
 * User: vpak
 * Date: 02.09.13
 * Time: 15:43
 */

namespace Miao\Path;

class ResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Resolver
     */
    private $_path;

    /**
     * @codeCoverageIgnore
     */
    public function setUp()
    {
        $map = array(
            'project_root' => '/www/rbc',
            'libs' => array(
                array(
                    'name' => 'PHPUnit',
                    'path' => '/www/rbc/libs/phpunit/3.6',
                    'plugin' => 'PHPUnit',
                ),
                array(
                    'name' => 'Miao',
                    'path' => '/www/rbc/libs/miao/master',
                    'plugin' => 'Standart',
                ),
                array(
                    'name' => 'Rbc',
                    'path' => '/www/rbc/libs/rbc/default',
                    'plugin' => 'Standart',
                ),
                array(
                    'name' => 'PHPMailer',
                    'path' => '/www/rbc/libs/phpmailer/5.2.1',
                    'plugin' => 'IncludePath',
                ),
            ),
        );
        $this->_path = Resolver::factory( $map );
    }

    /**
     * @codeCoverageIgnore
     */
    public function tearDown()
    {
        unset( $this->_path );
    }

    /**
     * @dataProvider providerTestFactory
     * @param $map
     * @param string $exceptionName
     */
    public function testFactory( $map, $exceptionName = '' )
    {
        if ( $exceptionName )
        {
            $this->setExpectedException( $exceptionName );
        }
        $path = Resolver::factory( $map );

        $this->assertInstanceOf( '\\Miao\\Path\\Resolver', $path );
    }

    /**
     * @codeCoverageIgnore
     */
    public function providerTestFactory()
    {
        $data = array();

        $exceptionName = '\\Miao\\Path\\Exception';
        $map = array(
            'libs' => array(
                array(
                    'name' => 'PHPUnit',
                    'path' => '/www/rbc/libs/phpunit/3.6',
                    'plugin' => 'PHPUnit',
                ),
            )
        );
        $data[ ] = array( $map, $exceptionName );
        $map = array(
            'project_root' => '/www/rbc',
            'libs' => array(
                array(
                    'path' => '/www/rbc/libs/phpunit/3.6',
                    'plugin' => 'PHPUnit',
                ),
            )
        );
        $data[ ] = array( $map, $exceptionName );

        $map = array(
            'project_root' => '/www/rbc',
            'libs' => array(
                array(
                    'name' => 'PHPUnit',
                    'path' => '/www/rbc/libs/phpunit/3.6',
                    'plugin' => 'PHPUnit',
                ),
            )
        );
        $data[ ] = array( $map );

        return $data;
    }

    /**
     * @dataProvider providerTestGetRootDir
     * @param $string
     * @param $expected
     * @param string $exceptionName
     */
    public function testGetRootDir( $string, $expected, $exceptionName = '' )
    {
        if ( $exceptionName )
        {
            $this->setExpectedException( $exceptionName );
        }
        $path = $this->_path;

        $actual = $path->getRootDir( $string );
        $this->assertEquals( $expected, $actual );
    }

    /**
     * @codeCoverageIgnore
     */
    public function providerTestGetRootDir()
    {
        $data = array();

        $data[ ] = array( '', '/www/rbc' );
        $data[ ] = array( 'Miao', '/www/rbc/libs/miao/master' );
        $data[ ] = array( 'UnknownLib', '', '\Miao\Path\Exception' );
        $data[ ] = array( 'Rbc\Cache', '/www/rbc/libs/rbc/default' );

        return $data;
    }

    /**
     * @dataProvider providerTestGetModuleDir
     * @param $string
     * @param $expected
     * @param string $exceptionName
     */
    public function testGetModuleDir( $string, $expected, $exceptionName = '' )
    {
        if ( $exceptionName )
        {
            $this->setExpectedException( $exceptionName );
        }
        $path = $this->_path;

        $actual = $path->getModuleDir( $string );
        $this->assertEquals( $expected, $actual );
    }

    /**
     * @codeCoverageIgnore
     */
    public function providerTestGetModuleDir()
    {
        $data = array();

        $data[ ] = array( 'Miao\\Autoload', '/www/rbc/libs/miao/master/modules/Autoload' );
        $data[ ] = array( 'Miao\\Autoload\\Plugin', '/www/rbc/libs/miao/master/modules/Autoload' );

        $data[ ] = array( '', '', '\\Miao\\Path\\Exception' );

        return $data;
    }

    /**
     * @dataProvider providerTestGetTestSourcesDir
     * @param $string
     * @param $expected
     * @param string $exceptionName
     */
    public function testGetTestSourcesDir( $string, $expected, $exceptionName = '' )
    {
        if ( $exceptionName )
        {
            $this->setExpectedException( $exceptionName );
        }
        $path = $this->_path;
        $actual = $path->getTestSourcesDir( $string );
        $this->assertEquals( $expected, $actual );
    }

    /**
     * @codeCoverageIgnore
     */
    public function providerTestGetTestSourcesDir()
    {
        $data = array();

        $data[ ] = array( '', '', '\\Miao\\Path\\Exception' );
        $data[ ] = array(
            'Miao_Office_View_Test::setUp',
            '/www/rbc/libs/miao/master/modules/Office/tests/sources/View/setUp'
        );

        $data[ ] = array(
            '\Miao\Office\View\Test::setUp',
            '/www/rbc/libs/miao/master/modules/Office/tests/sources/View/Test/setUp'
        );
        $data[ ] = array(
            'Miao\Office\ViewTest::setUp',
            '/www/rbc/libs/miao/master/modules/Office/tests/sources/ViewTest/setUp'
        );
        $data[ ] = array(
            'Miao\Office\ViewTest',
            '/www/rbc/libs/miao/master/modules/Office/tests/sources/ViewTest'
        );

        $data[ ] = array(
            'Miao\Office\ViewTest::providerTestGetTestSourcesDir',
            '/www/rbc/libs/miao/master/modules/Office/tests/sources/ViewTest/testGetTestSourcesDir'
        );
        return $data;
    }

    /**
     * @dataProvider providerTestGetTemplateDir
     * @param $className
     * @param $expected
     * @param $exceptionName
     */
    public function testGetTemplateDir( $className, $expected, $exceptionName = '' )
    {
        if ( $exceptionName )
        {
            $this->setExpectedException( $exceptionName );
        }

        $actual = $this->_path->getTemplateDir( $className );
        $this->assertEquals( $expected, $actual );
    }

    /**
     * @codeCoverageIgnore
     * @return array
     */
    public function providerTestGetTemplateDir()
    {
        $data = array();

        $data[ ] = array( 'Miao_TestOffice', '/www/rbc/libs/miao/master/modules/TestOffice/templates' );
        $data[ ] = array(
            'Miao_TestOffice_View_Article_Item',
            '/www/rbc/libs/miao/master/modules/TestOffice/templates/View'
        );
        $data[ ] = array( 'Miao_TestOffice_View_Main', '/www/rbc/libs/miao/master/modules/TestOffice/templates/View' );
        $data[ ] = array(
            'Miao_TestOffice_ViewBlock_Article_Item',
            '/www/rbc/libs/miao/master/modules/TestOffice/templates/ViewBlock/Article/Item'
        );
        $data[ ] = array(
            'Miao_Console_Generator',
            '/www/rbc/libs/miao/master/modules/Console/templates/Generator'
        );
        return $data;
    }
}