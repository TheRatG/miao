<?php
/**
 * @author vpak
 * @date 2013-09-04 11:26:57
 */

namespace Miao\Office;

use Miao\App;

class IndexTest extends \PHPUnit_Framework_TestCase
{
    static protected $_testOfficeDir;

    static public function setUpBeforeClass()
    {
        $moduleDir = \Miao\App::getInstance()->getPath()->getModuleDir( __CLASS__ );
        $sourceTestOfficeDir = $moduleDir . '/tests/source/TestOffice';

        self::$_testOfficeDir = dirname( $moduleDir ) . DIRECTORY_SEPARATOR . 'TestOffice';

        if ( file_exists( self::$_testOfficeDir ) )
        {
            \Miao\Path\Helper::removeDir( self::$_testOfficeDir );
        }
        \Miao\Path\Helper::copyr( $sourceTestOfficeDir, self::$_testOfficeDir );
    }

    static public function tearDownAfterClass()
    {
        \Miao\Path\Helper::removeDir( self::$_testOfficeDir );
    }

    public function getFactory()
    {
        $factory = new \Miao\Office\Factory( '\\Miao\\TestOffice' );
        return $factory;
    }

    /**
     * @dataProvider providerTestGetController
     * @param Factory $factory
     * @param array $params
     * @param $expected
     */
    public function testGetController( \Miao\Office\Factory $factory, array $params, $expected )
    {
        $office = $factory->getOffice( $params );
        $actual = $office->getController();

        $this->assertInstanceOf( $expected, $actual );
    }

    /**
     * @codeCoverageIgnore
     * @return array
     */
    public function providerTestGetController()
    {
        $data = array();

        $factory = $this->getFactory();
        $factory->setDefaultParams( array( $factory->getViewRequestName() => 'Main' ) );
        $params = array( $factory->getViewRequestName() => 'Main' );
        $data[ ] = array( $factory, $params, '\\Miao\\TestOffice\\View\\Main' );

        $factory->setDefaultParams( array( $factory->getViewRequestName() => 'Main' ) );
        $factory->setDefaultPrefix('\\Miao\\TestOffice');
        $params = array( $factory->getViewRequestName() => 'Article' );
        $data[ ] = array( $factory, $params, '\\Miao\\TestOffice\\View\\Article' );

        return $data;
    }
}