<?php
/**
 * User: vpak
 * Date: 05.09.13
 * Time: 15:11
 */

namespace Miao\Office\Controller;

class ViewBlockTest extends \PHPUnit_Framework_TestCase
{
    static protected $_testOfficeDir;

    static public function setUpBeforeClass()
    {
        $moduleDir = \Miao\App::getInstance()
            ->getPath()
            ->getModuleDir( __CLASS__ );
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

    public function testGenerateContentSimple()
    {
        $viewBlock = new \Miao\TestOffice\ViewBlock\Article\Item();
        $actual = $viewBlock->generateContent();

        $expected = 'file:ViewBlock/Article/Item/index.tpl';
        $this->assertEquals( $expected, $actual );
    }

    public function testParams()
    {
        $viewBlock = new \Miao\TestOffice\ViewBlock\Offer( null );
        $viewBlock->setParams( array( 'number' => 1 ) );
        $actual = $viewBlock->generateContent();
        $expected = 'file:ViewBlock/Offer/index.tpl 1';
        $this->assertEquals( $expected, $actual );

        $viewBlock->setParams( array( 'number' => 2 ) );
        $expected = 'file:ViewBlock/Offer/index.tpl 1';
        $this->assertEquals( $expected, $actual );
    }
}